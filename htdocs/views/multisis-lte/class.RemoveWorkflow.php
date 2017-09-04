<?php
/**
 * Implementation of RemoveWorkflow view
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
 * Class which outputs the html page for Removeorkflow view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveWorkflow extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$workflow = $this->params['workflow'];

		$this->htmlStartPage(getMLText("rm_workflow"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();		

		//echo $this->getDefaultFolderPathHTML($folder, true, $document);

		//// Document content ////
		echo "<div class=\"row\">";
		echo "<div class=\"gap-20\"></div>";
		echo "<div class=\"col-md-12\">";
		$this->startBoxPrimary(getMLText("rm_workflow"));
		// Display the Workflow form.
?>
	<?php $this->warningMsg(getMLText("rm_workflow_warning")); ?>
	<div class="col-md-6">
		<form method="post" action="../op/op.RemoveWorkflow.php" name="form1">
			<?php echo createHiddenFieldWithKey('removeworkflow'); ?>
			<input type='hidden' name='workflowid' value='<?php echo $workflow->getId(); ?>'/>
			<div class="box-footer">
				<button type='submit' class="btn btn-danger"><i class="fa fa-times"></i> <?php printMLText("rm_workflow"); ?></button>	
			</div>
		</form>
	</div>

	<div id="workflowgraph" class="col-md-6">
		<iframe src="out.WorkflowGraph.php?workflow=<?php echo $workflow->getID(); ?>" width="100%" height="661" style="border: 1px solid #AAA;"></iframe>
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
