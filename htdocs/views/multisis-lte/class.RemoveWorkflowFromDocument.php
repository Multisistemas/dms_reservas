<?php
/**
 * Implementation of RemoveWorkflowFromDocument view
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
class SeedDMS_View_RemoveWorkflowFromDocument extends SeedDMS_Bootstrap_Style {

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

		$currentstate = $latestContent->getWorkflowState();
		$wkflog = $latestContent->getWorkflowLog();
		$workflow = $latestContent->getWorkflow();

		$msg = getMLText("status_current_info")." ".$currentstate->getName()."<br />";
		if($wkflog) {
			foreach($wkflog as $entry) {
				if($entry->getTransition()->getNextState()->getID() == $currentstate->getID()) {
					$enterdate = $entry->getDate();
					$enterts = makeTsFromLongDate($enterdate);
				}
			}
			$msg .= getMLText("status_date_record_one")." ".$enterdate." ".getMLText("status_date_record_two");
			$msg .= getReadableDuration((time()-$enterts)).".<br />";
		}
		//$msg .= "The document may stay in this state for ".$currentstate->getMaxTime()." sec.";
		$this->infoMsg($msg);

		$this->startBoxDanger(getMLText("rm_workflow"));
		// Display the Workflow form.
?>

	<?php $this->warningMsg(getMLText("rm_workflow_warning")); ?>
	<div class="col-md-6">
		<form method="post" action="../op/op.RemoveWorkflowFromDocument.php" name="form1">
		<?php echo createHiddenFieldWithKey('removeworkflowfromdocument'); ?>
		<input type='hidden' name='documentid' value='<?php echo $document->getId(); ?>'/>
		<input type='hidden' name='version' value='<?php echo $latestContent->getVersion(); ?>'/>
		<div class="box-footer">
			<button type='submit' class="btn btn-danger"><i class="fa fa-times"></i> <?php printMLText("rm_workflow"); ?></button>
		</div>
		</form>
	</div>

	<div id="workflowgraph" class="col-md-6">
		<iframe src="out.WorkflowGraph.php?workflow=<?php echo $workflow->getID(); ?>" width="100%" height="400" style="border: 1px solid #c0c0c0;"></iframe>
	</div>

<?php


		if($wkflog) {

			echo "<table class=\"table-condensed\">";
			echo "<tr><th>".getMLText('action')."</th><th>Start state</th><th>End state</th><th>".getMLText('date')."</th><th>".getMLText('user')."</th><th>".getMLText('comment')."</th></tr>";
			foreach($wkflog as $entry) {
				echo "<tr>";
				echo "<td>".getMLText('action_'.$entry->getTransition()->getAction()->getName())."</td>";
				echo "<td>".$entry->getTransition()->getState()->getName()."</td>";
				echo "<td>".$entry->getTransition()->getNextState()->getName()."</td>";
				echo "<td>".$entry->getDate()."</td>";
				echo "<td>".$entry->getUser()->getFullname()."</td>";
				echo "<td>".$entry->getComment()."</td>";
				echo "</tr>";
			}
			echo "</table>\n";

		}

		$this->endsBoxDanger();
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
