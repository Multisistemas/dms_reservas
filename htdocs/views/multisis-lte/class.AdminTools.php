<?php
/**
 * Implementation of AdminTools view
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
 * Class which outputs the html page for AdminTools view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AdminTools extends SeedDMS_Bootstrap_Style {
	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$logfileenable = $this->params['logfileenable'];
		$enablefullsearch = $this->params['enablefullsearch'];

		$this->htmlStartPage(getMLText("my_account"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		

?>
<div class="gap-20"></div>
<div class="row">
<div class="col-md-12">

	<div id="admin-tools">
	<div class="row"> <!-- /////// -->
	<?php if ($user->_comment != "client-admin") { ?>
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h4><?php echo getMLText("user_management")?></h4>
          <p></p>
        </div>
        <div class="icon">
          <i class="fa fa-user"></i>
        </div>
        <a href="../out/out.UsrMgr.php" class="small-box-footer">
        	<i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h4><?php echo getMLText("group_management")?></h4>
          <p></p>
        </div>
        <div class="icon">
          <i class="fa fa-group"></i>
        </div>
        <a href="../out/out.GroupMgr.php" class="small-box-footer">
        	<i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

	<?php } ?>
	</div>
	<div class="row"> <!-- /////// -->
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h4><?php echo getMLText("backup_tools")?></h4>
          <p></p>
        </div>
        <div class="icon">
          <i class="fa fa-hdd-o"></i>
        </div>
        <a href="../out/out.BackupTools.php" class="small-box-footer">
        	<i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

<?php if ($logfileenable) { ?>
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h4><?php echo getMLText("log_management")?></h4>
          <p></p>
        </div>
        <div class="icon">
          <i class="fa fa-list"></i>
        </div>
        <a href="../out/out.LogManagement.php" class="small-box-footer">
        	<i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
<?php } ?>
	</div>

	<div class="row"> <!-- /////// -->
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
        <h4><?php echo getMLText("global_default_keywords")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-reorder"></i>
      	</div>
      	<a href="../out/out.DefaultKeywords.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
        <h4><?php echo getMLText("global_document_categories")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-columns"></i>
      	</div>
      	<a href="../out/out.Categories.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
        <h4><?php echo getMLText("global_attributedefinitions")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-tags"></i>
      	</div>
      	<a href="../out/out.AttributeMgr.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
	</div>

<?php
	if($this->params['workflowmode'] == 'advanced') {
?>
	<div class="row">
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-purple">
        <div class="inner">
        <h4><?php echo getMLText("global_workflows")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-sitemap"></i>
      	</div>
      	<a href="../out/out.WorkflowMgr.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-purple">
        <div class="inner">
        <h4><?php echo getMLText("global_workflow_states")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-star"></i>
      	</div>
      	<a href="../out/out.WorkflowStatesMgr.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-purple">
        <div class="inner">
        <h4><?php echo getMLText("global_workflow_actions")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-bolt"></i>
      	</div>
      	<a href="../out/out.WorkflowActionsMgr.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
	</div>
<?php } if($enablefullsearch) { ?>
	<div class="row"> <!-- /////// -->
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
        <h4><?php echo getMLText("update_fulltext_index")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-refresh"></i>
      	</div>
      	<a href="../out/out.Indexer.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
        <h4><?php echo getMLText("create_fulltext_index")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-search"></i>
      	</div>
      	<a href="../out/out.CreateIndex.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
        <h4><?php echo getMLText("fulltext_info")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-info-sign"></i>
      	</div>
      	<a href="../out/out.IndexInfo.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
	</div>

<?php } ?>

	<div class="row"> <!-- /////// -->
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
        <h4><?php echo getMLText("folders_and_documents_statistic")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-tasks"></i>
      	</div>
      	<a href="../out/out.Statistic.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
        <h4><?php echo getMLText("charts")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-bar-chart"></i>
      	</div>
      	<a href="../out/out.Charts.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
        <h4><?php echo getMLText("objectcheck")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-check"></i>
      	</div>
      	<a href="../out/out.ObjectCheck.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
        <h4><?php echo getMLText("timeline")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-tasks"></i>
      	</div>
      	<a href="../out/out.Timeline.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
	</div>
	<div class="row"> <!-- /////// -->
	<?php if ($user->_comment != "client-admin") { ?>
		<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
        <h4><?php echo getMLText("settings")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-wrench"></i>
      	</div>
      	<a href="../out/out.Settings.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
        <h4><?php echo getMLText("extension_manager")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-cogs"></i>
      	</div>
      	<a href="../out/out.ExtensionMgr.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
  	<div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
        <h4><?php echo getMLText("version_info")?></h4>
        <p></p>
      	</div>
      	<div class="icon">
        	<i class="fa fa-info-circle"></i>
      	</div>
      	<a href="../out/out.Info.php" class="small-box-footer">
      	<i class="fa fa-arrow-circle-right"></i>
      	</a>
    	</div>
  	</div>
	<?php } ?>	
	</div>
	</div>
	</div>
	</div>
<?php
		echo "</div>";

		

		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>