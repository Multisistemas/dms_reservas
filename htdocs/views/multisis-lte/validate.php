<?php
include("../../inc/inc.Settings.php");
include("../../inc/inc.LogInit.php");
include("../../inc/inc.Utils.php");
include("../../inc/inc.Language.php");
include("../../inc/inc.Init.php");
include("../../inc/inc.DBInit.php");
include("../../inc/inc.ClassUI.php");
include("../../inc/inc.Authentication.php");

if (isset($_POST["command"])) {
	//$target_dir = $settings->_rootDir."/views/multisis-lte/images/";
	switch ($_POST["command"]) {

	case 'validatelogo':

		$target_dir =$settings->_rootDir."/images/multisis-lte/";
		$target_file = $target_dir . basename($_FILES["logofile"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["logofile"]["tmp_name"]);
		    if($check !== false) {
		        $uploadOk = 1;
		    } else {
		      $session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_error')));
				  header("Location:../../out/out.ViewFolder.php?folderid=1");
				  die();
		    }
		}

		// Check if file already exists
		/*if (file_exists($target_file)) {
		    echo "Sorry, file already exists.";
		    $uploadOk = 0;
		}*/

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
			$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_ext')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  die();
		}

		// Check file size
		if ($_FILES["logofile"]["size"] > 500000) {
			$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_too_large')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  die();
		}

		// If everything is ok, try to upload file
		if (move_uploaded_file($_FILES["logofile"]["tmp_name"], $target_file)) {
		  rename($target_file, $target_dir."logo.png");
		  clearstatcache();
		  $session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('img_success')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  
		} else {
			$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_error')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  die();
		}

	break;

	case 'validatebrand':

		$target_dir =$settings->_rootDir."/images/multisis-lte/";
		$target_file = $target_dir . basename($_FILES["brandfile"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["brandfile"]["tmp_name"]);
		    if($check !== false) {
		        $uploadOk = 1;
		    } else {
					$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_error')));
				  header("Location:../../out/out.ViewFolder.php?folderid=1");
				  die();
		    }
		}

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
		  $session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_ext')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  die();
		}

		// Check file size
		if ($_FILES["brandfile"]["size"] > 500000) {
		  $session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_too_large')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  die();
		}

		// If everything is ok, try to upload file
		if (move_uploaded_file($_FILES["brandfile"]["tmp_name"], $target_file)) {
		  rename($target_file, $target_dir."brand.png");
		  clearstatcache();
		  $session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('img_success')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  
		} else {
		  $session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('img_validate_error')));
		  header("Location:../../out/out.ViewFolder.php?folderid=1");
		  die();
		}

	break;
	}	
}

/**
 * Check if the $_FILES[][name] is a valid name
 *
 * @param (string) $filename - Uploaded file name.
 */
function check_file_uploaded_name($filename) {
	return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$filename)) ? true : false);
}

/**
 * Check $_FILES[][name] length.
 *
 * @param (string) $filename - Uploaded file name.
 */
function check_file_uploaded_length($filename) {
  return (bool) ((mb_strlen($filename,"UTF-8") > 225) ? true : false);
}

/**
 * Returns the file extension.
 *
 * @param file - Uploaded file.
 */
function pathinfo_extension($file) {
	if (defined('PATHINFO_EXTENSION')) {
		return pathinfo($file,PATHINFO_EXTENSION);
	}
}

?>