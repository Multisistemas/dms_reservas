<?php
/**
 * Implementation of ViewEvent view
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
 * Class which outputs the html page for ViewEvent view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ViewEvent extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$event = $this->params['event'];

		$this->htmlStartPage(getMLText("calendar"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    <?php 

		$this->startCalendarBox(getMLText("event_details"));

		$u=$dms->getUser($event["userID"]);

		echo "<table class=\"table table-bordered table-striped\">";

		echo "<tr>";
		echo "<td><strong>".getMLText("name").": </strong></td>";
		echo "<td>".htmlspecialchars($event["name"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td><strong>".getMLText("comment").": </strong></td>";
		echo "<td>".htmlspecialchars($event["comment"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td><strong>".getMLText("from").": </strong></td>";
		echo "<td>".getReadableDate($event["start"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td><strong>".getMLText("to").": </strong></td>";
		echo "<td>".getReadableDate($event["stop"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td><strong>".getMLText("last_update").": </strong></td>";
		echo "<td>".getLongReadableDate($event["date"])."</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td><strong>".getMLText("user").": </strong></td>";
		echo "<td>".(is_object($u)?htmlspecialchars($u->getFullName()):getMLText("unknown_user"))."</td>";
		echo "</tr>";

		if (($user->getID()==$event["userID"])||($user->isAdmin())){
			echo "<tr>";
			echo "<td></td>";
			echo "<td>";
			print "<a href=\"../out/out.RemoveEvent.php?id=".$event["id"]."\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText("delete")."</a> ";
			print "<a href=\"../out/out.EditEvent.php?id=".$event["id"]."\" class=\"btn btn-success\"><i class=\"fa fa-pencil\"></i> ".getMLText("edit")."</a>";
			echo "</tr>";
		}

		echo "</table>";

		$this->endsCalendarBox();

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
