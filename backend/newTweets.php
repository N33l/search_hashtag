<?php
	$hash = $_GET['q'] ?? false;
	$lastId = $_GET['lastId'] ?? false;
	if(empty($hash) || empty($lastId)) {
		echo '[]';
	} else {
		require_once('../config/DBConfig.php');
		require_once('DBOperations.php');
		require_once('../util/DBConnection.php');

		$hash = trim($hash, '# ');
		$dbObject=new DBOperations(new DBConnection(HOST_NAME,DB_NAME,USER_NAME,PASSWORD));
		$tweets = $dbObject->getTweetsByHash($hash, $lastId, true);
		echo json_encode($tweets);
	}
?>