<?php
/**
 * Implementation of RemoveVersion view
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
 * Class which outputs the html page for RemoveVersion view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveVersion extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$version = $this->params['version'];

		$this->htmlStartPage(getMLText("delete"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
		<div class="gap-15"></div>
		<div class="row">
		<div class="col-md-12">
		<?php $this->startBoxDanger(getMLText("rm_version")); ?>

<form action="../op/op.RemoveVersion.php" name="form1" method="post">
	<?php echo createHiddenFieldWithKey('removeversion'); ?>
	<input type="hidden" name="documentid" value="<?php echo $document->getID()?>">
	<input type="hidden" name="version" value="<?php echo $version->getVersion()?>">
	<p><?php printMLText("confirm_rm_version", array ("documentname" => htmlspecialchars($document->getName()), "version" => $version->getVersion()));?></p>
  <p><button type="submit" class="btn btn-danger"><i class="fa fa-remove"></i> <?php printMLText("rm_version");?></button></p>
</form>
<?php $this->endsBoxSuccess(); ?>
</div>
</div>
<?php
		
		echo "</div>";

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
