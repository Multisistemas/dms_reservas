<?php
/**
 * Implementation of RewindWorkflow view
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
 * Class which outputs the html page for Rewindorkflow view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RewindWorkflow extends SeedDMS_Bootstrap_Style {

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
		$this->startBoxPrimary(getMLText("rewind_workflow"));

		$currentstate = $latestContent->getWorkflowState();
		$wkflog = $latestContent->getWorkflowLog();
		$workflow = $latestContent->getWorkflow();

		$msg = getMLText("status_current_info").$currentstate->getName()."<br />";
		if($wkflog) {
			foreach($wkflog as $entry) {
				if($entry->getTransition()->getNextState()->getID() == $currentstate->getID()) {
					$enterdate = $entry->getDate();
					$enterts = makeTsFromLongDate($enterdate);
				}
			}
			$msg .= getMLText("status_date_record_one").$enterdate.getMLText("status_date_record_two");
			$msg .= getReadableDuration((time()-$enterts)).".<br />";
		}
		//$msg .= "The document may stay in this state for ".$currentstate->getMaxTime()." sec.";
		$this->infoMsg($msg);

		// Display the Workflow form.
?>

	<?php $this->warningMsg(getMLText("rewind_workflow_warning")); ?>
	<div class="col-md-4">
		<form method="post" action="../op/op.RewindWorkflow.php" name="form1">
			<?php echo createHiddenFieldWithKey('rewindworkflow'); ?>
			<input type='hidden' name='documentid' value='<?php echo $document->getId(); ?>'/>
			<input type='hidden' name='version' value='<?php echo $latestContent->getVersion(); ?>'/>
		    <input type='hidden' name='folderid' value='<?php echo $folder->getId(); ?>' />
		  <div class="">
		  	<button type='submit' class="btn btn-info"><i class="fa fa-refresh"></i> <?php printMLText("rewind_workflow"); ?></button>
		  </div>
		</form>
	</div>

	<?php


		if($wkflog) {
			echo "<div class=\"col-md-8\">";
			echo "<div class=\"table-responsive\">";
			echo "<table class=\"table table-striped table-bordered\">";
			echo "<tr><th class=\"align-center th-info-background\">".getMLText('action')."</th><th class=\"align-center th-info-background\">".getMLText('status_from')."</th><th class=\"align-center th-info-background\">".getMLText('status_to')."</th><th class=\"align-center th-info-background\">".getMLText('date')."</th><th class=\"align-center th-info-background\">".getMLText('user')."</th><th class=\"align-center th-info-background\">".getMLText('comment')."</th></tr>";
			foreach($wkflog as $entry) {
				echo "<tr>";
				echo "<td>".getMLText('action')." ".$entry->getTransition()->getAction()->getName()."</td>";
				echo "<td>".$entry->getTransition()->getState()->getName()."</td>";
				echo "<td>".$entry->getTransition()->getNextState()->getName()."</td>";
				echo "<td>".$entry->getDate()."</td>";
				echo "<td>".$entry->getUser()->getFullname()."</td>";
				echo "<td>".$entry->getComment()."</td>";
				echo "</tr>";
			}
			echo "</table>\n";
			echo "</div>";
			echo "</div>";
		}

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
