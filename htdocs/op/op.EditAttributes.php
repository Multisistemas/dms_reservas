<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//    Copyright (C) 2010-2016 Uwe Steinmann
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../inc/inc.Settings.php");
include("../inc/inc.LogInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.Utils.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

/* Check if the form data comes from a trusted request */
if(!checkFormKey('editattributes')) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_request_token"))),getMLText("invalid_request_token"));
}

if (!isset($_POST["documentid"]) || !is_numeric($_POST["documentid"]) || intval($_POST["documentid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$documentid = $_POST["documentid"];
$document = $dms->getDocument($documentid);

if (!is_object($document)) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$folder = $document->getFolder();
$docPathHTML = getFolderPathHTML($folder, true). " / <a href=\"../out/out.ViewDocument.php?documentid=".$documentid."\">".$document->getName()."</a>";
$docaccess = $document->getAccessMode($user);

if ($docaccess < M_READWRITE) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

$versionid = $_POST["version"];
$version = $document->getContentByVersion($versionid);

if (!is_object($version)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
}

if ($version->_mimeType != $_FILES["filename"]["type"][0]){
    UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("no_equal_file_extension"));
}

$target_dir = $folder->_dms->contentDir.$documentid."/";

$target_file = $target_dir . basename($_FILES["filename"]["name"][0]);

$upload_result = move_uploaded_file($_FILES["filename"]["tmp_name"][0], $target_file);

$change_name = rename($target_file, $target_dir.$version->getVersion().$version->_fileType);
//$result = move_uploaded_file($thename = $_POST["userfile"][0], $file_folder.$version->getVersion().$version->_fileType);

//$document->setComment($_POST['version_comment']);
$comment = $_POST['version_comment'];
$version->setComment($comment);

/*if (($oldcomment = $version->getComment()) != $comment) {
	$version->setComment($comment);
}*/
$workflow = $dms->getWorkflow();

// Send notifications to reviewers

$latestContent = $document->getLatestContent();
$workflow = $latestContent->getWorkflow();

if($notifier) {

	$notifyList = $document->getNotifyList();
	$subject = "attribute_changed_email_subject";
	$message = "attribute_changed_email_body";
	$params = array();
	$params['name'] = $document->getName();
	$params['version'] = $version->getVersion();
	$params['comment'] = $comment;
	$params['folder_path'] = $folder->getFolderPathPlain();
	$params['username'] = $user->getFullName();
	$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$version->getVersion();

	if($workflow && $settings->_enableNotificationWorkflow) {
		foreach($workflow->getNextTransitions($workflow->getInitState()) as $ntransition) {
			foreach($ntransition->getUsers() as $tuser) {
				$notifier->toIndividual($user, $tuser->getUser(), $subject, $message, $params);
			}
			foreach($ntransition->getGroups() as $tuser) {
				$notifier->toGroup($user, $tuser->getGroup(), $subject, $message, $params);
			}
		}
	}
}

//$queryStr = "UPDATE tbl SET modified = " . $db->getCurrentTimestamp() . ", modifiedBy = " . $user->getID() . ", name = " . $db->qstr($name) . " WHERE id = ". (int) $id;
//$ret = $db->getResult($queryStr);

//$new_file = rename($_POST["userfile"]["name"][0], $version->getVersion().$version->_fileType);

/*if($attributes) {
	$oldattributes = $version->getAttributes();
	foreach($attributes as $attrdefid=>$attribute) {
		$attrdef = $dms->getAttributeDefinition($attrdefid);
		if($attribute) {
			if(!$attrdef->validate($attribute)) {
				$errmsg = getAttributeValidationText($attrdef->getValidationError(), $attrdef->getName(), $attribute);
				UI::exitError(getMLText("document_title", array("documentname" => $document->getName())), $errmsg);
			}
			if(!isset($oldattributes[$attrdefid]) || $attribute != $oldattributes[$attrdefid]->getValue()) {
				if(!$version->setAttributeValue($dms->getAttributeDefinition($attrdefid), $attribute)) {
					UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
				} else {
					if($notifier) {
						$notifyList = $document->getNotifyList();
						$subject = "attribute_changed_email_subject";
						$message = "attribute_changed_email_body";
						$params = array();
						$params['name'] = $document->getName();
						$params['version'] = $version->getVersion();
						$params['attribute'] = $attribute;
						$params['folder_path'] = $folder->getFolderPathPlain();
						$params['username'] = $user->getFullName();
						$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$version->getVersion();
						$params['sitename'] = $settings->_siteName;
						$params['http_root'] = $settings->_httpRoot;

						$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
						foreach ($notifyList["groups"] as $grp) {
							$notifier->toGroup($user, $grp, $subject, $message, $params);
						}
						// if user is not owner send notification to owner
//						if ($user->getID() != $document->getOwner()->getID()) 
//							$notifier->toIndividual($user, $document->getOwner(), $subject, $message, $params);

					}
				}
			}
		} elseif($attrdef->getMinValues() > 0) {
			UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("attr_min_values", array("attrname"=>$attrdef->getName())));
		} elseif(isset($oldattributes[$attrdefid])) {
			if(!$version->removeAttribute($dms->getAttributeDefinition($attrdefid)))
				UI::exitError(getMLText("document_title", array("documentname" => $folder->getName())),getMLText("error_occured"));
		}
	}
}*/

add_log_line("?documentid=".$documentid);

header("Location:../out/out.DocumentVersionDetail.php?documentid=".$documentid."&version=".$versionid);

?>
