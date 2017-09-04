<?php
/**
 * Implementation of DocumentVersionDetail view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");

/**
 * Class which outputs the html page for DocumentVersionDetail view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_DocumentVersionDetail extends SeedDMS_Bootstrap_Style {

	function preview() { /* {{{ */
		$document = $this->params['document'];
		$timeout = $this->params['timeout'];
		$showfullpreview = $this->params['showFullPreview'];
		$converttopdf = $this->params['convertToPdf'];
		$cachedir = $this->params['cachedir'];
		$version = $this->params['version'];
		if(!$showfullpreview)
			return;

		switch($version->getMimeType()) {
		case 'audio/mpeg':
		case 'audio/mp3':
		case 'audio/ogg':
		case 'audio/wav':
			$this->contentHeading(getMLText("preview"));
?>
		<audio controls style="width: 100%;">
		<source  src="../op/op.Download.php?documentid=<?php echo $document->getID(); ?>&version=<?php echo $version->getVersion(); ?>" type="audio/mpeg">
		</audio>
<?php
			break;
		case 'application/pdf':
			$this->contentHeading(getMLText("preview"));
?>
			<iframe src="../pdfviewer/web/viewer.html?file=<?php echo urlencode('../../op/op.Download.php?documentid='.$document->getID().'&version='.$version->getVersion()); ?>" width="100%" height="700px"></iframe>
<?php
			break;
		case 'image/svg+xml':
			$this->contentHeading(getMLText("preview"));
?>
			<img src="../op/op.Download.php?documentid=<?php echo $document->getID(); ?>&version=<?php echo $latestContent->getVersion(); ?>" width="100%">
<?php
			break;
		default:
			break;
		}
		if($converttopdf) {
			$pdfpreviewer = new SeedDMS_Preview_PdfPreviewer($cachedir, $timeout);
			if($pdfpreviewer->hasConverter($version->getMimeType())) {
				$this->contentHeading(getMLText("preview"));
?>
				<iframe src="../pdfviewer/web/viewer.html?file=<?php echo urlencode('../../op/op.PdfPreview.php?documentid='.$document->getID().'&version='.$version->getVersion()); ?>" width="100%" height="700px"></iframe>
<?php
			}
		}
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$version = $this->params['version'];
		$viewonlinefiletypes = $this->params['viewonlinefiletypes'];
		$enableversionmodification = $this->params['enableversionmodification'];
		$cachedir = $this->params['cachedir'];
		$previewwidthdetail = $this->params['previewWidthDetail'];
		$timeout = $this->params['timeout'];

		$latestContent = $document->getLatestContent();
		$status = $version->getStatus();
		$reviewStatus = $version->getReviewStatus();
		$approvalStatus = $version->getApprovalStatus();

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();
		echo $this->getDefaultFolderPathHTML($folder, true, $document);

		//// Atach file ////
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-12\">";

		$this->startBoxPrimary(getMLText("details"));

?>
<div class="row-fluid">
<div class="col-md-4">
<?php
		$this->contentHeading(getMLText("document_infos"));
		$this->contentContainerStart();
?>
<table class="table table-bordered table-condensed">
<tr>
<td><b><?php printMLText("owner");?>:</b></td>
<td>
<?php
		$owner = $document->getOwner();
		print "<a class=\"infos\" href=\"mailto:".$owner->getEmail()."\">".htmlspecialchars($owner->getFullName())."</a>";
?>
</td>
</tr>
<?php
		if($document->getComment()) {
?>
<tr>
<td><b><?php printMLText("comment");?>:</b></td>
<td><?php print htmlspecialchars($document->getComment());?></td>
</tr>
<?php
		}
?>
<tr>
<td><b><?php printMLText("used_discspace");?>:</b></td>
<td><?php print SeedDMS_Core_File::format_filesize($document->getUsedDiskSpace());?></td>
</tr>
<tr>
<tr>
<td><b><?php printMLText("creation_date");?>:</b></td>
<td><?php print getLongReadableDate($document->getDate()); ?></td>
</tr>
<?php
		if($document->expires()) {
?>
		<tr>
		<td><b><?php printMLText("expires");?>:</b></td>
		<td><?php print getReadableDate($document->getExpires()); ?></td>
		</tr>
<?php
		}
		if($document->getKeywords()) {
?>
<tr>
<td><b><?php printMLText("keywords");?>:</b></td>
<td><?php print htmlspecialchars($document->getKeywords());?></td>
</tr>
<?php
		}
		if ($document->isLocked()) {
			$lockingUser = $document->getLockingUser();
?>
<tr>
	<td><b><?php printMLText("lock_status");?>:</b></td>
	<td><?php printMLText("lock_message", array("email" => $lockingUser->getEmail(), "username" => htmlspecialchars($lockingUser->getFullName())));?></td>
</tr>
<?php
		}
?>
</tr>
<?php
		$attributes = $document->getAttributes();
		if($attributes) {
			foreach($attributes as $attribute) {
				$attrdef = $attribute->getAttributeDefinition();
?>
		    <tr>
					<td><b><?php echo htmlspecialchars($attrdef->getName()); ?>:</b></td>
					<td><?php echo htmlspecialchars(implode(', ', $attribute->getValueAsArray())); ?></td>
		    </tr>
<?php
			}
		}
?>
</table>
<?php
		$this->contentContainerEnd();
		//$this->preview();
?>
</div>
<div class="col-md-8">
<?php

		// verify if file exists
		$file_exists=file_exists($dms->contentDir . $version->getPath());

		$this->contentHeading(getMLText("details_version", array ("version" => $version->getVersion())));
		$this->contentContainerStart();
		print "<table class=\"table table-bordered table-condensed\">";
		print "<thead>\n<tr>\n";
		print "<th width='10%'></th>\n";
		print "<th width='30%'>".getMLText("file")."</th>\n";
		print "<th width='25%'>".getMLText("comment")."</th>\n";
		print "<th width='15%'>".getMLText("status")."</th>\n";
		print "<th width='20%'>".getMLText("actions")."</th>\n";
		print "</tr>\n</thead>\n<tbody>\n";
		print "<tr>\n";
		print "<td><ul class=\"unstyled\">";

		print "</ul>";
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidthdetail, $timeout);
		$previewer->createPreview($version);
		if($previewer->hasPreview($version)) {
			print("<img class=\"mimeicon\" width=\"".$previewwidthdetail."\" src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$version->getVersion()."&width=".$previewwidthdetail."\" title=\"".htmlspecialchars($version->getMimeType())."\">");
		}
		print "</td>\n";

		print "<td><ul class=\"unstyled\">\n";
		print "<li>".$version->getOriginalFileName()."</li>\n";
		print "<li>".getMLText('version').": ".$version->getVersion()."</li>\n";

		if ($file_exists) print "<li>". formatted_size(filesize($dms->contentDir . $version->getPath())) ." ".htmlspecialchars($version->getMimeType())."</li>";
		else print "<li><span class=\"warning\">".getMLText("document_deleted")."</span></li>";

		$updatingUser = $version->getUser();
		print "<li>".getMLText("uploaded_by")." <a href=\"mailto:".$updatingUser->getEmail()."\">".htmlspecialchars($updatingUser->getFullName())."</a></li>";
		print "<li>".getLongReadableDate($version->getDate())."</li>";
		print "</ul></td>\n";

		print "<td>".htmlspecialchars($version->getComment())."</td>";
		print "<td>".getOverallStatusText($status["status"])."</td>";
		print "<td>";

		//if (($document->getAccessMode($user) >= M_READWRITE)) {
		echo "<div class=\"btn-group-horizontal\">";
		if ($file_exists){
			print "<a type=\"button\" class=\"btn btn-primary btn-action\" href=\"../op/op.Download.php?documentid=".$document->getID()."&version=".$version->getVersion()."\" title=\"".htmlspecialchars($version->getMimeType())."\"><i class=\"fa fa-download\"></i></a>";
			if ($viewonlinefiletypes && in_array(strtolower($version->getFileType()), $viewonlinefiletypes))
				print "<a type=\"button\" class=\"btn btn-info btn-action\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$document->getID()."&version=".$version->getVersion()."\"><i class=\"fa fa-eye\"></i></a>";

		}

		if (($enableversionmodification && ($document->getAccessMode($user) >= M_READWRITE)) || $user->isAdmin()) {
			print "<a type=\"button\" class=\"btn btn-danger btn-action\" href=\"out.RemoveVersion.php?documentid=".$document->getID()."&version=".$version->getVersion()."\"><i class=\"fa fa-remove\"></i></a>";
		}
		if (($enableversionmodification && ($document->getAccessMode($user) == M_ALL)) || $user->isAdmin()) {
			if ( $status["status"]==S_RELEASED || $status["status"]==S_OBSOLETE ){
				print "<a type=\"button\" class=\"btn btn-warning btn-action\" href='../out/out.OverrideContentStatus.php?documentid=".$document->getID()."&version=".$version->getVersion()."'><i class=\"fa fa-align-justify\"></i></a>";
			}
		}
		if (($enableversionmodification && ($document->getAccessMode($user) >= M_READWRITE)) || $user->isAdmin()) {
			if($status["status"] != S_OBSOLETE)
				print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditComment.php?documentid=".$document->getID()."&version=".$version->getVersion()."\"><i class=\"fa fa-comment\"></i></a>";
			if ( $status["status"] == S_DRAFT_REV){
				print "<a type=\"button\" class=\"btn btn-success btn-action\" href=\"out.EditAttributes.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."\"><i class=\"fa fa-pencil\"></i></a>";
		}
			print "</div>";
		}
		else {
			print "&nbsp;";
		}

		echo "</td>";
		print "</tr></tbody>\n</table>\n";


		print "<table class=\"table table-bordered table-condensed\">\n";

		if (is_array($reviewStatus) && count($reviewStatus)>0) {

			print "<tr><td colspan=4>\n";
			$this->contentSubHeading(getMLText("reviewers"));
			print "</td></tr>\n";
			
			print "<tr>\n";
			print "<td width='20%'><b>".getMLText("name")."</b></td>\n";
			print "<td width='20%'><b>".getMLText("last_update")."</b></td>\n";
			print "<td width='25%'><b>".getMLText("comment")."</b></td>";
			print "<td width='35%'><b>".getMLText("status")."</b></td>\n";
			print "</tr>\n";

			foreach ($reviewStatus as $r) {
				$required = null;
				switch ($r["type"]) {
					case 0: // Reviewer is an individual.
						$required = $dms->getUser($r["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_user")." '".$r["required"]."'";
						}
						else {
							$reqName = htmlspecialchars($required->getFullName());
						}
						break;
					case 1: // Reviewer is a group.
						$required = $dms->getGroup($r["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_group")." '".$r["required"]."'";
						}
						else {
							$reqName = htmlspecialchars($required->getName());
						}
						break;
				}
				print "<tr>\n";
				print "<td>".$reqName."</td>\n";
				print "<td><ul class=\"unstyled\"><li>".$r["date"]."</li>";
				$updateUser = $dms->getUser($r["userID"]);
				print "<li>".(is_object($updateUser) ? $updateUser->getFullName() : "unknown user id '".$r["userID"]."'")."</li></ul></td>";
				print "<td>".$r["comment"]."</td>\n";
				print "<td>".getReviewStatusText($r["status"])."</td>\n";
				print "</tr>\n";
			}
		}

		if (is_array($approvalStatus) && count($approvalStatus)>0) {

			print "<tr><td colspan=4>\n";
			$this->contentSubHeading(getMLText("approvers"));
			print "</td></tr>\n";
				
			print "<tr>\n";
			print "<td width='20%'><b>".getMLText("name")."</b></td>\n";
			print "<td width='20%'><b>".getMLText("last_update")."</b></td>\n";
			print "<td width='25%'><b>".getMLText("comment")."</b></td>";
			print "<td width='35%'><b>".getMLText("status")."</b></td>\n";
			print "</tr>\n";

			foreach ($approvalStatus as $a) {
				$required = null;
				switch ($a["type"]) {
					case 0: // Approver is an individual.
						$required = $dms->getUser($a["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_user")." '".$r["required"]."'";
						}
						else {
							$reqName = htmlspecialchars($required->getFullName());
						}
						break;
					case 1: // Approver is a group.
						$required = $dms->getGroup($a["required"]);
						if (!is_object($required)) {
							$reqName = getMLText("unknown_group")." '".$r["required"]."'";
						}
						else {
							$reqName = htmlspecialchars($required->getName());
						}
						break;
				}
				print "<tr>\n";
				print "<td>".$reqName."</td>\n";
				print "<td><ul class=\"documentDetail\"><li>".$a["date"]."</li>";
				$updateUser = $dms->getUser($a["userID"]);
				print "<li>".(is_object($updateUser) ? htmlspecialchars($updateUser->getFullName()) : "unknown user id '".$a["userID"]."'")."</li></ul></td>";
				print "<td>".$a["comment"]."</td>\n";
				print "<td>".getApprovalStatusText($a["status"])."</td>\n";
				print "</tr>\n";
			}
		}

		print "</table>\n";

		$this->contentContainerEnd();

		if($user->isAdmin()) {
			$this->contentHeading(getMLText("status"));
			$this->contentContainerStart();
			$statuslog = $version->getStatusLog();
			echo "<table class=\"table table-condensed table-bordered\"><thead>";
			echo "<th class=\"align-center\">".getMLText('date')."</th><th class=\"align-center\">".getMLText('status')."</th><th class=\"align-center\">".getMLText('user')."</th><th class=\"align-center\">".getMLText('comment')."</th></tr>\n";
			echo "</thead><tbody>";
			foreach($statuslog as $entry) {
				if($suser = $dms->getUser($entry['userID']))
					$fullname = $suser->getFullName();
				else
					$fullname = "--";
				echo "<tr><td>".$entry['date']."</td><td>".getOverallStatusText($entry['status'])."</td><td>".$fullname."</td><td>".$entry['comment']."</td></tr>\n";
			}
			print "</tbody>\n</table>\n";
			$this->contentContainerEnd();

?>
			<div class="row-fluid">
<?php
			/* Check for an existing review log, even if the workflowmode
			 * is set to traditional_only_approval. There may be old documents
			 * that still have a review log if the workflow mode has been
			 * changed afterwards.
			 */
			if($version->getReviewStatus(10)) {
?>
				<div class="span6">
				<?php $this->printProtocol($version, 'review'); ?>
				</div>
<?php
			}
			if($version->getApprovalStatus(10)) {
?>
				<div class="span6">
				<?php $this->printProtocol($version, 'approval'); ?>
				</div>
<?php
			}
?>
			</div>
<?php
		}
?>
</div>
</div>
<?php
		
		$this->endsBoxPrimary();

		echo "</div>"; 
		echo "</div>"; 
		echo "</div>"; // Ends row

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
