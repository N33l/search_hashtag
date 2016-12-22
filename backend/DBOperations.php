<?php
/**
 * Created by PhpStorm.
 * User: neel
 * Date: 12/19/16
 * Time: 10:12 PM
 */
require_once(dirname(__DIR__).'/util/DBConnection.php');

class DBOperations{

    private $connection;
    private $tweetColumns=[
        'tweet_id',
        'created_at',
        'tweet'
    ];
    private $hashColumns=[
        'hash_tag'
    ];
    private $mapperColumns=[
        'hash_id',
        'tweet_id'
    ];
    public function __construct(DBConnection $db)
    {
        $this->connection=$db->makeConnection();
    }

    private function insertHelper($tableName,$data,$columnNames){

        $rowVars = '(' . implode(', ', array_fill(0, count($columnNames), '?')) . ')';
        $insertSql = "INSERT INTO $tableName (" . implode(', ', $columnNames) .
            ") VALUES " . $rowVars;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        try {
            $insertStatement = $this->connection->prepare($insertSql);
            if($insertStatement){
                $insertStatement->execute($data);
                $id=$this->connection->lastInsertId();
                return ($id);
            }
        } catch ( PDOException $e ) {
            echo $e->getMessage();
        }

    }

    public function insertIntoTweetTable($tweetData,$tableName){
        if ( !$this->isExists($tweetData['tweet_id'],'tweet','tweet_id') ) {
            $tweetId=$this->insertHelper($tableName,array_values($tweetData),$this->tweetColumns);
            return $tweetId;
        }else{
            return $this->fetchIdFromValue($tweetData['tweet_id'],'tweet','tweet_id');
        }
    }

    public function insertIntoHashTagTable($hashTagData,$tweetId,$tableName){
        foreach($hashTagData as $hashTag) {
            if ( !$this->isExists($hashTag, 'hash_tag', 'hash_tag') ) {
                $hashId=$this->insertHelper($tableName,[$hashTag],$this->hashColumns);

            }else{
                $hashId=$this->fetchIdFromValue($hashTag, 'hash_tag', 'hash_tag');
            }
            $this->insertHelper('hash_tag_tweet_mapper',[$hashId,$tweetId],$this->mapperColumns);
        }
    }

    public function getMaxTweetId(){
        $selectSql="SELECT MAX(`tweet_id`) as `max_val` FROM `tweet`";
        try {
            $stmt = $this->connection->prepare($selectSql);
            if($stmt){
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return ($result['max_val']);
            }
        } catch ( PDOException $e ) {
            echo $e->getMessage();
        }

    }

    private function fetchIdFromValue($value,$tableName,$columnName){
        $selectSql="SELECT `id` FROM $tableName WHERE $columnName =?";
        try {
            $stmt=$this->connection->prepare($selectSql);
            if($stmt){
                $stmt->execute([$value]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return ($result ['id']);
            }
        } catch ( PDOException $e ) {
            echo $e->getMessage();
        }
    }

    //    public function insertIntoDb($tableName,$columns,$values,$updateCols){
    //
    //        $dataToInsert=$this->formatData($values);
    //
    //        $rowVars = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
    //        $allPlaces = implode(', ', array_fill(0, count($values), $rowVars));
    //
    //        $updateColumns=implode(', ', $updateCols);
    //
    //        $insertSql="INSERT INTO $tableName (" . implode(', ', $columns) .
    //            ") VALUES " . $allPlaces . " ON DUPLICATE KEY UPDATE $updateColumns";
    //
    //        $insertStatement=$this->connection->prepare($insertSql);
    //        try {
    //            $insertStatement->execute($dataToInsert);
    //        } catch (PDOException $e){
    //            echo $e->getMessage();
    //        }
    //
    //
    //        //        $insertTweetColumns=[
    //        //            'tweet_id',
    //        //            'hash_tag',
    //        //            'user_id',
    //        //            'text'
    //        //        ];
    //        //
    //        //        $insertUserColumns=[
    //        //            'user_id',
    //        //            'user_name',
    //        //            'user_screen_name',
    //        //            'user_location',
    //        //            'user_description'
    //        //        ];
    //    }

    private function isExists($value,$tableName,$columnName){
        $countSql="SELECT COUNT(*) AS `total` FROM $tableName WHERE $columnName = ?";
        try {
            $insertStatement=$this->connection->prepare($countSql);
            if($insertStatement){
                $insertStatement->execute([$value]);
                $countObj=$insertStatement->fetchObject();
                if($countObj->total > 0)
                    return true;
                else
                    return false;
            }
        } catch (PDOException $e){
            echo $e->getMessage();
        }

    }

    public function noOfTweetsByHash($hash) {
        $sql = 'SELECT COUNT(`tweet_id`) AS cnt FROM `hash_tag_tweet_mapper` WHERE hash_id = (SELECT `id` FROM `hash_tag` WHERE hash_tag = "' . $hash .'")';
        try {
            $stmt = $this->connection->query($sql);
            if($stmt) {
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $tweets = $stmt->fetch();
                if(empty($tweets)) {
                    return 0;
                } else {
                    return $tweets['cnt'];
                }
            }
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getTweetsByHash($hash, $lastId = 0, $new = false, $size = 10) {
        $sql = 'SELECT `tweet_id` FROM `hash_tag_tweet_mapper` WHERE hash_id = (SELECT `id` FROM `hash_tag` WHERE hash_tag = "' . $hash .'")';
        try {
            $stmt = $this->connection->query($sql);
            if($stmt) {
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $tweetIds = array_column($stmt->fetchAll(), 'tweet_id');
                if(empty($tweetIds)) {
                    return [];
                } else {
                    $sql = 'SELECT `tweet`, `tweet_id` FROM `tweet` WHERE id IN ( ' . implode(',', $tweetIds) . ' )';
                    if($new) {
                        $sql .= ' AND `tweet_id` > ' . $lastId;
                    } else {
                        $sql .= ' AND `tweet_id` < ' . $lastId;
                    }
                    $sql .= ' ORDER BY `tweet_id` DESC LIMIT ' . $size;
                    $stmt = $this->connection->query($sql);
                    if($stmt) {
                        $stmt->setFetchMode(PDO::FETCH_ASSOC);
                        $tweets = $stmt->fetchAll();
                        return $tweets;
                    } else {
                        echo 'Something is wrong in ' . $sql . PHP_EOL;
                    }
                }
            } else {
                echo 'Something is wrong in ' . $sql . PHP_EOL;
            }
        } catch(PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function getTweets(){

    }
}