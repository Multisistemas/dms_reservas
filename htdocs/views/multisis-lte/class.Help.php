<?php
/**
 * Implementation of Help view
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
 * Class which outputs the html page for Help view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Help extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$context = $this->params['context'];


		$this->htmlStartPage(getMLText("help"), "skin-blue sidebar-mini sidebar-collapse");
		$this->containerStart();
		$this->mainHeader();
		$this->contentStart();

		echo "<div class=\"row\">";
		echo "<div class=\"gap-20\"></div>";
		echo "<div class=\"col-md-12\">";

		$this->startBoxPrimary(getMLText("help"));

		$helpfile = "../languages/".$this->params['session']->getLanguage()."/help/".$context.".html";
		if(file_exists($helpfile))
			readfile($helpfile);
		else
			readfile("../languages/".$this->params['session']->getLanguage()."/help.htm");

		$this->endsBoxPrimary();

		print "</div>";
		print "</div>";
		print "</div>";
		
		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
