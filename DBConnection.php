<?php
/**
 * Created by PhpStorm.
 * User: neel
 * Date: 12/19/16
 * Time: 9:54 PM
 */

class DBConnection{

    private $hostName;
    private $DBName;
    private $userName;
    private $password;

    public function __construct($hostName,$DBName,$userName,$password)
    {
        $this->hostName=$hostName;
        $this->DBName=$DBName;
        $this->userName=$userName;
        $this->password=$password;
    }

    public function makeConnection(){

        try {
//            return new PDO('mysql:host=localhost;dbname=someDb', $username, $password);
            return new PDO('mysql:host='.$this->hostName.';dbname='.$this->DBName, $this->userName, $this->password);;
        } catch (PDOException $ex) {

        }

    }


}