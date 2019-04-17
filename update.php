<?php
	// Enable error reporting
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include('config.inc');
	
	// Setup S3 access	
	require('S3.php');
	$s3 = new S3($s3_id, $s3_key);
	date_default_timezone_set('America/New_York');

	// Text manipulation function
	function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

	// Build array of images
	$fileSystemIterator = new FilesystemIterator('images');
	$entries = array();
	foreach ($fileSystemIterator as $fileInfo){
		$entries[] = $fileInfo->getFilename();
	}

	// Loop through array of images
	foreach ($entries as $entry){
		echo $entry."\n";
		$object_no = get_string_between($entry, 'DAACS_', '_Img');
		echo $object_no."\n";
	}
	
	
	// Upload to S3
	//$local_image = file_get_contents("images/762_40_47_DAACS_1722817_Img0206.jpg");
	//S3::putObject($local_image,'mtv-collectionsonline',"archeology/762_40_47_DAACS_1722817_Img0206.jpg",S3::ACL_PUBLIC_READ,array(),array(),S3::STORAGE_CLASS_RRS);

?>