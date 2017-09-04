<?php
/**
 * Implementation of SetWorkflow view
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
 * Class which outputs the html page for SetWorkflow view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SetWorkflow extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$document = $this->params['document'];
		header('Content-Type: application/javascript; charset=UTF-8');
?>
function showWorkflow(selectObj) {
	id = selectObj.options[selectObj.selectedIndex].value;
	if (id > 0) {
		$('#workflowgraph').show();
		$('#workflowgraph iframe').attr('src', 'out.WorkflowGraph.php?documentid=<?php echo $document->getID(); ?>&workflow='+id);
	} else {
		$('#workflowgraph').hide();
	}

}
$(document).ready( function() {
	$( "#selector" ).change(function() {
		showWorkflow(this);
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];

		$latestContent = $document->getLatestContent();

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))), "skin-blue sidebar-mini");

		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();		

		echo $this->getDefaultFolderPathHTML($folder, true, $document);

		//// Document content ////
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-12\">";

		$this->startBoxPrimary(getMLText("set_workflow"));

		$workflows = $dms->getAllWorkflows();
		if($workflows) {
?>
		<form class="form-horizontal" action="../op/op.SetWorkflow.php" method="post" name="form1">
		<?php echo createHiddenFieldWithKey('setworkflow'); ?>
		<input type="hidden" name="documentid" value="<?php print $document->getID(); ?>">
		<input type="hidden" name="version" value="<?php print $latestContent->getVersion(); ?>">
		<input type="hidden" name="showtree" value="<?php echo showtree();?>">

		<div class="control-group">
			<label class="control-label">
				<div class="cbSelectTitle"><?php printMLText("workflow");?>:</div>
      		</label>
      		<div class="controls">
<?php
					echo "<select id=\"selector\" class=\"_chzn-select-deselect form-control\" name=\"workflow\" data-placeholder=\"".getMLText('select_workflow')."\">";
					$mandatoryworkflow = $user->getMandatoryWorkflow();
					$workflows=$dms->getAllWorkflows();
					foreach ($workflows as $workflow) {
						print "<option value=\"".$workflow->getID()."\"";
						if($mandatoryworkflow && $mandatoryworkflow->getID() == $workflow->getID())
							echo " selected=\"selected\"";
						print ">". htmlspecialchars($workflow->getName())."</option>";
					}
					echo "</select>";
?>
			</div>
		</div>

		<div class="box-footer">
			<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> <?php printMLText("set_workflow");?></button>
		</div>

		</form>
<?php
				} else {
?>
		<p><?php printMLText('no_workflow_available'); ?></p>
<?php
				}
?>

	<div id="workflowgraph" class="span8" style="display: none;">
	<iframe src="" width="100%" height="500" style="border: 1px solid #AAA;"></iframe>
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
