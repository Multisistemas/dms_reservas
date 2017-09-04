<?php
/**
 * Implementation of Checked out documents view
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
 * Class which outputs the html page for checked out documents view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_CheckedOutDocuments extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');

	} /* }}} */

	function show() { /* {{{ */
		$this->dms = $this->params['dms'];
		$user = $this->params['user'];
		$rootfolder = $this->params['rootfolder'];
		$data = $this->params['data'];
		$type = $this->params['type'];

		$this->htmlStartPage(getMLText("checked_out_documents"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		echo "<div class=\"row-fluid\">\n";

		echo "<div class=\"span3\">\n";
		$this->contentHeading(getMLText("chart_selection"));
		echo "<div class=\"well\">\n";
		foreach(array('docsperuser', 'sizeperuser', 'docspermimetype', 'docspercategory', 'docsperstatus', 'docspermonth', 'docsaccumulated') as $atype) {
			echo "<div><a href=\"?type=".$atype."\">".getMLText('chart_'.$atype.'_title')."</a></div>\n";
		}
		echo "</div>\n";
		echo "</div>\n";

		if(in_array($type, array('docspermonth', 'docsaccumulated'))) {
			echo "<div class=\"span9\">\n";
		} else {
			echo "<div class=\"span6\">\n";
		}
		$this->contentHeading(getMLText('chart_'.$type.'_title'));
		echo "<div class=\"well\">\n";
?>
<div id="chart" style="height: 400px;" class="chart"></div>
<?php
		echo "</div>\n";
		echo "</div>\n";

		if(!in_array($type, array('docspermonth', 'docsaccumulated'))) {
			echo "<div class=\"span3\">\n";
			$this->contentHeading(getMLText('legend'));
			echo "<div class=\"well\" id=\"legend\">\n";
			echo "</div>\n";
			echo "</div>\n";
		}

		echo "</div>\n";

		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}

