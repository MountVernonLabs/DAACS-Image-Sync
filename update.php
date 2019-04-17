<?php
	// Enable error reporting
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require('config.inc');
	
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

	// Generate Image Derivatives
	foreach ($entries as $entry){
		echo "Resizing ".$entry."\n";
	
		$im = new imagick("images/".$entry);
		$im->setImageFormat('jpeg');
		$im->scaleImage(2000, 0);
		$im->writeImage("convert/".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->scaleImage(1175, 0);
		$im->writeImage("convert/xlrg_".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->scaleImage(705, 0);
		$im->writeImage("convert/lrg_".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->scaleImage(705, 0);
		$im->writeImage("convert/lrg_".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->scaleImage(575, 0);
		$im->writeImage("convert/med_".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->cropThumbnailImage(400,250);
		$im->writeImage("convert/sml_".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->cropThumbnailImage(250,230);
		$im->writeImage("convert/sqr_".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->cropThumbnailImage(175,120);
		$im->writeImage("convert/thumb_".pathinfo($entry, PATHINFO_FILENAME).".jpg");
		$im->clear();
		$im->destroy();		
		
	}

	// Upload Images to S3
	$fileSystemIterator = new FilesystemIterator('convert');
	$images = array();
	foreach ($fileSystemIterator as $fileInfo){
		$images[] = $fileInfo->getFilename();
	}
	foreach ($images as $image){
		echo "Uploading ".$image."\n";
		$local_image = file_get_contents($image);
		S3::putObject($local_image,'mtv-collectionsonline',"archeology/".$image,S3::ACL_PUBLIC_READ,array(),array(),S3::STORAGE_CLASS_RRS);
	}	
	



	// Loop through array of images
	foreach ($entries as $entry){
		echo $entry."\n";
		$object_no = get_string_between($entry, 'DAACS_', '_Img');
		echo $object_no."\n";
	}


	
	
	// Upload to S3

?>