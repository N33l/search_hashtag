<?php
/**
 * Created by PhpStorm.
 * User: neel
 * Date: 12/19/16
 * Time: 8:40 PM
 */

ini_set('display_errors', 1);
require_once(dirname(__DIR__).'/util/TwitterAPIHelper.php');
require_once(dirname(__DIR__).'/config/TwitterConfig.php');
require_once(dirname(__DIR__).'/config/DBConfig.php');

require_once(dirname(__DIR__).'/backend/DBOperations.php');
require_once(dirname(__DIR__).'/util/DBConnection.php');
/** Set access tokens here - see: https://dev.twitter.com/apps/ **/


function hitTwitterApi(){

    $settings = array(
        'oauth_access_token' => OAUTH_ACCESS_TOKEN,
        'oauth_access_token_secret' => OAUTH_ACCESS_TOKEN_SECRET,
        'consumer_key' => CONSUMER_KEY,
        'consumer_secret' =>CONSUMER_SECRET
    );

    $dbObject=new DBOperations(new DBConnection(HOST_NAME,DB_NAME,USER_NAME,PASSWORD));
    $maxTweetId=$dbObject->getMaxTweetId();


    $inputData='london';

    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $getfield = '?q=#'.$inputData.'&since_id='.$maxTweetId;
    $requestMethod = 'GET';
    $twitter = new TwitterAPIHelper($settings);
    $result=$twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();

//    $result='{ "errors": [ { "code": 88, "message": "Rate limit exceeded" } ] } ';

    $result = json_decode($result, true);
    var_dump('result',$result);

    if(!isset($result['errors'])){
        foreach($result['statuses'] as $tweet) {
            // insert in Tweet Model
            $tweetData = ['tweet_id' => $tweet['id_str'], 'created_at' => date("Y-m-d H:i:s", strtotime($tweet['created_at'])), 'tweet' => utf8_encode($tweet['text'])];
            $tweetId=$dbObject->insertIntoTweetTable($tweetData,'tweet');
            // Insert in hashtag model
            $hashTags = array_column($tweet['entities']['hashtags'], 'text');
            $dbObject->insertIntoHashTagTable($hashTags,$tweetId,'hash_tag');
        }
    }else{
        //if rate limit exceeds then sleep for 5 mins
        if($result['errors'][0]['code']== 88)
        {
            // sleep for 5 mins
            sleep(300);
        }

    }
}
hitTwitterApi();



