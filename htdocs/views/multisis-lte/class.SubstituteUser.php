<?php
/**
 * Implementation of SubstituteUser view
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
 * Class which outputs the html page for SubstituteUser view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SubstituteUser extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$allUsers = $this->params['allusers'];

		$this->htmlStartPage(getMLText("substitute_user"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    <?php 
    $this->startBoxPrimary(getMLText("substitute_user"));
		?>
	<table class="table table-striped table-condensed">
		<thead>
		<tr><th class="align-center"><?php printMLText('name'); ?></th><th class="align-center"><?php printMLText('email');?></th><th class="align-center"><?php printMLText('groups'); ?></th><th class="align-center"><?php printMLText("action"); ?></th></tr>
		</thead>
		<tbody>
<?php
		foreach ($allUsers as $currUser) {
			echo "<tr>";
			echo "<td>";
			echo htmlspecialchars($currUser->getFullName())." (".htmlspecialchars($currUser->getLogin()).")<br />";
			echo "<small>".htmlspecialchars($currUser->getComment())."</small>";
			echo "</td>";
			echo "<td>";
			echo "<a href=\"mailto:".htmlspecialchars($currUser->getEmail())."\">".htmlspecialchars($currUser->getEmail())."</a><br />";
			echo "</td>";
			echo "<td>";
			$groups = $currUser->getGroups();
			if (count($groups) != 0) {
				for ($j = 0; $j < count($groups); $j++)	{
					print htmlspecialchars($groups[$j]->getName());
					if ($j +1 < count($groups))
						print ", ";
				}
			}
			echo "</td>";
			echo "<td>";
			if($currUser->getID() != $user->getID()) {
				echo "<a class=\"btn btn-success\" href=\"../op/op.SubstituteUser.php?userid=".((int) $currUser->getID())."&formtoken=".createFormKey('substituteuser')."\"><i class=\"fa fa-exchange\"></i> ".getMLText('substitute_user')."</a> ";
			}
			echo "</td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
		
		$this->endsBoxPrimary();
		?>
    </div>
    </div>
    </div>
    <?php
		
    $this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

