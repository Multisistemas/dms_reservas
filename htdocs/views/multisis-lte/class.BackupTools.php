<?php
/**
 * Implementation of BackupTools view
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
 * Class which outputs the html page for BackupTools view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_BackupTools extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');

		$this->printFolderChooserJs("form1");
		$this->printFolderChooserJs("form2");
		$this->printFolderChooserJs("form3");
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$contentdir = $this->params['contentdir'];

		$this->htmlStartPage(getMLText("admin_tools"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    <?php 

		$this->startBoxPrimary(getMLText("backup_tools"));

		//$this->contentContainerStart();
		$this->infoMsg(getMLText("space_used_on_data_folder")." : ".SeedDMS_Core_File::format_filesize(dskspace($contentdir)));
		//$this->contentContainerEnd();

		// versioning file creation ////////////////////////////////////////////////////

		//$this->contentHeading(getMLText("versioning_file_creation"));
		//$this->contentContainerStart();
		$this->startBoxSolidPrimary(getMLText("versioning_file_creation"));
		print "<p>".getMLText("versioning_file_creation_warning")."</p>\n";

		print "<form class=\"form-inline\" action=\"../op/op.CreateVersioningFiles.php\" name=\"form1\">";
		$this->printFolderChooserHtml3("form1",M_READWRITE);
		print "<input type='submit' class='btn btn-info' name='' value='".getMLText("versioning_file_creation")."'/>";
		print "</form>\n";

		$this->endsBoxSolidPrimary();
		//$this->contentContainerEnd();

		// archive creation ////////////////////////////////////////////////////////////

		//$this->contentHeading(getMLText("archive_creation"));
		//$this->contentContainerStart();
		$this->startBoxSolidPrimary(getMLText("archive_creation"));
		print "<p>".getMLText("archive_creation_warning")."</p>\n";

		print "<form action=\"../op/op.CreateFolderArchive.php\" name=\"form2\">";
		$this->printFolderChooserHtml3("form2",M_READWRITE);
		print "<input type=\"checkbox\" name=\"human_readable\" value=\"1\"> ".getMLText("human_readable")."<br>";
		print "<br>";
		print "<input type='submit' class='btn btn-info' name='' value='".getMLText("archive_creation")."'/>";
		print "</form>\n";

		// list backup files

		$handle = opendir($contentdir);
		$entries = array();
		while ($e = readdir($handle)){
			if (is_dir($contentdir.$e)) continue;
			if (strpos($e,".tar.gz")==FALSE) continue;
			$entries[] = $e;
		}
		closedir($handle);

		sort($entries);
		$entries = array_reverse($entries);

		if($entries) {
			$this->contentSubHeading(getMLText("backup_list"));
			print "<div class='table-responsive'>";
			print "<table class=\"table table-bordered table-condensed\">\n";
			print "<thead>\n<tr>\n";
			print "<th></th>\n";
			print "<th>".getMLText("folder")."</th>\n";
			print "<th>".getMLText("creation_date")."</th>\n";
			print "<th>".getMLText("file_size")."</th>\n";
			print "<th></th>\n";
			print "</tr>\n</thead>\n<tbody>\n";

			foreach ($entries as $entry){

				$folderid=substr($entry,strpos($entry,"_")+1);
				$folder=$dms->getFolder((int)$folderid);
						
				print "<tr>\n";
				print "<td><a href=\"../op/op.Download.php?arkname=".$entry."\">".$entry."</a></td>\n";
				if (is_object($folder)) print "<td>".htmlspecialchars($folder->getName())."</td>\n";
				else print "<td>".getMLText("unknown_id")."</td>\n";
				print "<td>".getLongReadableDate(filectime($contentdir.$entry))."</td>\n";
				print "<td>".SeedDMS_Core_File::format_filesize(filesize($contentdir.$entry))."</td>\n";
				print "<td>";
				print "<a href=\"out.RemoveArchive.php?arkname=".$entry."\" class=\"btn btn-mini btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText("backup_remove")."</a>";
				print "</td>\n";	
				print "</tr>\n";
			}
			print "</table>\n";
			print "</div>";
		}

		//$this->contentContainerEnd();
		$this->endsBoxSolidPrimary();

		// dump creation ///////////////////////////////////////////////////////////////

		//$this->contentHeading(getMLText("dump_creation"));
		//$this->contentContainerStart();
		$this->startBoxSolidPrimary(getMLText("dump_creation"));
		print "<p>".getMLText("dump_creation_warning")."</p>\n";

		print "<form action=\"../op/op.CreateDump.php\" name=\"form4\">";
		print "<input type='submit' class='btn btn-info' name='' value='".getMLText("dump_creation")."'/>";
		print "</form>\n";

		// list backup files
		$handle = opendir($contentdir);
		$entries = array();
		while ($e = readdir($handle)){
			if (is_dir($contentdir.$e)) continue;
			if (strpos($e,".sql.gz")==FALSE) continue;
			$entries[] = $e;
		}
		closedir($handle);

		sort($entries);
		$entries = array_reverse($entries);

		if($entries) {
			$this->contentSubHeading(getMLText("dump_list"));
			print "<div class='table-responsive'>";
			print "<table class=\"table table-bordered table-condensed\">\n";
			print "<thead>\n<tr>\n";
			print "<th></th>\n";
			print "<th>".getMLText("creation_date")."</th>\n";
			print "<th>".getMLText("file_size")."</th>\n";
			print "<th></th>\n";
			print "</tr>\n</thead>\n<tbody>\n";

			foreach ($entries as $entry){
				print "<tr>\n";
				print "<td><a href=\"../op/op.Download.php?dumpname=".$entry."\">".$entry."</a></td>\n";
				print "<td>".getLongReadableDate(filectime($contentdir.$entry))."</td>\n";
				print "<td>".SeedDMS_Core_File::format_filesize(filesize($contentdir.$entry))."</td>\n";
				print "<td>";
				print "<a href=\"out.RemoveDump.php?dumpname=".$entry."\" class=\"btn btn-mini btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText("dump_remove")."</a>";
				print "</td>\n";	
				print "</tr>\n";
			}
			print "</table>\n";
			print "</div>";
		}

		//$this->contentContainerEnd();
		$this->endsBoxSolidPrimary();

		// files deletion //////////////////////////////////////////////////////////////
		/*
		$this->contentHeading(getMLText("files_deletion"));
		$this->contentContainerStart();
		print "<p>".getMLText("files_deletion_warning")."</p>\n";

		print "<form class=\"form-inline\" action=\"../out/out.RemoveFolderFiles.php\" name=\"form3\">";
		$this->printFolderChooserHtml("form3",M_READWRITE);
		print "<input type='submit' class='btn' name='' value='".getMLText("files_deletion")."'/>";
		print "</form>\n";

		$this->contentContainerEnd();
		*/

		$this->endsBoxPrimary();

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
