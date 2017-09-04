<?php
/**
 * Implementation of RemoveGroup view
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
 * Class which outputs the html page for RemoveGroup view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveGroup extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$group = $this->params['group'];

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

		$this->startBoxDanger(getMLText("rm_group"));

?>
<form action="../op/op.GroupMgr.php" name="form1" method="post">
<input type="hidden" name="groupid" value="<?php print $group->getID();?>">
<input type="hidden" name="action" value="removegroup">
<?php echo createHiddenFieldWithKey('removegroup'); ?>
<p>
<?php printMLText("confirm_rm_group", array ("groupname" => htmlspecialchars($group->getName())));?>
</p>
<p><button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> <?php printMLText("rm_group");?></button></p>
</form>
<?php
		$this->endsBoxDanger();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
