<?php
/**
 * Created by PhpStorm.
 * User: neel
 * Date: 12/19/16
 * Time: 8:40 PM
 */

ini_set('display_errors', 1);
require_once(dirname(__DIR__).'/util/RunCronHelper.php');


$shortopts = "";

$longopts = [
    "hash_tag:",
];
$options = getopt($shortopts, $longopts);
$hashTag = isset($options['hash_tag']) ? $options['hash_tag'] : null;

if($hashTag){
    $helperObj=new RunCronHelper($hashTag);
    $helperObj->hitTwitterApi();
}





