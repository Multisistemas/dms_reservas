<?php
/**
 * Implementation of AddFile view
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
 * Class which outputs the html page for AddFile view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddFile extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		header('Content-Type: application/javascript');

?>
function folderSelected(id, name) {
	window.location = '../out/out.ViewFolder.php?folderid=' + id;
}

function checkForm()
{
	msg = new Array();
	if ($("#userfile").val() == "") msg.push("<?php printMLText("js_no_file");?>");
	if ($("#name").val() == "") msg.push("<?php printMLText("js_no_name");?>");
<?php
	if (isset($settings->_strictFormCheck) && $settings->_strictFormCheck) {
?>
	if ($("#comment").val() == "") msg.push("<?php printMLText("js_no_comment");?>");
<?php
	}
?>
	if (msg != "")
	{
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}

$(document).ready( function() {
	$('body').on('submit', '#fileupload', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
});
<?php
		$this->printNewTreeNavigationJs($folder->getID(), M_READ, 0, '', 0, "");
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$strictformcheck = $this->params['strictformcheck'];
		$enablelargefileupload = $this->params['enablelargefileupload'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();
		echo $this->getDefaultFolderPathHTML($folder, true, $document);

		//// Atach file ////
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-12\">";

		?>

<!--<div class="callout callout-warning alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>-->
	<?php	$this->warningMsg(getMLText("max_upload_size").": ".ini_get( "upload_max_filesize")); ?>
	<?php
		/*if($enablelargefileupload) {
	  	printf('<p>'.getMLText('link_alt_updatedocument').'</p>', "out.AddFile2.php?documentid=".$document->getId());
		}*/
	?>
<!--</div>-->

<?php
		echo "<div class=\"box box-primary\">";
		echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".getMLText("linked_files")."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";
?>

<form class="" action="../op/op.AddFile.php" enctype="multipart/form-data" method="post" name="form1" id="fileupload">
<input type="hidden" name="documentid" value="<?php print $document->getId(); ?>">

<div class="form-group">
	<label><?php printMLText("local_file");?>:</label>
	<div class="controls">
		<?php $this->printFileChooser('userfile', false); ?>
	</div>
</div>


<div class="form-group">
	<label><?php printMLText("name");?>:</label>
	<div class="controls">
		<input type="text" class="form-control" name="name" id="name" size="60" required="required">
	</div>
</div>


<div class="form-group">
	<label><?php printMLText("comment");?>:</label>
	<div class="controls">
		<textarea name="comment" class="form-control" id="comment" rows="4" cols="80"></textarea>
	</div>
</div>


<div class="controls">
	<button class="btn btn-info" type="submit"><i class="fa fa-save"></i> <?php printMLText("add");?></button>
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
