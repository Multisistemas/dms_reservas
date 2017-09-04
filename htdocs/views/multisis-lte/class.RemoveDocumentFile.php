<?php
/**
 * Implementation of RemoveDocumentFile view
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
 * Class which outputs the html page for RemoveDocumentFile view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveDocumentFile extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		header('Content-Type: application/javascript; charset=UTF-8');
?>

function folderSelected(id, name) {
	window.location = '../out/out.ViewFolder.php?folderid=' + id;
}

<?php 
	$this->printNewTreeNavigationJs($folder->getID(), M_READ, 0, '', 0, "");
} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$file = $this->params['file'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();
		echo $this->getDefaultFolderPathHTML($folder, true, $document);

		//// Remove attached file ////
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-12\">";

		echo "<div class=\"box box-danger\">";
		echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".getMLText("rm_file")."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";
?>

<form action="../op/op.RemoveDocumentFile.php" name="form1" method="post">
  <?php echo createHiddenFieldWithKey('removedocumentfile'); ?>
	<input type="Hidden" name="documentid" value="<?php echo $document->getID()?>">
	<input type="Hidden" name="fileid" value="<?php echo $file->getID()?>">
	<p><?php printMLText("confirm_rm_file", array ("documentname" => htmlspecialchars($document->getName()), "name" => htmlspecialchars($file->getName())));?></p>
	<div class="box-footer">
		<button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> <?php printMLText("rm_file");?></button>
	</div>
</form>

<?php

		echo "</div>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
		echo "</div>";

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>
