<?php
/**
 * Implementation of LogManagement view
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
 * Class which outputs the html page for LogManagement view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_LogManagement extends SeedDMS_Bootstrap_Style {

	function filelist($entries, $mode) { /* {{{ */
		$print_header = true;
		foreach ($entries as $entry){
			
			if ($print_header){
				print "<div class='table-responsive'>";
				print "<form action=\"out.RemoveLog.php\" method=\"get\">\n";
				print "<table class=\"table table-bordered table-condensed\">\n";
				print "<thead>\n<tr>\n";
				print "<th></th>\n";
				print "<th>".getMLText("name")."</th>\n";
				print "<th>".getMLText("creation_date")."</th>\n";
				print "<th>".getMLText("file_size")."</th>\n";
				print "<th></th>\n";
				print "</tr>\n</thead>\n<tbody>\n";
				$print_header=false;
			}
					
			print "<tr>\n";
			print "<td><input type=\"checkbox\" name=\"logname[]\" value=\"".$entry."\"/></td>\n";
			print "<td><a href=\"out.LogManagement.php?logname=".$entry."\">".$entry."</a></td>\n";
			print "\n";
			print "<td>".getLongReadableDate(filectime($this->contentdir.$entry))."</td>\n";
			print "<td>".SeedDMS_Core_File::format_filesize(filesize($this->contentdir.$entry))."</td>\n";
			print "<td>";
			
			print "<a href=\"out.RemoveLog.php?mode=".$mode."&logname=".$entry."\" class=\"btn btn-mini btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText("rm_file")."</a>";
			print "&nbsp;";
			print "<a href=\"../op/op.Download.php?logname=".$entry."\" class=\"btn btn-mini btn-success\"><i class=\"fa fa-download\"></i> ".getMLText("download")."</a>";
			print "&nbsp;";
			print "<a data-target=\"#logViewer\" data-cache=\"false\" href=\"out.LogManagement.php?logname=".$entry."\" role=\"button\" class=\"btn btn-mini btn-info\" data-toggle=\"modal\"><i class=\"fa fa-eye\"></i> ".getMLText('view')."</a>";
			print "</td>\n";	
			print "</tr>\n";
		}

		if ($print_header) printMLText("empty_notify_list");
		else print "<tr><td><i class=\"fa fa-arrow-up\"></i></td><td colspan=\"2\"><button type=\"submit\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText('remove_marked_files')."</button></td></tr></table></form></div>\n";

	} /* }}} */

	function js() { /* {{{ */
		header('Content-Type: application/javascript');
?>
$(document).ready( function() {
	$('i.fa-arrow-up').on('click', function(e) {
//var checkBoxes = $("input[type=checkbox]");
//checkBoxes.prop("checked", !checkBoxes.prop("checked"));
		$('input[type=checkbox]').prop('checked', true);
	});

});		
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$this->contentdir = $this->params['contentdir'];
		$logname = $this->params['logname'];
		$mode = $this->params['mode'];

		if(!$logname) {
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

		$this->startBoxPrimary(getMLText("log_management"));

		$entries = array();
		$wentries = array();
		$handle = opendir($this->contentdir);
		if($handle) {
			while ($e = readdir($handle)){
				if (is_dir($this->contentdir.$e)) continue;
				if (strpos($e,".log")==FALSE) continue;
				if (strcmp($e,"current.log")==0) continue;
				if(substr($e, 0, 6) ==  'webdav') {
					$wentries[] = $e;
				} else {
					$entries[] = $e;
				}
			}
			closedir($handle);

			sort($entries);
			sort($wentries);
			$entries = array_reverse($entries);
			$wentries = array_reverse($wentries);
		}
?>
	<div class="nav-tabs-custom">
  <ul class="nav nav-tabs" id="logtab">
	  <li <?php echo ($mode == 'web') ? 'class="active"' : ''; ?>><a href="" data-target="#web" data-toggle="tab">web</a></li>
	  <li <?php echo ($mode == 'webdav') ? 'class="active"' : ''; ?>><a href="" data-target="#webdav" data-toggle="tab">webdav</a></li>
	</ul>
	<div class="tab-content">
	  <div class="tab-pane <?php echo ($mode == 'web') ? 'active' : ''; ?>" id="web">
<?php
		$this->contentContainerStart();
		$this->filelist($entries, 'web');
		$this->contentContainerEnd();
?>
		</div>
	  <div class="tab-pane <?php echo ($mode == 'webdav') ? 'active' : ''; ?>" id="webdav">
<?php
		$this->contentContainerStart();
		$this->filelist($wentries, 'webdav');
		$this->contentContainerEnd();
?>
		</div>
	</div>
</div>
 
  <div class="modal fade" id="logViewer" tabindex="-1" role="dialog" aria-labelledby="docChooserLabel" aria-hidden="true">
  <div class="modal-dialog modal-primary modal-lg" role="document">
  	<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 class="modal-title"><?php printMLText("log_management") ?></h3>
		  </div>
		  <div class="modal-body">
				<p><?php printMLText('tree_loading') ?></p>
		  </div>
		  <div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
		  </div>
	  </div>
	 </div>
	</div>

<?php
		$this->endsBoxPrimary();

		echo "</div>";
		echo "</div>";
		echo "</div>";
		
    $this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();

		} elseif(file_exists($this->contentdir.$logname)){
			echo $logname."<pre>\n";
			readfile($this->contentdir.$logname);
			echo "</pre>\n";
		} else {
			UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
		}

	} /* }}} */
}
?>
