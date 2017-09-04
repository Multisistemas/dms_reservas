<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//    Copyright (C) 2009-2012 Uwe Steinmann
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.


class SeedDMS_Bootstrap_Style extends SeedDMS_View_Common {
	var $imgpath;

	/**
	 * @var string $extraheader extra html code inserted in the html header
	 * of the page
	 *
	 * @access protected
	 */
	protected $extraheader;

	function __construct($params, $theme='multisis-lte') {
		$this->theme = $theme;
		$this->params = $params;
		$this->imgpath = '/views/'.$theme.'/images/';
		$this->extraheader = array('js'=>'', 'css'=>'');
		$this->footerjs = array();
	}

	/**
	 * Add javascript to an internal array which is output at the
	 * end of the page within a document.ready() function.
	 *
	 * @param string $script javascript to be added
	 */
	function addFooterJS($script) { /* {{{ */
		$this->footerjs[] = $script;
	} /* }}} */

	function htmlStartPage($title="", $bodyClass="", $base="") { /* {{{ */
		if(1 || method_exists($this, 'js')) {
			/* We still need unsafe-eval, because printDocumentChooserHtml and
			 * printFolderChooserHtml will include a javascript file with ajax
			 * which is evaled by jquery
			 * X-WebKit-CSP is deprecated, Chrome understands Content-Security-Policy
			 * since version 25+
			 * X-Content-Security-Policy is deprecated, Firefox understands
			 * Content-Security-Policy since version 23+
			 */
			$csp_rules = "script-src 'self' 'unsafe-eval';"; // style-src 'self';";
			foreach (array("X-WebKit-CSP", "X-Content-Security-Policy", "Content-Security-Policy") as $csp) {
				header($csp . ": " . $csp_rules);
			}
		}
		echo "<!DOCTYPE html>\n";
		echo "<html lang=\"en\">\n<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">'."\n";
		if($base)
			echo '<base href="/../">'."\n";

		// CSS Style Sheets
		echo '<link href="/styles/'.$this->theme.'/bootstrap/css/bootstrap.min.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/font-awesome/css/font-awesome.min.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/ionicons/css/ionicons.min.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/dist/css/AdminLTE.min.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/dist/css/skins/_all-skins.min.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/plugins/pace/pace.min.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/datepicker/css/datepicker.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/chosen/css/chosen.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/select2/css/select2.min.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/select2/css/select2-bootstrap.css" rel="stylesheet">'."\n";
		echo '<link href="/styles/'.$this->theme.'/plugins/jqtree/jqtree.css" rel="stylesheet">'."\n";
		//echo '<link href="/styles/'.$this->theme.'/application.css" rel="stylesheet">'."\n";

		echo '<link href="/styles/'.$this->theme.'/custom.css" rel="stylesheet">'."\n";

		// Js Scripts
		echo '<link rel="shortcut icon" href="/styles/'.$this->theme.'/favicon.ico" type="image/x-icon"/>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/plugins/jQuery/jquery-2.2.3.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/plugins/bootbox/bootbox-4.4.0.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/passwordstrength/jquery.passwordstrength.js"></script>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/plugins/noty/jquery.noty.js"></script>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/plugins/noty/layouts/topRight.js"></script>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/plugins/noty/layouts/topCenter.js"></script>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/plugins/noty/themes/default.js"></script>'."\n";
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/plugins/jqtree/tree.jquery.js"></script>'."\n";
		
		echo '<script type="text/javascript" src="/styles/'.$this->theme.'/custom/js/validate-logo.js"></script>'."\n";	
		

		if($this->extraheader['css'])
			echo $this->extraheader['css'];
			if($this->extraheader['js'])
				echo $this->extraheader['js'];

		$sitename = trim(strip_tags($this->params['sitename']));
		echo "<title>".(strlen($sitename)>0 ? $sitename : "SeedDMS").(strlen($title)>0 ? ": " : "").htmlspecialchars($title)."</title>\n";
		echo "</head>\n";
		echo "<body".(strlen($bodyClass)>0 ? " class=\"".$bodyClass."\"" : "").">\n";
		if($this->params['session'] && $flashmsg = $this->params['session']->getSplashMsg()) {
			$this->params['session']->clearSplashMsg();
			echo "<div class=\"splash\" data-type=\"".$flashmsg['type']."\">".$flashmsg['msg']."</div>\n";
		}
	} /* }}} */

	function htmlAddHeader($head, $type='js') { /* {{{ */
		$this->extraheader[$type] .= $head;
	} /* }}} */

	function htmlEndPage($nofooter=false) { /* {{{ */
		//echo '<script src="/styles/'.$this->theme.'/bootstrap/js/bootstrap.min.js"></script>'."\n";
		echo '<script src="/styles/'.$this->theme.'/dist/js/app.min.js"></script>'."\n";
		echo "<script src=\"/styles/".$this->theme."/plugins/slimScroll/jquery.slimscroll.min.js\"></script>";
		echo "<script src=\"/styles/".$this->theme."/plugins/fastclick/fastclick.js\"></script>";
		echo "<script src=\"/styles/".$this->theme."/plugins/pace/pace.min.js\"></script>";
		

		echo '<script src="/styles/'.$this->theme.'/datepicker/js/bootstrap-datepicker.js"></script>'."\n";
		foreach(array('de', 'es', 'ca', 'nl', 'fi', 'cs', 'it', 'fr', 'sv', 'sl', 'pt-BR', 'zh-CN', 'zh-TW') as $lang)
			echo '<script src="/styles/'.$this->theme.'/datepicker/js/locales/bootstrap-datepicker.'.$lang.'.js"></script>'."\n";

		echo '<script src="/styles/'.$this->theme.'/chosen/js/chosen.jquery.min.js"></script>'."\n";
		echo '<script src="/styles/'.$this->theme.'/select2/js/select2.min.js"></script>'."\n";
		echo '<script src="/styles/'.$this->theme.'/application.js"></script>'."\n";
		echo '<script src="/styles/'.$this->theme.'/dist/js/demo.js"></script>'."\n";
		echo '<script src="/styles/'.$this->theme.'/bootstrap/js/bootstrap-2.min.js"></script>'."\n";
		//echo '<script src="/styles/'.$this->theme.'/plugins/jQueryUI/jquery-ui.min.js"></script>'."\n";
		//echo '<script src="/styles/'.$this->theme.'/plugins/daterangepicker/moment.min.js"></script>'."\n";
		//echo '<script src="/styles/'.$this->theme.'/plugins/fullcalendar/fullcalendar.min.js"></script>'."\n";
		
		if($this->footerjs) {
			$jscode = "$(document).ready(function () {\n";
			foreach($this->footerjs as $script) {
				$jscode .= $script."\n";
			}
			$jscode .= "});\n";
			$hashjs = md5($jscode);
			if(!is_dir($this->params['cachedir'].'/js')) {
				SeedDMS_Core_File::makeDir($this->params['cachedir'].'/js');
			}
			if(is_dir($this->params['cachedir'].'/js')) {
				file_put_contents($this->params['cachedir'].'/js/'.$hashjs.'.js', $jscode);
			}
			parse_str($_SERVER['QUERY_STRING'], $tmp);
			$tmp['action'] = 'footerjs';
			$tmp['hash'] = $hashjs;
			echo '<script src="/out/out.'.$this->params['class'].'.php?'.http_build_query($tmp).'"></script>'."\n";
		}
		if(method_exists($this, 'js')) {
			parse_str($_SERVER['QUERY_STRING'], $tmp);
			$tmp['action'] = 'js';
			echo '<script src="../out/out.'.$this->params['class'].'.php?'.http_build_query($tmp).'"></script>'."\n";
		}
		echo "</body>\n</html>\n";
	} /* }}} */

	function footerjs() { /* {{{ */
		header('Content-Type: application/javascript');
		if(file_exists($this->params['cachedir'].'/js/'.$_GET['hash'].'.js')) {
			readfile($this->params['cachedir'].'/js/'.$_GET['hash'].'.js');
		}
	} /* }}} */

	function missingḺanguageKeys() { /* {{{ */
		global $MISSING_LANG, $LANG;
		if($MISSING_LANG) {
			echo '<div class="container-fluid">'."\n";
			echo '<div class="row-fluid">'."\n";
			echo '<div class="alert alert-error">'."\n";
			echo "<p><strong>This page contains missing translations in the selected language. Please help to improve SeedDMS and provide the translation.</strong></p>";
			echo "</div>";
			echo "<table class=\"table table-condensed\">";
			echo "<tr><th>Key</th><th>engl. Text</th><th>Your translation</th></tr>\n";
			foreach($MISSING_LANG as $key=>$lang) {
				echo "<tr><td>".$key."</td><td>".(isset($LANG['en_GB'][$key]) ? $LANG['en_GB'][$key] : '')."</td><td><div class=\"input-append send-missing-translation\"><input name=\"missing-lang-key\" type=\"hidden\" value=\"".$key."\" /><input name=\"missing-lang-lang\" type=\"hidden\" value=\"".$lang."\" /><input type=\"text\" class=\"input-xxlarge\" name=\"missing-lang-translation\" placeholder=\"Your translation in '".$lang."'\"/><a class=\"btn\">Submit</a></div></td></tr>";
			}
			echo "</table>";
			echo "<div class=\"splash\" data-type=\"error\" data-timeout=\"5500\"><b>There are missing translations on this page!</b><br />Please check the bottom of the page.</div>\n";
			echo "</div>\n";
			echo "</div>\n";
		}
	} /* }}} */

	function footNote() { /* {{{ */
		echo '<footer class="main-footer">';

		if ($this->params['printdisclaimer']){
			echo "<div class=\"\">".getMLText("disclaimer")."</div>";
		}

		if (isset($this->params['footnote']) && strlen((string)$this->params['footnote'])>0) {
			echo "<div class=\"footnote\"><strong>".(string)$this->params['footnote']."</strong></div>";
		}

		echo "</footer>";


	} /* }}} */

	function startLoginContent(){
		echo "<div class=\"login-box\">
  	<div class=\"login-logo\">
    <a href=\"#\"><b><img class=\"login-brand\" src=".$this->getBrand()."></b></a>
  	</div>
		<div class=\"login-box-body\">
    <p class=\"login-box-msg\"></p>";
	}

	function endLoginContent(){
		echo "</div></div>";
		echo "<script src=\"/styles/".$this->theme."/bootstrap/js/bootstrap.min.js\"></script>";
		echo "<script src=\"/styles/".$this->theme."/plugins/slimScroll/jquery.slimscroll.min.js\"></script>";
		echo "<script src=\"/styles/".$this->theme."/plugins/iCheck/icheck.min.js\"></script>";
		if(method_exists($this, 'js')) {
			parse_str($_SERVER['QUERY_STRING'], $tmp);
			$tmp['action'] = 'js';
			echo '<script src="../out/out.'.$this->params['class'].'.php?'.http_build_query($tmp).'"></script>'."\n";
		}
		echo "</body>\n</html>\n";
	}

	function containerStart() { /* {{{ */
		echo "<div class=\"wrapper\">\n";
	} /* }}} */

	function contentStart() { /* {{{ */
		echo "<div class=\"content-wrapper\">\n";
	} /* }}} */

	function contentEnd() { /* {{{ */
		echo "<div>\n";
	} /* }}} */

	function containerEnd() { /* {{{ */
		echo "</div>\n";
	} /* }}} */

	function startBoxPrimary($title = "") { /* {{{ */
		echo "<div class=\"box box-primary\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";
	} /* }}} */

	function startCalendarBox($title = "", $pagination = null){ /* {{{ */
		echo "<div class=\"box box-success\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";

    if ($pagination != null) {
    	echo $pagination;	
    }
    
    echo "<div class=\"pull-right\">";
		echo "<div class=\"btn-group\">";

		$currDate = time();
		$year = (int)date("Y", $currDate);
		$month = (int)date("m", $currDate);
		$day = (int)date("d", $currDate);

		echo "<a type=\"button\" class=\"btn btn-info btn-sm btn-flat\" href=\"/out/out.AddEvent.php\"><i class=\"fa fa-calendar-plus-o\"></i> ".getMLText("add_event")."</a>";
		echo "<a type=\"button\" class=\"btn btn-success btn-sm btn-flat\" href=\"/out/out.Calendar.php?mode=y\"><i class=\"fa fa-calendar\"></i> ".$year."</a>";
		echo "<a type=\"button\" class=\"btn btn-success btn-sm btn-flat\" href=\"/out/out.Calendar.php?mode=m\"><i class=\"fa fa-calendar\"></i> ".$month."</a>";
		echo "<a type=\"button\" class=\"btn btn-success btn-sm btn-flat\" href=\"/out/out.Calendar.php?mode=w\"><i class=\"fa fa-calendar\"></i> ".$day."</a>";
		echo "</div>";
		echo "</div>";

    echo "</div>";
    echo "<div class=\"box-body box-calendar-background\">";
	} /* }}} */

	function endsCalendarBox(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function startBoxDanger($title = "") { /* {{{ */
		echo "<div class=\"box box-danger\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";
	} /* }}} */

	function startBoxSuccess($title = "") { /* {{{ */
		echo "<div class=\"box box-success\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";
	} /* }}} */

	function endsBoxPrimary(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function endsBoxDanger(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function endsBoxSuccess(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function startBoxCollapsablePrimary($title = "", $collapsed = "", $id = "") { /* {{{ */
		if ($id != "") {
			echo "<div class=\"box box-primary box-solid ".$collapsed."\" id=\"".$id."\">";
		} else {
			echo "<div class=\"box box-primary box-solid ".$collapsed."\">";
		}
		
    echo "<div class=\"box-header with-border\">";

    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "<div class=\"box-tools pull-right\">";
    if ($collapsed != "") {
    	echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"collapse\"><i class=\"fa fa-plus\"></i>";
    } else {
    	echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"collapse\"><i class=\"fa fa-minus\"></i>";
    }
    
    echo "</button>";
    echo "</div>";
    echo "</div>";
    if ($collapsed != "") {
    	echo "<div class=\"box-body\" style=\"display:none;\">";	
    } else {
    	echo "<div class=\"box-body\">";
    }
    
	} /* }}} */

	function startBoxCollapsableSuccess($title = "") { /* {{{ */
		echo "<div class=\"box box-success box-solid\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "<div class=\"box-tools pull-right\">";
    echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"collapse\"><i class=\"fa fa-minus\"></i>";
    echo "</button>";
    echo "</div>";
    echo "</div>";
    echo "<div class=\"box-body\">";
	} /* }}} */

	function startBoxCollapsableInfo($title = "") { /* {{{ */
		echo "<div class=\"box box-info box-solid\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "<div class=\"box-tools pull-right\">";
    echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"collapse\"><i class=\"fa fa-minus\"></i>";
    echo "</button>";
    echo "</div>";
    echo "</div>";
    echo "<div class=\"box-body\">";
	} /* }}} */

	function startBoxSolidSuccess($title = "") { /* {{{ */
		echo "<div class=\"box box-success box-solid\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";
	} /* }}} */

	function endsBoxSolidSuccess(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function startBoxSolidPrimary($title = "") { /* {{{ */
		echo "<div class=\"box box-primary box-solid\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".$title."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";
	} /* }}} */

	function endsBoxSolidPrimary(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function endsBoxCollapsablePrimary(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function endsBoxCollapsableSuccess(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function endsBoxCollapsableInfo(){ /* {{{ */
		echo "</div>";
    echo "</div>";
	} /* }}} */

	function startBoxRemovablePrimary($title = "") {
		echo "<div class=\"box box-primary\">";
	  echo "<div class=\"box-header with-border\">";
	  echo "<h3 class=\"box-title\">".$title."</h3>";
	  echo "<div class=\"box-tools pull-right\">";
	  echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\"><i class=\"fa fa-times\"></i></button>";
	  echo "</div>";
	  echo "</div>";
	  echo "<div class=\"box-body\">";
  } /* }}} */

  function endsBoxRemovablePrimary(){ /* {{{ */
  	echo "</div>";
    echo "</div>";
  } /* }}} */

	function globalBanner() { /* {{{ */
		echo "<div class=\"navbar navbar-default navbar-fixed-top\">\n";
		echo " <div class=\"navbar-inner\">\n";
		echo "  <div class=\"container-fluid\">\n";
		echo "   <a class=\"brand\" href=\"/out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."&showtree=1\">".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS")."</a>\n";
		echo "  </div>\n";
		echo " </div>\n";
		echo "</div>\n";
	} /* }}} */

	/**
	 * Returns the html needed for the clipboard list in the menu
	 *
	 * This function renders the clipboard in a way suitable to be
	 * used as a menu
	 *
	 * @param array $clipboard clipboard containing two arrays for both
	 *        documents and folders.
	 * @return string html code
	 */
	function menuClipboard($clipboard) { /* {{{ */
		if ($this->params['user']->isGuest() || (count($clipboard['docs']) + count($clipboard['folders'])) == 0) {
			return '';
		}
		$content = '';
		$content .= "   <ul id=\"main-menu-clipboard\" class=\"nav pull-right\">\n";
		$content .= "    <li class=\"dropdown add-clipboard-area\">\n";
		$content .= "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" class=\"add-clipboard-area\">".getMLText('clipboard')." (".count($clipboard['folders'])."/".count($clipboard['docs']).") <i class=\"icon-caret-down\"></i></a>\n";
		$content .= "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		foreach($clipboard['folders'] as $folderid) {
			if($folder = $this->params['dms']->getFolder($folderid))
				$content .= "    <li><a href=\"/out/out.ViewFolder.php?folderid=".$folder->getID()."\"><i class=\"icon-folder-close-alt\"></i> ".htmlspecialchars($folder->getName())."</a></li>\n";
		}
		foreach($clipboard['docs'] as $docid) {
			if($document = $this->params['dms']->getDocument($docid))
				$content .= "    <li><a href=\"/out/out.ViewDocument.php?documentid=".$document->getID()."\"><i class=\"icon-file\"></i> ".htmlspecialchars($document->getName())."</a></li>\n";
		}
		$content .= "    <li class=\"divider\"></li>\n";
		if(isset($this->params['folder']) && $this->params['folder']->getAccessMode($this->params['user']) >= M_READWRITE) {
			$content .= "    <li><a href=\"/op/op.MoveClipboard.php?targetid=".$this->params['folder']->getID()."&refferer=".urlencode($this->params['refferer'])."\">".getMLText("move_clipboard")."</a></li>\n";
		}
//		$content .= "    <li><a href=\"/op/op.ClearClipboard.php?refferer=".urlencode($this->params['refferer'])."\">".getMLText("clear_clipboard")."</a><a class=\"ajax-click\" data-href=\"/op/op.Ajax.php\" data-param1=\"command=clearclipboard\">kkk</a> </li>\n";
		$content .= "    <li><a class=\"ajax-click\" data-href=\"/op/op.Ajax.php\" data-param1=\"command=clearclipboard\">".getMLText("clear_clipboard")."</a></li>\n";
		$content .= "     </ul>\n";
		$content .= "    </li>\n";
		$content .= "   </ul>\n";
		return $content;
	} /* }}} */
 
	function mainHeaderForLoginError(){ /* {{{ */
		$sitename = trim(strip_tags($this->params['sitename']));

		echo "<header class=\"main-header\">";
    echo "<a href=\"/out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\" class=\"logo\">";
    echo "<!-- mini logo for sidebar mini 50x50 pixels -->";
    echo "<span class=\"logo-mini\"><b><img class=\"header-logo\" src=".$this->getLogo()."></b></span>"; // TODO: change for mini logo 
    echo "<!-- logo for regular state and mobile devices -->";
    echo "<span class=\"logo-lg\"><b><img class=\"header-brand\" src=".$this->getBrand()."></b></span>";
    echo "</a>";

    echo "<!-- Header Navbar -->";
    echo "<nav class=\"navbar navbar-static-top\" role=\"navigation\">";
    echo "<!-- Sidebar toggle button-->";
    echo "<a href=\"#\" class=\"sidebar-toggle\" data-toggle=\"offcanvas\" role=\"button\">";
    echo "<span class=\"sr-only\"></span>";
    echo "</a>";
    echo "</nav>";
  	echo "</header>";
	} /* }}} */

	function mainHeader(){ /* {{{ */
		$sitename = trim(strip_tags($this->params['sitename']));

		echo "<header class=\"main-header\">";
    echo "<a href=\"/out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\" class=\"logo\">";
    echo "<!-- mini logo for sidebar mini 50x50 pixels -->";
    echo "<span class=\"logo-mini\"><img class=\"header-logo\" src=".$this->getLogo()."></span>"; // TODO: change for mini logo 
    echo "<!-- logo for regular state and mobile devices -->";
    echo "<span class=\"logo-lg\"><img class=\"header-brand\" src=".$this->getBrand()."></span>";
    echo "</a>";

    echo "<!-- Header Navbar -->";
    echo "<nav class=\"navbar navbar-static-top\" role=\"navigation\">";
    echo "<!-- Sidebar toggle button-->";
    echo "<a href=\"#\" class=\"sidebar-toggle\" data-toggle=\"offcanvas\" role=\"button\">";
    echo "<span class=\"sr-only\"></span>";
    echo "</a>";

    echo "<!-- Navbar Right Menu -->";
    echo "<div class=\"navbar-custom-menu\">";
    echo "<ul class=\"nav navbar-nav\">";
    echo "<!-- Messages: style can be found in dropdown.less-->";
    /*echo "<li class=\"dropdown messages-menu\">";
    echo "<!-- Menu toggle button -->";
    echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">";
    echo "<i class=\"fa fa-envelope-o\"></i>";
    echo "<span class=\"label label-success\">4</span>";
    echo "</a>";
    echo "<ul class=\"dropdown-menu\">";
    echo "<li class=\"header\">You have 4 messages</li>";
    echo "<li>";
    echo "<!-- inner menu: contains the messages -->";
    echo "<ul class=\"menu\">";
    echo "<li><!-- start message -->";
    echo "<a href=\"#\">";
    echo "<div class=\"pull-left\">";
    echo "<!-- User Image -->";
    // Get user image
   	if($this->params['user']->hasImage()) {
    	echo "<img class=\"img-circle\" src=\"/out/out.UserImage.php?userid=".$this->params['user']->getId()."\">";
  	} else {
  		echo "<img class=\"img-circle\" src=\"/views/".$this->theme."/images/user-default.png\" alt=\"User Image\">";
  	}
    echo "</div>";
    echo "<!-- Message title and timestamp -->";
    echo "<h4>";
    echo "Support Team";
    echo "<small><i class=\"fa fa-clock-o\"></i> 5 mins</small>";
    echo "</h4>";
    echo "<!-- The message -->";
    echo "<p>Why not buy a new awesome theme?</p>";
    echo "</a>";
    echo "</li>";
    echo "<!-- end message -->";
    echo "</ul>";
    echo "<!-- /.menu -->";
    echo "</li>";
    echo "<li class=\"footer\"><a href=\"#\">See All Messages</a></li>";
    echo "</ul>";
    echo "</li>";
    echo "<!-- /.messages-menu -->";
		
    echo "<!-- Notifications Menu -->";
    echo "<li class=\"dropdown notifications-menu\">";
    echo "<!-- Menu toggle button -->";
    echo "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">";
    echo "<i class=\"fa fa-bell-o\"></i>";
    echo "<span class=\"label label-warning\">10</span>";
    echo "</a>";
    echo "<ul class=\"dropdown-menu\">";
    echo "<li class=\"header\">You have 10 notifications</li>";
    echo "<li>";
    echo "<!-- Inner Menu: contains the notifications -->";
    echo "<ul class=\"menu\">";
    echo "<li><!-- start notification -->";
    echo "<a href=\"#\">";
    echo "<i class=\"fa fa-users text-aqua\"></i> 5 new members joined today";
    echo "</a>";
    echo "</li>";
    echo "<!-- end notification -->";
    echo "</ul>";
    echo "</li>";
    echo "<li class=\"footer\"><a href=\"#\">View all</a></li>";
    echo "</ul>";
    echo "</li>";
    */

    if($this->params['enablelanguageselector']) {

				echo "<!-- Languages Menu -->";
		    echo "<li class=\"dropdown tasks-menu\">";
		    echo "<!-- Menu Toggle Button -->";
		    echo "<a href=\"#\" class=\"dropdown-toggle a-fix-height\" data-toggle=\"dropdown\">";
		    echo "<i class=\"fa fa-flag-o fix-padding\"></i>";

		    if ($this->params['session']->getLanguage() == "es_ES") {
		    	echo "<span class=\"label label-info\">es</span>";	
		    } else if ($this->params['session']->getLanguage() == "en_GB") {
		    	echo "<span class=\"label label-info\">en</span>";
		    }
		    
		    echo "</a>";
		    echo "<ul class=\"dropdown-menu\">";
		    echo "<li class=\"header\">".getMLText("settings_available_languages")."</li>";
		    echo "<li>";
		    echo "<!-- Inner menu: contains languages -->";

		    echo "<ul class=\"my-menu\">";

				$languages = getLanguages();
				$langCount = 0;
				foreach ($languages as $currLang) {
					if($this->params['session']->getLanguage() == $currLang)
						echo "<li class=\"language-active\">";
					else
						echo "<li>";

					echo "<a href=\"/op/op.SetLanguage.php?lang=".$currLang."&referer=".$_SERVER["REQUEST_URI"]."\">";
					echo "<div class=\"my-menu-body\">".getMLText($currLang);
					//echo "<img class=\"pull-right\" src=\"".$this->getCountryFlag($languages[$langCount])."\" ></img>";
					
			    echo "</div>";
			    echo "</a>";
			    echo "</li>";
			    $langCount++;
				}
				echo "</ul>\n";
				echo "</li>\n";

			echo "</ul>\n";
			echo "</li>\n";
		}

		

    echo "<!-- User Account Menu -->";
    echo "<li class=\"dropdown user user-menu\">";
    echo "<!-- Menu Toggle Button -->";
    echo "<a href=\"#\" class=\"dropdown-toggle a-fix-height\" data-toggle=\"dropdown\">";
    echo "<!-- The user image in the navbar-->";
    
    // Get user image
   	if($this->params['user']->hasImage()) {
    	echo "<img class=\"img-nav-mini img-circle\" src=\"/out/out.UserImage.php?userid=".$this->params['user']->getId()."\"> ";
  	} else {
  		echo "<img class=\"user-image\" src=\"/views/".$this->theme."/images/user-default.png\" alt=\"User Image\"> ";
  	}

    echo "<!-- hidden-xs hides the username on small devices so only the image appears. -->";
    $thename = $this->params['user']->getFullName();
    $firstname = explode(" ", $thename);

    echo "<span class=\"hidden-xs\">".$this->params['user']->getFullName()."</span>";
    echo "</a>";
    echo "<ul class=\"dropdown-menu\">";
    echo "<!-- The user image in the menu -->";
    echo "<li class=\"user-header\">";

    // Get user image
   	if($this->params['user']->hasImage()) {
    	echo "<img class=\"img-circle\" src=\"/out/out.UserImage.php?userid=".$this->params['user']->getId()."\">";
  	} else {
  		echo "<img class=\"img-circle\" src=\"/views/".$this->theme."/images/user-default.png\" alt=\"User Image\">";
  	}

    echo "<p>";
    echo $this->params['user']->getFullName();
    echo "<small>".$this->params['user']->getEmail()."</small>";
    echo "</p>";
    echo "</li>";

    echo "<li class=\"user-footer\">";
    echo "<div class=\"row\">";
    if ($this->params['user']->isAdmin()) {
	    echo "<div class=\"col-xs-3 text-center\">"; 
	    echo "<a class=\"btn btn-info btn-flat\" href=\"/out/out.MyAccount.php\" title=\"".getMLText("my_account")."\"><i class=\"fa fa-user\"></i></a>";
	    echo "</div>";

	    echo "<div class=\"col-xs-3 text-center\">";
	    echo "<a class=\"btn btn-success btn-flat\" href=\"/out/out.MyDocuments.php?inProcess=1\" title=\"".getMLText("my_documents")."\"><i class=\"fa fa-file\"></i></a>";
	    echo "</div>";

	    if(!$this->params['session']->getSu()) {
	    	echo "<div class=\"col-xs-3 text-center\">";
	    	echo "<a class=\"btn btn-primary btn-flat\" href=\"/out/out.SubstituteUser.php\" title=\"".getMLText("substitute_user")."\"><i class=\"fa fa-exchange\"></i></a>";
	    	echo "</div>";
	    }

    	if($this->params['session']->getSu()) {

				echo "<div class=\"col-xs-6 text-center\">";
    		echo "<a href=\"/op/op.ResetSu.php\" class=\"btn btn-danger btn-flat\" title=\"".getMLText("sign_out_user")."\"><i class=\"fa fa-sign-out\"></i></a>";
    		echo "</div>";

			} else {
				echo "<div class=\"col-xs-3 text-center\">";
    		echo "<a href=\"/op/op.Logout.php\" class=\"btn btn-danger btn-flat\" title=\"".getMLText("sign_out")."\"><i class=\"fa fa-sign-out\"></i></a>";
    		echo "</div>";

			}

    } else {

    	if (!$this->params['user']->isGuest()) {
    		echo "<div class=\"col-xs-4 text-center\">"; 
		    echo "<a class=\"btn btn-info btn-flat\" href=\"/out/out.MyAccount.php\" title=\"".getMLText("my_account")."\"><i class=\"fa fa-user\"></i></a>";
		    echo "</div>";

		    echo "<div class=\"col-xs-4 text-center\">";
		    echo "<a class=\"btn btn-success btn-flat\" href=\"/out/out.MyDocuments.php?inProcess=1\" title=\"".getMLText("my_documents")."\"><i class=\"fa fa-file\"></i></a>";
		    echo "</div>";
    	}

	    if($this->params['session']->getSu()) {

				echo "<div class=\"col-xs-4 text-center\">";
    		echo "<a href=\"/op/op.ResetSu.php\" class=\"btn btn-danger btn-flat\" title=\"".getMLText("sign_out_user")."\"><i class=\"fa fa-sign-out\"></i></a>";
    		echo "</div>";

			} else {
				echo "<div class=\"col-xs-4 text-center\">";
    		echo "<a href=\"/op/op.Logout.php\" class=\"btn btn-danger btn-flat\" title=\"".getMLText("sign_out")."\"><i class=\"fa fa-sign-out\"></i></a>";
    		echo "</div>";

			}

    }

    echo "<!-- Menu Footer-->";
    echo "</div>";
    echo "<!-- /.row -->";
    echo "</li>";
    echo "</ul>";
    echo "</li>";


    echo "<!-- Control Sidebar Toggle Button -->";
    if ($this->params['user']->isAdmin()) {
    	echo "<li>";
	    echo "<a href=\"#\" data-toggle=\"control-sidebar\" class=\"a-fix-height\"><i class=\"fa fa-gears\"></i></a>";
	    echo "</li>";
    } else {
    	echo "<li>";
	    echo "<a href=\"#\" data-toggle=\"control-sidebar\" class=\"a-fix-height\"><i class=\"fa fa-wrench\"></i></a>";
	    echo "</li>";
    }
    
    echo "</ul>";
    echo "</div>";
    echo "</nav>";
  	echo "</header>";


	} /* }}} */

	/* Generate folder tree widget */

	function printTheTree($tree, $i = 0, $folder){ /* {{{ */

			foreach ($tree as $key => $treeNode) {
				
					if ($i == 0 && $folder != 0) {
						echo "<li class=\"treeview active\">";
						echo "<a href=\"/out/out.ViewFolder.php?folderid=".$treeNode['id']."\" class=\"link-to-folder\"><i class=\"fa fa-folder-open\"></i></a>";
					} else {
						echo "<li class=\"treeview\">";
						echo "<a href=\"/out/out.ViewFolder.php?folderid=".$treeNode['id']."\" class=\"link-to-folder\"><i class=\"fa fa-folder-open\"></i></a>";
					}
				
			  echo "<a href=\"#\" class=\"fix-width\"><i class=\"fa fa-folder\"></i> <span class=\"wrap-normal\">".$treeNode['label']." (".count($treeNode['children']).") </span>";

			  if (count($treeNode['children']) > 0) {
			  	echo "<span class=\"pull-right-container\">";
			  	echo "<i class=\"\"></i>";
			  	echo "</span>";	
			  }
			  echo "</a>";

				if (count($treeNode['children']) > 0) {
					$children = $treeNode['children'];
			    echo "<ul class=\"treeview-menu\">";
			    $i++;
					$this->printTheTree($children, $i,$folder);
					echo "</ul>";
				}
			  echo "</li>";
			}

	} /* }}} */

	function mainSideBar($folder = 0, $nonconfo = 0, $calendar = 0){ /* {{{ */
		echo "<aside class=\"main-sidebar\">";
    echo "<section class=\"sidebar\">";

    echo "<!-- Sidebar user panel (optional) -->";
    echo "<div class=\"user-panel\">";
    echo "<div class=\"pull-left image\">";

    // Get user image
   	if($this->params['user']->hasImage()) {
    	echo "<img class=\"img-circle\" src=\"/out/out.UserImage.php?userid=".$this->params['user']->getId()."\">";
  	} else {
  		echo "<img class=\"img-circle\" src=\"/views/".$this->theme."/images/user-default.png\" alt=\"User Image\">";
  	}

    echo "</div>";
    echo "<div class=\"pull-left info\">";
    $thename = $this->params['user']->getFullName();
    $firstname = explode(" ", $thename);
    echo "<p>".$firstname[0]."</p>";
    echo "<!-- Status -->";
    echo "<a href=\"#\"><i class=\"fa fa-circle text-success\"></i> Online</a>";
    echo "</div>";
    echo "</div>";

    ?>
    	<form class="sidebar-form" action="../out/out.Search.php" method="get" name="form1">
    	<div class="input-group">
			<input type="text" name="query" class="form-control" placeholder="<?php echo getMLText("search"); ?>">
			<input type="hidden" name="mode" value="1">
			<input type="hidden" name="ownerid" value="-1">
			<input type="hidden" name="resultmode" value="3">
			<input type="hidden" name="targetid" value="1">
			<input type="hidden" name="targetnameform1" value="">
			<span class="input-group-btn">
			<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
			</span>
    	</div>
			</form>
    <?php


    // Original search
    /*echo "<!-- search form (Optional) -->";
    echo "<form action=\"#\" method=\"get\" class=\"sidebar-form\">";
    echo "<div class=\"input-group\">";
    echo "<input type=\"text\" name=\"q\" class=\"form-control\" placeholder=\"".getMLText("search")."\">";
    echo "<span class=\"input-group-btn\">";
    echo "<button type=\"submit\" name=\"search\" id=\"search-btn\" class=\"btn btn-flat\"><i class=\"fa fa-search\"></i>";
    echo "</button>";
    echo "</span>";
    echo "</div>";
    echo "</form>";
    echo "<!-- /.search form -->";*/
    /////////////////////////////////////

    echo "<!-- Sidebar Menu -->";
    echo "<ul class=\"sidebar-menu\">";
    echo "<li class=\"header\">".getMLText("tools")."</li>";

		// View tree

		$rootFolder = $this->printTree(1, M_READ, 0,'', 1, 's');

		$this->printTheTree($rootFolder, 0, $folder);

    //TreeView Old 

		/*echo "<li class=\"treeview active\">";
    echo "<a href=\"#\"><i class=\"fa fa-sitemap\"></i> <span>".getMLText("folderTree")."</span>";
    echo "<span class=\"pull-right-container\">";
    echo "<i class=\"fa fa-angle-left pull-right\"></i>";
    echo "</span>";
    echo "</a>";
    echo "<ul class=\"treeview-menu menu-open\">";
    echo "<li>";
    echo "<a>";
    $this->printNewTreeNavigationHTML($this->params['rootfolderid'], M_READ, 0, '', 2, "");
    echo "</a>";
		echo "</li>";
    echo "</ul>";
    echo "</li>";*/

  	// Calendar
  	$calendarWeekActive = "";
  	$calendarMonthActive = "";
  	$calendarYearActive = "";
    $addEventActive = "";
    if ($calendar != 0) {
    	if (isset($_GET['mode'])) {
    		switch ($_GET['mode']) {
		    	case "w":
		    		$calendarWeekActive = "active";
		    		break;
		    	case "m": 
		    		$calendarMonthActive = "active";
		    		break;
		    	case "y":
		    		$calendarYearActive = "active";
		    		break;
    		}	
    	}

    	if ($calendar == 2) {
    		$addEventActive = "active";
    	}
    }

    
    if ($this->params['enablecalendar'] && !$this->params['user']->isGuest()){
    	if ($calendar != 0) {
    		echo "<li class=\"treeview active\">";	
    	} else {
    		echo "<li class=\"treeview\">";
    	}

	    echo "<a href=\"#\"><i class=\"fa fa-calendar\"></i> <span>".getMLText("calendar")."</span>";
	    echo "<span class=\"pull-right-container\">";
	    echo "<i class=\"fa fa-angle-left pull-right\"></i>";
	    echo "</span>";
	    echo "</a>";
	    echo "<ul class=\"treeview-menu\">";
	    echo "<li class=\"".$calendarWeekActive."\"><a href=\"/out/out.Calendar.php?mode=w\">".getMLText("week_view")."</a></li>";
	    echo "<li class=\"".$calendarMonthActive."\"><a href=\"/out/out.Calendar.php?mode=m\">".getMLText("month_view")."</a></li>";
	    echo "<li class=\"".$calendarYearActive."\"><a href=\"/out/out.Calendar.php?mode=y\">".getMLText("year_view")."</a></li>";
	    echo "<li class=\"".$addEventActive."\"><a href=\"/out/out.AddEvent.php\">".getMLText("add_event")."</a></li>";
	    echo "</ul>";
	    echo "</li>";
	  }

	  // Help
	  if($this->params['enablehelp']) {
			$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
			echo "<li><a href=\"/out/out.Help.php?context=".$tmp[1]."\"><i class=\"fa fa-info-circle\"></i> <span>".getMLText("help")."</span></a></li>";
		}

    echo "</ul>";
    echo "<!-- /.sidebar-menu -->";
    echo "</section>";
    echo "<!-- /.sidebar -->";
  	echo "</aside>";
	} /* }}} */

	function mainFooter($nofooter=false){ /* {{{ */
		if(!$nofooter) {
			$this->footNote();
			if($this->params['showmissingtranslations']) {
				$this->missingḺanguageKeys();
			}
		}

		//if ($this->params['user']->isAdmin()) {
		$this->controlSideBar();
		//}
	} /* }}} */

	function controlSideBar(){ /* {{{ */
		echo "<aside class=\"control-sidebar control-sidebar-dark aside-fixed\">";
		echo "<ul class=\"nav nav-tabs nav-justified control-sidebar-tabs\">";
		echo "<li class=\"active\"><a href=\"#control-sidebar-theme-demo-options-tab\" data-toggle=\"tab\"><i class=\"fa fa-paint-brush\"></i></a></li>";
		if($this->params['user']->isAdmin()) {
			echo "<li><a href=\"#control-sidebar-home-tab\" data-toggle=\"tab\"><i class=\"fa fa-gears\"></i></a></li>";
			echo "<li><a href=\"#control-sidebar-logo-tab\" data-toggle=\"tab\"><i class=\"fa fa-flag\"></i></a></li>";
		}
    echo "</ul>";
    echo "<!-- Sidebar Menu -->";
    echo "<div class=\"tab-content\">";

    if($this->params['user']->isAdmin()) {
    echo "<div class=\"tab-pane\" id=\"control-sidebar-home-tab\">";
    echo "<a type=\"button\" href=\"/out/out.AdminTools.php\"><h3 class=\"control-sidebar-heading btn-admin-tools\">".getMLText("admin_tools")."</h3></a>";
    echo "<ul class=\"control-sidebar-menu\">";

    if ($this->params['user']->_comment != "client-admin") {
    echo "<li><a href=\"/out/out.UsrMgr.php\"><i class=\"menu-icon fa fa-user bg-green\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("user_management")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.GroupMgr.php\"><i class=\"menu-icon fa fa-users bg-green\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("group_management")."</h4></div></a></li>";
  	}

    echo "<li><a href=\"/out/out.BackupTools.php\"><i class=\"menu-icon fa fa-hdd-o bg-green\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("backup_tools")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.LogManagement.php\"><i class=\"menu-icon fa fa-list bg-green\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("log_management")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.DefaultKeywords.php\"><i class=\"menu-icon fa fa-bars bg-green\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("global_default_keywords")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.Categories.php\"><i class=\"menu-icon fa fa-columns bg-light-blue\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("global_document_categories")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.AttributeMgr.php\"><i class=\"menu-icon fa fa-tags bg-light-blue\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("global_attributedefinitions")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.WorkflowMgr.php\"><i class=\"menu-icon fa fa-sitemap bg-light-blue\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("global_workflows")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.WorkflowStatesMgr.php\"><i class=\"menu-icon fa fa-star-o bg-light-blue\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("global_workflow_states")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.WorkflowActionsMgr.php\"><i class=\"menu-icon fa fa-bolt bg-light-blue\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("global_workflow_actions")."</h4></div></a></li>";
    
    if($this->params['enablefullsearch']) {
    echo "<li><a href=\"/out/out.Indexer.php\"><i class=\"menu-icon fa fa-refresh bg-yellow\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("update_fulltext_index")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.CreateIndex.php\"><i class=\"menu-icon fa fa-search bg-yellow\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("create_fulltext_index")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.IndexInfo.php\"><i class=\"menu-icon fa fa-info bg-yellow\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("fulltext_info")."</h4></div></a></li>";
  	}

    echo "<li><a href=\"/out/out.Statistic.php\"><i class=\"menu-icon fa fa-tasks bg-yellow\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("folders_and_documents_statistic")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.Charts.php\"><i class=\"menu-icon fa fa-pie-chart bg-yellow\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("charts")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.ObjectCheck.php\"><i class=\"menu-icon fa fa-check-circle-o bg-red\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("objectcheck")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.Timeline.php\"><i class=\"menu-icon fa fa-clock-o bg-red\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("timeline")."</h4></div></a></li>";

    if ($this->params['user']->_comment != "client-admin") {
    echo "<li><a href=\"/out/out.Settings.php\"><i class=\"menu-icon fa fa-wrench bg-red\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("settings")."</h4></div></a></li>";
    echo "<li><a href=\"/out/out.ExtensionMgr.php\"><i class=\"menu-icon fa fa-cogs bg-red\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("extension_manager")."</h4></div></a></li>";
  	}

    echo "<li><a href=\"/out/out.Info.php\"><i class=\"menu-icon fa fa-info-circle bg-red\"></i>";
    echo "<div class=\"menu-info\"><h4 class=\"control-sidebar-subheading\">".getMLText("version_info")."</h4></div></a></li>";
    echo "</ul>";
    echo "<!-- /.sidebar-menu -->";
    echo "<!-- /.tab-pane -->";
    echo "</div>";
  	}
  	?>

  	<div id="control-sidebar-theme-demo-options-tab" class="tab-pane active">
  		<div>
  			<h4 class="control-sidebar-heading">Skins</h4>
  			<ul class="list-unstyled clearfix"><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-blue" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span><span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin">Blue</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-black" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span><span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #222;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin">Black</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-purple" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span><span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin">Purple</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-green" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin">Green</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-red" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin">Red</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-yellow" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span><span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin">Yellow</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-blue-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span><span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin" style="font-size: 12px">Blue Light</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-black-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span><span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin" style="font-size: 12px">Black Light</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-purple-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span><span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin" style="font-size: 12px">Purple Light</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-green-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin" style="font-size: 12px">Green Light</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-red-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin" style="font-size: 12px">Red Light</p></li><li style="float:left; width: 33.33333%; padding: 5px;"><a href="javascript:void(0);" data-skin="skin-yellow-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover"><div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span><span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span></div><div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a><p class="text-center no-margin" style="font-size: 12px;">Yellow Light</p></li></ul>
  		</div>
  	</div>
  	<?php if($this->params['user']->isAdmin()) { ?>
  	<div class="tab-pane" id="control-sidebar-logo-tab">
    	<h3 class="control-sidebar-heading">Logo</h3>
    	<div>
    		<ul class="list-unstyled clearfix">
    		<?php
    			$path_to_file_logo = $this->getLogo();
    			?>
    				<li class="align-center li-logo"><img class="thelogo" data-toggle="tooltip" data-placement="bottom" title="<?php echo getMLText("img_logo_recomendation"); ?>" src="<?php echo $path_to_file_logo; ?>"></li>
    				<li>
    					<form enctype="multipart/form-data" method="post" id="formupload1" name="formupload1" action="<?php echo "/views/".$this->theme."/validate.php"; ?>">
    					<input type="hidden" name="command" value="validatelogo" />

	    					<?php $this->printLogoChooser("logofile", false); ?>
						 	</form>
    				</li>
    		</ul>
    	</div>
    	<h3 class="control-sidebar-heading">Brand</h3>
    	<div>
    		<ul class="list-unstyled clearfix">
    		<?php
    			$path_to_file_brand = $this->getBrand();
    			?>
    				<li class="align-center li-logo"><img class="thebrand" data-toggle="tooltip" data-placement="bottom" title="<?php echo getMLText("img_brand_recomendation"); ?>" src="<?php echo $path_to_file_brand; ?>"></li>
    				<li>
    					<form enctype="multipart/form-data" method="post" id="formupload2" name="formupload2" action="<?php echo "/views/".$this->theme."/validate.php"; ?>">
    					<input type="hidden" name="command" value="validatebrand" />
	    					<?php $this->printLogoChooser("brandfile", false); ?>
						 	</form>
    				</li>
    		</ul>
    	</div>
    	<h3 class="control-sidebar-heading">Cache</h3>
    	<div>
    	<ul class="list-unstyled clearfix">
    		<li class="align-center">
    		<form action="../../op/op.ClearCache.php" name="form1" method="post">
				<?php echo createHiddenFieldWithKey('clearcache'); ?>
				<input type="hidden" name="preview" value="1">
				<input type="hidden" name="js" value="1">
				<button type="submit" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php printMLText("clear_cache");?></button>
				</form>
				</li>
			</ul>
    	</div>
    </div>
  	<?php }
    echo "</div>";
  	echo "</aside>";
  	echo "<div class=\"control-sidebar-bg\"></div>";
	} /* }}} */

	function getLogo(){
		$path = "/images/multisis-lte/logo.png";
		return $path;
	}

	function getBrand(){
		$path = "/images/multisis-lte/brand.png";
		return $path;
	}

	function globalNavigation($folder=null) { /* {{{ */
		$dms = $this->params['dms'];
		echo "<div class=\"navbar navbar-default navbar-fixed-top\">\n";
		echo " <div class=\"navbar-inner\">\n";
		echo "  <div class=\"container-fluid\">\n";
		echo "   <a class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".nav-col1\">\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "   </a>\n";
		echo "   <a class=\"brand\" href=\"/out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\"><img src='/views/multisistemas/images/logo.png' alt=\"Multisistemas Logo\" /> ".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS")."</a>\n";
		if(isset($this->params['user']) && $this->params['user']) {
			echo "   <div class=\"nav-collapse nav-col1\">\n";
			echo "   <ul id=\"main-menu-admin\" class=\"nav pull-right\">\n";
			echo "    <li class=\"dropdown\">\n";
			echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".($this->params['session']->getSu() ? getMLText("switched_to") : getMLText("signed_in_as"))." '".htmlspecialchars($this->params['user']->getFullName())."' <i class=\"icon-caret-down\"></i></a>\n";
			echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
			if (!$this->params['user']->isGuest()) {
				$menuitems = array();
				$menuitems['my_documents'] = array('link'=>"/out/out.MyDocuments.php?inProcess=1", 'label'=>'my_documents');
				$menuitems['my_account'] = array('link'=>"/out/out.MyAccount.php", 'label'=>'my_account');
				$hookObjs = $this->getHookObjects('SeedDMS_View_Bootstrap');
				foreach($hookObjs as $hookObj) {
					if (method_exists($hookObj, 'userMenuItems')) {
						$menuitems = $hookObj->userMenuItems($this, $menuitems);
					}
				}
				if($menuitems) {
					foreach($menuitems as $menuitem) {
						echo "<li><a href=\"".$menuitem['link']."\">".getMLText($menuitem['label'])."</a></li>";
					}
					echo "    <li class=\"divider\"></li>\n";
				}
			}
			$showdivider = false;
			if($this->params['enablelanguageselector']) {
				$showdivider = true;
				echo "    <li class=\"dropdown-submenu\">\n";
				echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("language")."</a>\n";
				echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
				$languages = getLanguages();
				foreach ($languages as $currLang) {
					if($this->params['session']->getLanguage() == $currLang)
						echo "<li class=\"active\">";
					else
						echo "<li>";
					echo "<a href=\"/op/op.SetLanguage.php?lang=".$currLang."&referer=".$_SERVER["REQUEST_URI"]."\">";
					echo getMLText($currLang)."</a></li>\n";
				}
				echo "     </ul>\n";
				echo "    </li>\n";
			}
			if($this->params['user']->isAdmin()) {
				$showdivider = true;
				echo "    <li><a href=\"/out/out.SubstituteUser.php\">".getMLText("substitute_user")."</a></li>\n";
			}
			if($showdivider)
				echo "    <li class=\"divider\"></li>\n";
			if($this->params['session']->getSu()) {
				echo "    <li><a href=\"/op/op.ResetSu.php\">".getMLText("sign_out_user")."</a></li>\n";
			} else {
				echo "    <li><a href=\"/op/op.Logout.php\">".getMLText("sign_out")."</a></li>\n";
			}
			echo "     </ul>\n";
			echo "    </li>\n";
			echo "   </ul>\n";

			if($this->params['enableclipboard']) {
				echo "   <div id=\"menu-clipboard\">";
				echo $this->menuClipboard($this->params['session']->getClipboard());
				echo "   </div>";
			}

			echo "   <ul class=\"nav\">\n";

			//Link to non-conformities extension 

			if (!$this->params['user']->isGuest()) {
					echo "<li><a href=\"/ext/nonconfo/out/out.ViewAllNonConfo.php\">".getMLText("nonconfo")."</a></li>\n";
			}	
			
	//		echo "    <li id=\"first\"><a href=\"/out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".getMLText("content")."</a></li>\n";
	//		echo "    <li><a href=\"/out/out.SearchForm.php?folderid=".$this->params['rootfolderid']."\">".getMLText("search")."</a></li>\n";
			if ($this->params['enablecalendar']) echo "    <li><a href=\"/out/out.Calendar.php?mode=".$this->params['calendardefaultview']."\">".getMLText("calendar")."</a></li>\n";
			if ($this->params['user']->isAdmin()) echo "    <li><a href=\"/out/out.AdminTools.php\">".getMLText("admin_tools")."</a></li>\n";
			if($this->params['enablehelp']) {
			$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
			echo "    <li><a href=\"/out/out.Help.php?context=".$tmp[1]."\">".getMLText("help")."</a></li>\n";
			}
			echo "   </ul>\n";
			echo "     <form action=\"/out/out.Search.php\" class=\"form-inline navbar-search pull-left\" autocomplete=\"off\">";
			if ($folder!=null && is_object($folder) && !strcasecmp(get_class($folder), $dms->getClassname('folder'))) {
				echo "      <input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\" />";
			}
			echo "      <input type=\"hidden\" name=\"navBar\" value=\"1\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"1\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"2\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"3\" />";
			echo "      <input type=\"hidden\" name=\"searchin[]\" value=\"4\" />";
			echo "      <input name=\"query\" class=\"search-query\" id=\"searchfield\" data-provide=\"typeahead\" type=\"text\" style=\"width: 150px;\" placeholder=\"".getMLText("search")."\"/>";
			if($this->params['defaultsearchmethod'] == 'fulltext')
				echo "      <input type=\"hidden\" name=\"fullsearch\" value=\"1\" />";
			if($this->params['enablefullsearch']) {
				echo "      <label class=\"checkbox\" style=\"color: #999999;\"><input type=\"checkbox\" name=\"fullsearch\" value=\"1\" title=\"".getMLText('fullsearch_hint')."\"/> ".getMLText('fullsearch')."</label>";
			}
			echo "      <input type=\"submit\" value=\"".getMLText("search")."\" id=\"searchButton\" class=\"btn\"/>";
			echo "</form>\n";
			echo "    </div>\n";
		}
		echo "  </div>\n";
		echo " </div>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	function getFolderPathHTML($folder, $tagAll=false, $document=null) { /* {{{ */
		$path = $folder->getPath();
		$user = $this->params['user'];
		$txtpath = "";
		for ($i = 0; $i < count($path); $i++) {
			$txtpath .= "<li>";
			if ($i +1 < count($path)) {
				$txtpath .= "<a href=\"/out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\" rel=\"folder_".$path[$i]->getID()."\" class=\"table-row-folder\" formtoken=\"".createFormKey('movefolder')."\">".
					htmlspecialchars($path[$i]->getName())."</a>";
			}
			else {
				$txtpath .= ($tagAll ? "<a href=\"/out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\">".
										 htmlspecialchars($path[$i]->getName())."</a>" : htmlspecialchars($path[$i]->getName()));
			}
			//$txtpath .= " <span class=\"divider\">/</span></li>";
		}
		if($document)
			$txtpath .= "<li><a href=\"/out/out.ViewDocument.php?documentid=".$document->getId()."\">".htmlspecialchars($document->getName())."</a></li>";
		
		if($folder->getAccessMode($user) >= M_READWRITE) {
			$txtpath .= "<li class=\"pull-right breadcrumb-btn\"><a id=\"add-document\" type=\"button\" class=\"btn btn-warning btn-sm\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("add_document")."\"><i class=\"fa fa-plus\"></i> <i class=\"fa fa-file\"></i></a> </li>";
			$txtpath .= "<li class=\"pull-right breadcrumb-btn\"><a id=\"add-folder\" type=\"button\" class=\"btn btn-success btn-sm\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("add_subfolder")."\"><i class=\"fa fa-plus\"></i> <i class=\"fa fa-folder\"></i></a></li>";
				//$txtpath .= "<li><a href=\"/out/out.ViewDocument.php?documentid=".$document->getId()."\">".htmlspecialchars($document->getName())."</a></li>";
		}

		return '<ul class="breadcrumb default-bread">'.$txtpath.'</ul>';
	} /* }}} */

	function getDefaultFolderPathHTML($folder, $tagAll=false, $document=null) { /* {{{ */
		$path = $folder->getPath();
		$txtpath = "";
		for ($i = 0; $i < count($path); $i++) {
			$txtpath .= "<li>";
			if ($i +1 < count($path)) {
				$txtpath .= "<a href=\"/out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\" rel=\"folder_".$path[$i]->getID()."\" class=\"table-row-folder\" formtoken=\"".createFormKey('movefolder')."\">".
					htmlspecialchars($path[$i]->getName())."</a>";
			}
			else {
				$txtpath .= ($tagAll ? "<a href=\"/out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\">".
										 htmlspecialchars($path[$i]->getName())."</a>" : htmlspecialchars($path[$i]->getName()));
			}
			//$txtpath .= " <span class=\"divider\">/</span></li>";
		}
		if($document)
			$txtpath .= "<li><a href=\"/out/out.ViewDocument.php?documentid=".$document->getId()."\">".htmlspecialchars($document->getName())."</a></li>";

		return '<ul class="breadcrumb default-bread">'.$txtpath.'</ul>';
	} /* }}} */

	// Nonconfo extension Navigation Bar for Multisis-LTE
	function getNonconfoPathHTML() { /* {{{ */
		$txtpath = "";

		$txtpath .= "<li><a href=\"/ext/nonconfo/out/out.ViewAllNonConfo.php\" type=\"button\" class=\"btn btn-primary btn-sm\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("nonconfo")."\"><i class=\"fa fa-home\"></i></a></li>";

		if ($this->params['user']->isAdmin()) {
			$txtpath .= "<li class=\"pull-right breadcrumb-btn\"><a href=\"/ext/nonconfo/out/out.AddProcess.php\" type=\"button\" class=\"btn btn-success btn-sm\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("nonconfo_add_process")."\"><i class=\"fa fa-wrench\"></i></a></li>";
			$txtpath .= "<li class=\"pull-right breadcrumb-btn\"><a href=\"/ext/nonconfo/out/out.AddOwners.php\" type=\"button\" class=\"btn btn-warning btn-sm\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("nonconfo_define_owners")."\"><i class=\"fa fa-users\"></i></a></li>";
		}

		$txtpath .= "<li class=\"pull-right breadcrumb-btn\"><a id=\"add-nonconfo\" href=\"/ext/nonconfo/out/out.AddNonConfo.php\" type=\"button\" class=\"btn btn-info btn-sm\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("nonconfo_add_nonconfo")."\"><i class=\"fa fa-plus\"></i> <i class=\"fa fa-file\"></i></a> </li>";

		echo '<ul class="breadcrumb nonconfo-bread">'.$txtpath.'</ul>';
	} /* }}} */
	
	function pageNavigation($pageTitle, $pageType=null, $extra=null) { /* {{{ */

		if ($pageType!=null && strcasecmp($pageType, "noNav")) {
			echo "<div class=\"navbar\">\n";
			echo " <div class=\"navbar-inner\">\n";
			echo "  <div class=\"container\">\n";
			echo "   <a class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".col2\">\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo "   </a>\n";
			switch ($pageType) {
				case "view_folder":
					$this->folderNavigationBar($extra);
					break;
				case "view_document":
					$this->documentNavigationBar($extra);
					break;
				case "my_documents":
					$this->myDocumentsNavigationBar();
					break;
				case "my_account":
					$this->accountNavigationBar();
					break;
				case "admin_tools":
					$this->adminToolsNavigationBar();
					break;
				case "calendar";
					$this->calendarNavigationBar($extra);
					break;
				case "nonconfo_view_navigation";
					$this->nonconfoNavigationBar(); // Nonconfo extension Navigation Bar
					break;
			}
			echo " 	</div>\n";
			echo " </div>\n";
			echo "</div>\n";
			if($pageType == "view_folder" || $pageType == "view_document")
				echo $pageTitle."\n";
		} else {
			echo "<legend>".$pageTitle."</legend>\n";
		}

		return;
	} /* }}} */

	private function nonconfoNavigationBar() {
		$dms = $this->params['dms'];
		echo "<id=\"first\"><a href=\"/ext/nonconfo/out/out.ViewAllNonConfo.php\" class=\"brand\">".getMLText("nonconfo")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";
		$menuitems = array();
		$menuitems['nonconfo_add_nonconfo'] = array('link' => "/ext/nonconfo/out/out.AddNonConfo.php", 'label' => 'nonconfo_add_nonconfo');
		$menuitems['nonconfo_view_all'] = array('link' => "/ext/nonconfo/out/out.ViewAllNonConfo.php", 'label' => 'nonconfo_view_all');
		$menuitems['nonconfo_processes'] = array('link' => "/ext/nonconfo/out/out.AddProcess.php", 'label' => 'nonconfo_processes');
		$menuitems['nonconfo_define_owners'] = array('link' => "/ext/nonconfo/out/out.AddOwners.php", 'label' => 'nonconfo_define_owners');

		foreach($menuitems as $menuitem) {
			echo "<li><a href=\"".$menuitem['link']."\">".getMLText($menuitem['label'])."</a></li>";
		}

		echo "</ul>\n";
		echo "</div>\n";
		return;
	}

	private function folderNavigationBar($folder) { /* {{{ */
		$dms = $this->params['dms'];
		if (!is_object($folder) || strcasecmp(get_class($folder), $dms->getClassname('folder'))) {
			echo "<ul class=\"nav\">\n";
			echo "</ul>\n";
			return;
		}
		$accessMode = $folder->getAccessMode($this->params['user']);
		$folderID = $folder->getID();
		echo "<id=\"first\"><a href=\"/out/out.ViewFolder.php?folderid=". $folderID ."&showtree=".showtree()."\" class=\"brand\">".getMLText("folder")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";
		$menuitems = array();

		if ($accessMode == M_READ && !$this->params['user']->isGuest()) {
			$menuitems['edit_folder_notify'] = array('link'=>"/out/out.FolderNotify.php?folderid=".$folderID."&showtree=".showtree(), 'label'=>'edit_folder_notify');
		}
		else if ($accessMode >= M_READWRITE) {
			$menuitems['add_subfolder'] = array('link'=>"/out/out.AddSubFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'add_subfolder');
			$menuitems['add_document'] = array('link'=>"/out/out.AddDocument.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'add_document');
			//if($this->params['enablelargefileupload'])
				//$menuitems['add_multiple_documents'] = array('link'=>"/out/out.AddMultiDocument.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'add_multiple_documents');
			$menuitems['edit_folder_props'] = array('link'=>"/out/out.EditFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'edit_folder_props');
			if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
				$menuitems['move_folder'] = array('link'=>"/out/out.MoveFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'move_folder');

			if ($accessMode == M_ALL) {
				if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
					$menuitems['rm_folder'] = array('link'=>"/out/out.RemoveFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'rm_folder');
			}
			if ($accessMode == M_ALL) {
				$menuitems['edit_folder_access'] = array('link'=>"/out/out.FolderAccess.php?folderid=".$folderID."&showtree=".showtree(), 'label'=>'edit_folder_access');
			}
			$menuitems['edit_existing_notify'] = array('link'=>"/out/out.FolderNotify.php?folderid=". $folderID ."&showtree=". showtree(), 'label'=>'edit_existing_notify');
		}
		if ($this->params['user']->isAdmin() && $this->params['enablefullsearch']) {
			$menuitems['index_folder'] = array('link'=>"/out/out.Indexer.php?folderid=". $folderID."&showtree=".showtree(), 'label'=>'index_folder');
		}

		/* Check if hook exists because otherwise callHook() will override $menuitems */
		if($this->hasHook('folderNavigationBar'))
			$menuitems = $this->callHook('folderNavigationBar', $folder, $menuitems);

		foreach($menuitems as $menuitem) {
			echo "<li><a href=\"".$menuitem['link']."\">".getMLText($menuitem['label'])."</a></li>";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function documentNavigationBar($document)	{ /* {{{ */
		$accessMode = $document->getAccessMode($this->params['user']);
		$docid=".php?documentid=" . $document->getID();
		echo "<id=\"first\"><a href=\"/out/out.ViewDocument". $docid ."\" class=\"brand\">".getMLText("document")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";
		$menuitems = array();

		if ($accessMode >= M_READWRITE) {
			if (!$document->isLocked()) {
				$menuitems['update_document'] = array('link'=>"/out/out.UpdateDocument".$docid, 'label'=>'update_document');
				$menuitems['lock_document'] = array('link'=>"/op/op.LockDocument".$docid, 'label'=>'lock_document');
				$menuitems['edit_document_props'] = array('link'=>"/out/out.EditDocument".$docid , 'label'=>'edit_document_props');
				$menuitems['move_document'] = array('link'=>"/out/out.MoveDocument".$docid, 'label'=>'move_document');
			}
			else {
				$lockingUser = $document->getLockingUser();
				if (($lockingUser->getID() == $this->params['user']->getID()) || ($document->getAccessMode($this->params['user']) == M_ALL)) {
					$menuitems['update_document'] = array('link'=>"/out/out.UpdateDocument".$docid, 'label'=>'update_document');
					$menuitems['unlock_document'] = array('link'=>"/op/op.UnlockDocument".$docid, 'label'=>'unlock_document');
					$menuitems['edit_document_props'] = array('link'=>"/out/out.EditDocument".$docid, 'label'=>'edit_document_props');
					$menuitems['move_document'] = array('link'=>"/out/out.MoveDocument".$docid, 'label'=>'move_document');
				}
			}
			if($this->params['accessobject']->maySetExpires()) {
				$menuitems['expires'] = array('link'=>"/out/out.SetExpires".$docid, 'label'=>'expires');
			}
		}
		if ($accessMode == M_ALL) {
			$menuitems['rm_document'] = array('link'=>"/out/out.RemoveDocument".$docid, 'label'=>'rm_document');
			$menuitems['edit_document_access'] = array('link'=>"/out/out.DocumentAccess". $docid, 'label'=>'edit_document_access');
		}
		if ($accessMode >= M_READ && !$this->params['user']->isGuest()) {
			$menuitems['edit_existing_notify'] = array('link'=>"/out/out.DocumentNotify". $docid, 'label'=>'edit_existing_notify');
		}

		/* Check if hook exists because otherwise callHook() will override $menuitems */
		if($this->hasHook('documentNavigationBar'))
			$menuitems = $this->callHook('documentNavigationBar', $document, $menuitems);

		/* Do not use $this->callHook() because $menuitems must be returned by the hook
		 * or left unchanged
		 */
		/*
		$hookObjs = $this->getHookObjects();
		foreach($hookObjs as $hookObj) {
			if (method_exists($hookObj, 'documentNavigationBar')) {
	      $menuitems = $hookObj->documentNavigationBar($this, $document, $menuitems);
			}
		}
		*/

		foreach($menuitems as $menuitem) {
			echo "<li><a href=\"".$menuitem['link']."\">".getMLText($menuitem['label'])."</a></li>";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function accountNavigationBar() { /* {{{ */
		echo "<id=\"first\"><a href=\"/out/out.MyAccount.php\" class=\"brand\">".getMLText("my_account")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		if ($this->params['user']->isAdmin() || !$this->params['disableselfedit'])
			echo "<li id=\"first\"><a href=\"/out/out.EditUserData.php\">".getMLText("edit_user_details")."</a></li>\n";
		
		if (!$this->params['user']->isAdmin()) 
			echo "<li><a href=\"/out/out.UserDefaultKeywords.php\">".getMLText("edit_default_keywords")."</a></li>\n";

		echo "<li><a href=\"/out/out.ManageNotify.php\">".getMLText("edit_existing_notify")."</a></li>\n";

		if ($this->params['enableusersview']){
			echo "<li><a href=\"/out/out.UsrView.php\">".getMLText("users")."</a></li>\n";
			echo "<li><a href=\"/out/out.GroupView.php\">".getMLText("groups")."</a></li>\n";
		}		
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function myDocumentsNavigationBar() { /* {{{ */

		echo "<id=\"first\"><a href=\"/out/out.MyDocuments.php?inProcess=1\" class=\"brand\">".getMLText("my_documents")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		echo "<li><a href=\"/out/out.MyDocuments.php?inProcess=1\">".getMLText("documents_in_process")."</a></li>\n";
		echo "<li><a href=\"/out/out.MyDocuments.php\">".getMLText("all_documents")."</a></li>\n";
		if($this->params['workflowmode'] == 'traditional' || $this->params['workflowmode'] == 'traditional_only_approval') {
			if($this->params['workflowmode'] == 'traditional')
				echo "<li><a href=\"/out/out.ReviewSummary.php\">".getMLText("review_summary")."</a></li>\n";
			echo "<li><a href=\"/out/out.ApprovalSummary.php\">".getMLText("approval_summary")."</a></li>\n";
		} else {
			echo "<li><a href=\"/out/out.WorkflowSummary.php\">".getMLText("workflow_summary")."</a></li>\n";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function adminToolsNavigationBar() { /* {{{ */
		echo "    <id=\"first\"><a href=\"/out/out.AdminTools.php\" class=\"brand\">".getMLText("admin_tools")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "   <ul class=\"nav\">\n";

		/*echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("user_group_management")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"/out/out.UsrMgr.php\">".getMLText("user_management")."</a></li>\n";
		echo "      <li><a href=\"/out/out.GroupMgr.php\">".getMLText("group_management")."</a></li>\n";
		echo "      <li><a href=\"/out/out.UserList.php\">".getMLText("user_list")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";*/

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("definitions")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"/out/out.DefaultKeywords.php\">".getMLText("global_default_keywords")."</a></li>\n";
		echo "     <li><a href=\"/out/out.Categories.php\">".getMLText("global_document_categories")."</a></li>\n";
		echo "     <li><a href=\"/out/out.AttributeMgr.php\">".getMLText("global_attributedefinitions")."</a></li>\n";
		if($this->params['workflowmode'] == 'advanced') {
			echo "     <li><a href=\"/out/out.WorkflowMgr.php\">".getMLText("global_workflows")."</a></li>\n";
			echo "     <li><a href=\"/out/out.WorkflowStatesMgr.php\">".getMLText("global_workflow_states")."</a></li>\n";
			echo "     <li><a href=\"/out/out.WorkflowActionsMgr.php\">".getMLText("global_workflow_actions")."</a></li>\n";
		}
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		if($this->params['enablefullsearch']) {
			echo "   <ul class=\"nav\">\n";
			echo "    <li class=\"dropdown\">\n";
			echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("fullsearch")." <i class=\"icon-caret-down\"></i></a>\n";
			echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
			echo "      <li><a href=\"/out/out.Indexer.php\">".getMLText("update_fulltext_index")."</a></li>\n";
			echo "      <li><a href=\"/out/out.CreateIndex.php\">".getMLText("create_fulltext_index")."</a></li>\n";
			echo "      <li><a href=\"/out/out.IndexInfo.php\">".getMLText("fulltext_info")."</a></li>\n";
			echo "     </ul>\n";
			echo "    </li>\n";
			echo "   </ul>\n";
		}

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("backup_log_management")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"/out/out.BackupTools.php\">".getMLText("backup_tools")."</a></li>\n";
		if ($this->params['logfileenable'])
			echo "      <li><a href=\"/out/out.LogManagement.php\">".getMLText("log_management")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("misc")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"/out/out.ImportFS.php\">".getMLText("import_fs")."</a></li>\n";
		echo "      <li><a href=\"/out/out.Statistic.php\">".getMLText("folders_and_documents_statistic")."</a></li>\n";
		echo "      <li><a href=\"/out/out.Charts.php\">".getMLText("charts")."</a></li>\n";
		echo "      <li><a href=\"/out/out.Timeline.php\">".getMLText("timeline")."</a></li>\n";
		echo "      <li><a href=\"/out/out.ObjectCheck.php\">".getMLText("objectcheck")."</a></li>\n";
		echo "      <li><a href=\"/out/out.ExtensionMgr.php\">".getMLText("extension_manager")."</a></li>\n";
		echo "      <li><a href=\"/out/out.ClearCache.php\">".getMLText("clear_cache")."</a></li>\n";
		echo "      <li><a href=\"/out/out.Info.php\">".getMLText("version_info")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "<ul class=\"nav\">\n";
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */
	
	private function calendarNavigationBar($d){ /* {{{ */
		$ds="&day=".$d[0]."&month=".$d[1]."&year=".$d[2];
		echo "<id=\"first\"><a href=\"/out/out.Calendar.php?mode=y\" class=\"brand\">".getMLText("calendar")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		echo "<li><a href=\"/out/out.Calendar.php?mode=w".$ds."\">".getMLText("week_view")."</a></li>\n";
		echo "<li><a href=\"/out/out.Calendar.php?mode=m".$ds."\">".getMLText("month_view")."</a></li>\n";
		echo "<li><a href=\"/out/out.Calendar.php?mode=y".$ds."\">".getMLText("year_view")."</a></li>\n";
		if (!$this->params['user']->isGuest()) echo "<li><a href=\"/out/out.AddEvent.php\">".getMLText("add_event")."</a></li>\n";
		echo "</ul>\n";
		echo "</div>\n";
		return;
	
	} /* }}} */

	function pageList($pageNumber, $totalPages, $baseURI, $params) { /* {{{ */

		$maxpages = 25; // skip pages when more than this is shown
		$range = 5; // pages left and right of current page
		if (!is_numeric($pageNumber) || !is_numeric($totalPages) || $totalPages<2) {
			return;
		}

		// Construct the basic URI based on the $_GET array. One could use a
		// regular expression to strip out the pg (page number) variable to
		// achieve the same effect. This seems to be less haphazard though...
		$resultsURI = $baseURI;
		$first=true;
		foreach ($params as $key=>$value) {
			// Don't include the page number in the basic URI. This is added in
			// during the list display loop.
			if (!strcasecmp($key, "pg")) {
				continue;
			}
			if (is_array($value)) {
				foreach ($value as $subkey=>$subvalue) {
					$resultsURI .= ($first ? "?" : "&").$key."%5B".$subkey."%5D=".$subvalue;
					$first = false;
				}
			}
			else {
					$resultsURI .= ($first ? "?" : "&").$key."=".$value;
			}
			$first = false;
		}

		echo "<div class=\"pagination pagination-small\">";
		echo "<ul>";
		if($totalPages <= $maxpages) {
			for ($i = 1; $i <= $totalPages; $i++) {
				echo "<li ".($i == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$i."\">".$i."</a></li>";
			}
		} else {
			if($pageNumber-$range > 1)
				$start = $pageNumber-$range;
			else
				$start = 2;
			if($pageNumber+$range < $totalPages)
				$end = $pageNumber+$range;
			else
				$end = $totalPages-1;
			/* Move start or end to always show 2*$range items */
			$diff = $end-$start-2*$range;
			if($diff < 0) {
				if($start > 2)
					$start += $diff;
				if($end < $totalPages-1)
					$end -= $diff;
			}
			if($pageNumber > 1)
				echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".($pageNumber-1)."\">&laquo;</a></li>";
			echo "<li ".(1 == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=1\">1</a></li>";
			if($start > 2)
				echo "<li><span>...</span></li>";
			for($j=$start; $j<=$end; $j++)
				echo "<li ".($j == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$j."\">".$j."</a></li>";
			if($end < $totalPages-1)
				echo "<li><span>...</span></li>";
			if($end < $totalPages)
				echo "<li ".($totalPages == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$totalPages."\">".$totalPages."</a></li>";
			if($pageNumber < $totalPages)
				echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".($pageNumber+1)."\">&raquo;</a></li>";
		}
		if ($totalPages>1) {
			echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=all\">".getMLText("all_pages")."</a></li>";
		}
		echo "</ul>";
		echo "</div>";

		return;
	} /* }}} */

	function contentContainer($content) { /* {{{ */
		echo "<div class=\"well\">\n";
		echo $content;
		echo "</div>\n";
		return;
	} /* }}} */

	function contentContainerStart($class='') { /* {{{ */
		echo "<div class=\"well".($class ? " ".$class : "")."\">\n";
		return;
	} /* }}} */

	function contentContainerEnd() { /* {{{ */

		echo "</div>\n";
		return;
	} /* }}} */

	function contentHeading($heading, $noescape=false) { /* {{{ */

		if($noescape)
			echo "<legend>".$heading."</legend>\n";
		else
			echo "<legend>".htmlspecialchars($heading)."</legend>\n";
		return;
	} /* }}} */

	function contentSubHeading($heading, $first=false) { /* {{{ */

//		echo "<div class=\"contentSubHeading\"".($first ? " id=\"first\"" : "").">".htmlspecialchars($heading)."</div>\n";
		echo "<h5>".$heading."</h5>";
		return;
	} /* }}} */

	function getMimeIcon($fileType) { /* {{{ */
		// for extension use LOWER CASE only
		$icons = array();
		$icons["txt"]  = "txt.png";
		$icons["text"] = "txt.png";
		$icons["doc"]  = "word.png";
		$icons["dot"]  = "word.png";
		$icons["docx"] = "word.png";
		$icons["dotx"] = "word.png";
		$icons["rtf"]  = "document.png";
		$icons["xls"]  = "excel.png";
		$icons["xlt"]  = "excel.png";
		$icons["xlsx"] = "excel.png";
		$icons["xltx"] = "excel.png";
		$icons["ppt"]  = "powerpoint.png";
		$icons["pot"]  = "powerpoint.png";
		$icons["pptx"] = "powerpoint.png";
		$icons["potx"] = "powerpoint.png";
		$icons["exe"]  = "binary.png";
		$icons["html"] = "html.png";
		$icons["htm"]  = "html.png";
		$icons["gif"]  = "image.png";
		$icons["jpg"]  = "image.png";
		$icons["jpeg"] = "image.png";
		$icons["bmp"]  = "image.png";
		$icons["png"]  = "image.png";
		$icons["tif"]  = "image.png";
		$icons["tiff"] = "image.png";
		$icons["log"]  = "log.png";
		$icons["midi"] = "midi.png";
		$icons["pdf"]  = "pdf.png";
		$icons["wav"]  = "sound.png";
		$icons["mp3"]  = "sound.png";
		$icons["c"]    = "source_c.png";
		$icons["cpp"]  = "source_cpp.png";
		$icons["h"]    = "source_h.png";
		$icons["java"] = "source_java.png";
		$icons["py"]   = "source_py.png";
		$icons["tar"]  = "tar.png";
		$icons["gz"]   = "gz.png";
		$icons["7z"]   = "gz.png";
		$icons["bz"]   = "gz.png";
		$icons["bz2"]  = "gz.png";
		$icons["tgz"]  = "gz.png";
		$icons["zip"]  = "gz.png";
		$icons["rar"]  = "gz.png";
		$icons["mpg"]  = "video.png";
		$icons["avi"]  = "video.png";
		$icons["tex"]  = "tex.png";
		$icons["ods"]  = "x-office-spreadsheet.png";
		$icons["ots"]  = "x-office-spreadsheet.png";
		$icons["sxc"]  = "x-office-spreadsheet.png";
		$icons["stc"]  = "x-office-spreadsheet.png";
		$icons["odt"]  = "x-office-document.png";
		$icons["ott"]  = "x-office-document.png";
		$icons["sxw"]  = "x-office-document.png";
		$icons["stw"]  = "x-office-document.png";
		$icons["odp"]  = "ooo_presentation.png";
		$icons["otp"]  = "ooo_presentation.png";
		$icons["sxi"]  = "ooo_presentation.png";
		$icons["sti"]  = "ooo_presentation.png";
		$icons["odg"]  = "ooo_drawing.png";
		$icons["otg"]  = "ooo_drawing.png";
		$icons["sxd"]  = "ooo_drawing.png";
		$icons["std"]  = "ooo_drawing.png";
		$icons["odf"]  = "ooo_formula.png";
		$icons["sxm"]  = "ooo_formula.png";
		$icons["smf"]  = "ooo_formula.png";
		$icons["mml"]  = "ooo_formula.png";

		$icons["default"] = "default.png";

		$ext = strtolower(substr($fileType, 1));
		if (isset($icons[$ext])) {
			return $this->imgpath.$icons[$ext];
		}
		else {
			return $this->imgpath.$icons["default"];
		}
	} /* }}} */

	function printFileChooser($varname='userfile', $multiple=false, $accept='') { /* {{{ */
?>
	<div id="upload-files">
		<div id="upload-file">
			<div class="input-append">
				<input type="text" class="custom-input-text-search" name="theuserfile" readonly>
				<span class="btn btn-primary btn-file float-left btn-flat">
					<?php printMLText("browse");?>... <i class="fa fa-search"></i>  	<input id="<?php echo $varname; ?>" type="file" name="<?php echo $varname; ?>"<?php if($multiple) echo " multiple"; ?><?php if($accept) echo " accept=\"".$accept."\""; ?>>
				</span>
			</div>
		</div>
	</div>
<?php
	} /* }}} */

	function printLogoChooser($varname) { /* {{{ */
?>
	<div id="upload-files">
		<div id="upload-file">
			<div class="input-append">
				<input type="text" class="form-control" name="theuserfile" readonly>
				<span class="btn btn-primary btn-file">
					<?php printMLText("browse");?> <i class="fa fa-search"></i><input type="file" id="<?php echo $varname; ?>" name="<?php echo $varname; ?>">
				</span>
				<button type="submit" class="btn btn-success pull-right"><i class="fa fa-upload"></i> <?php echo getMLText("upload") ?></button>
			</div>
		</div>
	</div>
<?php
	} /* }}} */

	function printDateChooser($defDate = -1, $varName) { /* {{{ */
	
		if ($defDate == -1)
			$defDate = mktime();
		$day   = date("d", $defDate);
		$month = date("m", $defDate);
		$year  = date("Y", $defDate);

		print "<select name=\"" . $varName . "day\">\n";
		for ($i = 1; $i <= 31; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($day) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select> \n";
		print "<select name=\"" . $varName . "month\">\n";
		for ($i = 1; $i <= 12; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($month) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select> \n";
		print "<select name=\"" . $varName . "year\">\n";	
		for ($i = $year-5 ; $i <= $year+5 ; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($year) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select>";
	} /* }}} */

	function printSequenceChooser($objArr, $keepID = -1) { /* {{{ */
		if (count($objArr) > 0) {
			$max = $objArr[count($objArr)-1]->getSequence() + 1;
			$min = $objArr[0]->getSequence() - 1;
		}
		else {
			$max = 1.0;
		}
		print "<select class=\"form-control\" name=\"sequence\">\n";
		if ($keepID != -1) {
			print "  <option value=\"keep\">" . getMLText("seq_keep");
		}
		print "  <option value=\"".$max."\">" . getMLText("seq_end");
		if (count($objArr) > 0) {
			print "  <option value=\"".$min."\">" . getMLText("seq_start");
		}
		for ($i = 0; $i < count($objArr) - 1; $i++) {
			if (($objArr[$i]->getID() == $keepID) || (($i + 1 < count($objArr)) && ($objArr[$i+1]->getID() == $keepID))) {
				continue;
			}
			$index = ($objArr[$i]->getSequence() + $objArr[$i+1]->getSequence()) / 2;
			print "  <option value=\"".$index."\">" . getMLText("seq_after", array("prevname" => htmlspecialchars($objArr[$i]->getName())));
		}
		print "</select>";
	} /* }}} */

	function printDocumentChooserHtml($formName) { /* {{{ */
		print "<input type=\"hidden\" id=\"docid".$formName."\" name=\"docid\" value=\"\">";
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" class=\"custom-input-text-search\" id=\"choosedocsearch".$formName."\" data-target=\"docid".$formName."\" data-provide=\"typeahead\" name=\"docname".$formName."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" />";
		print "<a type=\"button\" data-target=\"#docChooser".$formName."\" href=\"/out/out.DocumentChooser.php?form=".$formName."&folderid=".$this->params['rootfolderid']."\" role=\"button\" class=\"btn btn-primary btn-flat\" data-toggle=\"modal\">".getMLText("document")."…</a>\n";
		print "</div>\n";
?>
<div class="modal fade" id="docChooser<?php echo $formName ?>" tabindex="-1" role="dialog" aria-labelledby="docChooserLabel" aria-hidden="true">
	<div class="modal-dialog modal-primary" role="document">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 id="docChooserLabel"><?php printMLText("choose_target_document") ?></h3>
	  </div>
	  <div class="modal-body">
			<p><?php printMLText('tree_loading') ?></p>
	  </div>
	  <div class="modal-footer">
	    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
	  </div>
 	</div>
</div>
<?php 
	} /* }}} */

	function printDocumentChooserJs($formName) { /* {{{ */
?>
function documentSelected<?php echo $formName ?>(id, name) {
	$('#docid<?php echo $formName ?>').val(id);
	$('#choosedocsearch<?php echo $formName ?>').val(name);
	$('#docChooser<?php echo $formName ?>').modal('hide');
}
function folderSelected<?php echo $formName ?>(id, name) {
}
<?php
	} /* }}} */

	function printDocumentChooser($formName) { /* {{{ */
		$this->printDocumentChooserHtml($formName);
?>
		<script language="JavaScript">
<?php
		$this->printDocumentChooserJs($formName);
?>
		</script>
<?php
	} /* }}} */
	function printFolderChooserHtml3($form, $accessMode, $exclude = -1, $default = false, $formname = '') { /* {{{ */
		$formid = "targetid".$form;
		if(!$formname)
			$formname = "targetid";
		print "<input type=\"hidden\" id=\"".$formid."\" name=\"".$formname."\" value=\"". (($default) ? $default->getID() : "") ."\">";
		print "<div class=\"form-group\">\n";
		print "<input class=\"custom-input-text-search\" type=\"text\" id=\"choosefoldersearch".$form."\" data-target=\"".$formid."\" data-provide=\"typeahead\"  name=\"targetname".$form."\" value=\"". (($default) ? htmlspecialchars($default->getName()) : "") ."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" target=\"".$formid."\" required/>";
			print "<button type=\"button\" class=\"btn btn-default\" id=\"clearfolder".$form."\"><i class=\"fa fa-times\"></i></button>";
		print "<a type=\"button\" data-target=\"#folderChooser".$form."\" href=\"/out/out.FolderChooser.php?form=".$form."&mode=".$accessMode."&exclude=".$exclude."\" role=\"button\" class=\"btn btn-default\" data-toggle=\"modal\">".getMLText("folder")."…</a>\n";
		print "</div>\n";
?>
<div class="modal" id="folderChooser<?php echo $form ?>" tabindex="-1" role="dialog" aria-labelledby="folderChooser<?php echo $form ?>Label" aria-hidden="true">
  <div class="modal-dialog modal-primary" role="document">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 class="modal-title" id="folderChooser<?php echo $form ?>Label"><?php printMLText("choose_target_folder") ?></h3>
	  </div>
	  <div class="modal-body">
			<p><?php printMLText('tree_loading') ?></p>
	  </div>
	  <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
	  </div>
	 </div>
</div>

<?php
	} /* }}} */

	function printFolderChooserHtml2($form, $accessMode, $exclude = -1, $default = false, $formname = '') { /* {{{ */
		$formid = "targetid".$form;
		if(!$formname)
			$formname = "targetid";
		print "<input type=\"hidden\" id=\"".$formid."\" name=\"".$formname."\" value=\"". (($default) ? $default->getID() : "") ."\">";
		print "<div class=\"form-group\">\n";
		print "<input class=\"custom-input-text-search\" type=\"text\" id=\"choosefoldersearch".$form."\" data-target=\"".$formid."\" data-provide=\"typeahead\"  name=\"targetname".$form."\" value=\"". (($default) ? htmlspecialchars($default->getName()) : "") ."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" target=\"".$formid."\" required/>";
		//	print "<button type=\"button\" class=\"btn btn-default\" id=\"clearfolder".$form."\"><i class=\"fa fa-times\"></i></button>";
		print "<a type=\"button\" data-target=\"#folderChooser".$form."\" href=\"/out/out.FolderChooser.php?form=".$form."&mode=".$accessMode."&exclude=".$exclude."\" role=\"button\" class=\"btn btn-default\" data-toggle=\"modal\">".getMLText("folder")."…</a>\n";
		print "</div>\n";
?>
<div class="modal" id="folderChooser<?php echo $form ?>" tabindex="-1" role="dialog" aria-labelledby="folderChooser<?php echo $form ?>Label" aria-hidden="true">
  <div class="modal-dialog modal-primary" role="document">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 class="modal-title" id="folderChooser<?php echo $form ?>Label"><?php printMLText("choose_target_folder") ?></h3>
	  </div>
	  <div class="modal-body">
			<p><?php printMLText('tree_loading') ?></p>
	  </div>
	  <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
	  </div>
	 </div>
</div>

<?php
	} /* }}} */

	function printFolderChooserHtml($form, $accessMode, $exclude = -1, $default = false, $formname = '') { /* {{{ */
		$formid = "targetid".$form;
		if(!$formname)
			$formname = "targetid";
		print "<input type=\"hidden\" id=\"".$formid."\" name=\"".$formname."\" value=\"". (($default) ? $default->getID() : "") ."\">";
		print "<div class=\"form-group\">\n";
		print "<input class=\"custom-input-text-search\" type=\"text\" id=\"choosefoldersearch".$form."\" data-target=\"".$formid."\" data-provide=\"typeahead\"  name=\"targetname".$form."\" value=\"". (($default) ? htmlspecialchars($default->getName()) : "") ."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" target=\"".$formid."\" required/>";
		print "<button type=\"button\" class=\"btn btn-default\" id=\"clearfolder".$form."\"><i class=\"fa fa-times\"></i></button>";
		//print "<a type=\"button\" data-target=\"#folderChooser".$form."\" href=\"/out/out.FolderChooser.php?form=".$form."&mode=".$accessMode."&exclude=".$exclude."\" role=\"button\" class=\"btn btn-default\" data-toggle=\"modal\">".getMLText("folder")."…</a>\n";
		print "</div>\n";
?>
<div class="modal" id="folderChooser<?php echo $form ?>" tabindex="-1" role="dialog" aria-labelledby="folderChooser<?php echo $form ?>Label" aria-hidden="true">
  <div class="modal-dialog modal-primary" role="document">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 class="modal-title" id="folderChooser<?php echo $form ?>Label"><?php printMLText("choose_target_folder") ?></h3>
	  </div>
	  <div class="modal-body">
			<p><?php printMLText('tree_loading') ?></p>
	  </div>
	  <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
	  </div>
	 </div>
</div>

<?php
	} /* }}} */

	function printFolderChooserJs($form) { /* {{{ */
?>
function folderSelected<?php echo $form ?>(id, name) {
	$('#targetid<?php echo $form ?>').val(id);
	$('#choosefoldersearch<?php echo $form ?>').val(name);
	$('#folderChooser<?php echo $form ?>').modal('hide');
}
$(document).ready(function() {
	$('#clearfolder<?php print $form ?>').click(function(ev) {
		$('#choosefoldersearch<?php echo $form ?>').val('');
		$('#targetid<?php echo $form ?>').val('');
	});
});
<?php
	} /* }}} */

	function printFolderChooser($form, $accessMode, $exclude = -1, $default = false, $formname='') { /* {{{ */
		$this->printFolderChooserHtml($form, $accessMode, $exclude, $default, $formname);
?>
		<script language="JavaScript">
<?php
		$this->printFolderChooserJs($form);
?>
		</script>
<?php
	} /* }}} */

	/**
	 * Do not use anymore. Was previously used to show the category
	 * chooser. It has been replaced by a select box
	 */
	function printCategoryChooser($formName, $categories=array()) { /* {{{ */
?>
<script language="JavaScript">
	function clearCategory<?php print $formName ?>() {
		document.<?php echo $formName ?>.categoryid<?php echo $formName ?>.value = '';
		document.<?php echo $formName ?>.categoryname<?php echo $formName ?>.value = '';
	}

	function acceptCategories() {
		var targetName = document.<?php echo $formName?>.categoryname<?php print $formName ?>;
		var targetID = document.<?php echo $formName?>.categoryid<?php print $formName ?>;
		var value = '';
		$('#keywordta option:selected').each(function(){
			value += ' ' + $(this).text();
		});
		targetName.value = value;
		targetID.value = $('#keywordta').val();
		return true;
	}
</script>
<?php
		$ids = $names = array();
		if($categories) {
			foreach($categories as $cat) {
				$ids[] = $cat->getId();
				$names[] = htmlspecialchars($cat->getName());
			}
		}
		print "<input type=\"hidden\" name=\"categoryid".$formName."\" value=\"".implode(',', $ids)."\">";
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" disabled name=\"categoryname".$formName."\" value=\"".implode(' ', $names)."\">";
		print "<button type=\"button\" class=\"btn\" onclick=\"javascript:clearCategory".$formName."();\"><i class=\"icon-remove\"></i></button>";
		print "<a data-target=\"#categoryChooser\" href=\"out.CategoryChooser.php?form=form1&cats=".implode(',', $ids)."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("category")."…</a>\n";
		print "</div>\n";
?>
<div class="modal fade" id="categoryChooser" tabindex="-1" role="dialog" aria-labelledby="categoryChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="categoryChooserLabel"><?php printMLText("choose_target_category") ?></h3>
  </div>
  <div class="modal-body">
		<p><?php printMLText('categories_loading') ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
    <button class="btn" data-dismiss="modal" aria-hidden="true" onClick="acceptCategories();"><i class="icon-save"></i> <?php printMLText("save") ?></button>
  </div>
</div>
<?php
	} /* }}} */

	function printKeywordChooserHtml($formName, $keywords='', $fieldname='keywords') { /* {{{ */
		//$strictformcheck = $this->params['strictformcheck'];
?>
<div class="form-group">
	<input type="text" class="custom-input-text-search" name="<?php echo $fieldname; ?>" id="<?php echo $fieldname; ?>" value="<?php echo htmlspecialchars($keywords);?>"<?php //echo $strictformcheck ? ' required' : ''; ?> />

  <a data-target="#keywordChooser" type="button" role="button" class="btn btn-primary btn-flat float-left" data-toggle="modal" href="out.KeywordChooser.php?target=<?php echo $formName; ?>"><?php printMLText("keywords");?>…</a>

</div>
<div class="modal fade modal-primary" id="keywordChooser" tabindex="-1" role="dialog" aria-labelledby="keywordChooserLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 class="modal-title" id="keywordChooserLabel"><?php printMLText("use_default_keywords") ?></h3>
		  </div>
		  <div class="modal-body">
				<p><?php printMLText('keywords_loading') ?></p>
		  </div>
		  <div class="modal-footer">
		    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
		    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true" id="acceptkeywords"><i class="icon-save"></i> <?php printMLText("save") ?></button>
		  </div>
	</div>
</div>

<?php
	} /* }}} */

	function printKeywordChooserJs($formName) { /* {{{ */
?>
$(document).ready(function() {
	$('#acceptkeywords').click(function(ev) {
		acceptKeywords();
	});
});
<?php
	} /* }}} */

	function printKeywordChooser($formName, $keywords='', $fieldname='keywords') { /* {{{ */
		$this->printKeywordChooserHtml($formName, $keywords, $fieldname);
?>
		<script language="JavaScript">
<?php
		$this->printKeywordChooserJs($formName);
?>
		</script>
<?php
	} /* }}} */

	function printAttributeEditField($attrdef, $attribute, $fieldname='attributes', $norequire=false) { /* {{{ */
		switch($attrdef->getType()) {
		case SeedDMS_Core_AttributeDefinition::type_boolean:
			echo "<input type=\"hidden\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"0\" />";
			echo "<input class=\"form-control\" type=\"checkbox\" id=\"".$fieldname."_".$attrdef->getId()."\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"1\" ".(($attribute && $attribute->getValue()) ? 'checked' : '')." />";
			break;
		case SeedDMS_Core_AttributeDefinition::type_date:
				$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValue() : $attribute) : '';
?>
        <span class="input-append date datepicker" data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
					<input class="form-control" id="<?php echo $fieldname."_".$attrdef->getId();?>" class="span9" size="16" name="<?php echo $fieldname ?>[<?php echo $attrdef->getId() ?>]" type="text" value="<?php if($objvalue) echo $objvalue; else echo "" /*date('Y-m-d')*/; ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
				</span>
<?php
			break;
		case SeedDMS_Core_AttributeDefinition::type_email:
			$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValue() : $attribute) : '';
			echo "<input class=\"form-control\" type=\"text\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"".htmlspecialchars($objvalue)."\"".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').' data-rule-email="true"'." />";
			break;
		default:
			if($valueset = $attrdef->getValueSetAsArray()) {
				echo "<input type=\"hidden\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"\" />";
				echo "<select class=\"form-control\" id=\"".$fieldname."_".$attrdef->getId()."\" name=\"".$fieldname."[".$attrdef->getId()."]";
				if($attrdef->getMultipleValues()) {
					echo "[]\" multiple";
				} else {
					echo "\"";
				}
				echo "".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').">";
				if(!$attrdef->getMultipleValues()) {
					echo "<option value=\"\"></option>";
				}
				$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValueAsArray() : $attribute) : array();
				foreach($valueset as $value) {
					if($value) {
						echo "<option value=\"".htmlspecialchars($value)."\"";
						if(is_array($objvalue) && in_array($value, $objvalue))
							echo " selected";
						elseif($value == $objvalue)
							echo " selected";
						echo ">".htmlspecialchars($value)."</option>";
					}
				}
				echo "</select>";
			} else {
				$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValue() : $attribute) : '';
				if(strlen($objvalue) > 80) {
					echo "<textarea class=\"form-control\" id=\"".$fieldname."_".$attrdef->getId()."\" class=\"input-xxlarge\" name=\"".$fieldname."[".$attrdef->getId()."]\"".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').">".htmlspecialchars($objvalue)."</textarea>";
				} else {
					echo "<input class=\"form-control\" type=\"text\" id=\"".$fieldname."_".$attrdef->getId()."\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"".htmlspecialchars($objvalue)."\"".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').($attrdef->getType() == SeedDMS_Core_AttributeDefinition::type_int ? ' data-rule-digits="true"' : '')." />";
				}
			}
			break;
		}
	} /* }}} */

	function printDropFolderChooserHtml($formName, $dropfolderfile="", $showfolders=0) { /* {{{ */
		print "<div class=\"input-append\">\n";
		print "<input readonly type=\"text\" id=\"dropfolderfile".$formName."\" name=\"dropfolderfile".$formName."\" value=\"".$dropfolderfile."\">";
		print "<button type=\"button\" class=\"btn\" id=\"clearfilename".$formName."\"><i class=\"icon-remove\"></i></button>";
		print "<a data-target=\"#dropfolderChooser\" href=\"out.DropFolderChooser.php?form=form1&dropfolderfile=".$dropfolderfile."&showfolders=".$showfolders."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".($showfolders ? getMLText("choose_target_folder"): getMLText("choose_target_file"))."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="dropfolderChooser" tabindex="-1" role="dialog" aria-labelledby="dropfolderChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="dropfolderChooserLabel"><?php echo ($showfolders ? getMLText("choose_target_folder"): getMLText("choose_target_file")) ?></h3>
  </div>
  <div class="modal-body">
		<p><?php printMLText('files_loading') ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
  </div>
</div>
<?php
	} /* }}} */

	function printDropFolderChooserJs($formName, $showfolders=0) { /* {{{ */
?>
/* Set up a callback which is called when a folder in the tree is selected */
modalDropfolderChooser = $('#dropfolderChooser');
function fileSelected(name) {
	$('#dropfolderfile<?php echo $formName ?>').val(name);
	modalDropfolderChooser.modal('hide');
}
<?php if($showfolders) { ?>
function folderSelected(name) {
	$('#dropfolderfile<?php echo $formName ?>').val(name);
	modalDropfolderChooser.modal('hide');
}
<?php } ?>
$(document).ready(function() {
	$('#clearfilename<?php print $formName ?>').click(function(ev) {
		$('#dropfolderfile<?php echo $formName ?>').val('');
	});
});
<?php
	} /* }}} */

	function printDropFolderChooser($formName, $dropfolderfile="", $showfolders=0) { /* {{{ */
		$this->printDropFolderChooserHtml($formName, $dropfolderfile, $showfolders);
?>
		<script language="JavaScript">
<?php
		$this->printDropFolderChooserJs($formName, $showfolders);
?>
		</script>
<?php
	} /* }}} */

	function getImgPath($img) { /* {{{ */

		if ( is_file($this->imgpath.$img) ) {
			return $this->imgpath.$img;
		}
		return "/out/images/$img";
	} /* }}} */

	function getCountryFlag($lang) { /* {{{ */
		switch((string)$lang) {
		case "en_GB":
			return '/views/'.$this->theme.'/images/flags/gb.png';
			break;
		case "es_ES":
			return '/views/'.$this->theme.'/images/flags/es.png';
			break;
		}
	} /* }}} */

	function printImgPath($img) { /* {{{ */
		print $this->getImgPath($img);
	} /* }}} */

	function infoMsg($msg) { /* {{{ */
		echo "<div class=\"callout callout-info alert-dismissible\">";
		echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\"><i class=\"fa fa-times\"></i></button>";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function warningMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-warning\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function errorMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-error\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function exitError($pagetitle, $error, $noexit=false) { /* {{{ */
		$this->htmlStartPage($pagetitle, "skin-blue sidebar-mini sidebar-collapse");
		$this->containerStart();
		$this->mainHeaderForLoginError();
		$this->contentStart();
		?>
		<div class="gap-40"></div>
		<div class="row">
			<div class="col-md-12">
				<div class="box box-danger box-solid">
      		<div class="box-header with-border">
            <h3 class="box-title"><?php echo getMLText("error_occured"); ?></h3>
            <div class="box-tools pull-right">
	            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
        	<div class="box-body">
	        <?php
					print "<div class=\"alert alert-error\">";
					print "<h4>".getMLText('error')."!</h4>";
					print htmlspecialchars($error);
					print "</div>";
					print "<div><button class=\"btn history-back\">".getMLText('back')."</button></div>";
					?>
        	</div>
      	</div>
			</div>
		</div>
		<?php
		$this->contentEnd();		
		$this->containerEnd();
		$this->htmlEndPage();
		
		add_log_line(" UI::exitError error=".$error." pagetitle=".$pagetitle, PEAR_LOG_ERR);

		if($noexit)
			return;

		exit;	
	} /* }}} */

	function printNewTreeNavigation($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0, $orderby='') { /* {{{ */
		$this->printNewTreeNavigationHtml($folderid, $accessmode, $showdocs, $formid, $expandtree, $orderby);
?>
		<script language="JavaScript">
<?php
		$this->printNewTreeNavigationJs($folderid, $accessmode, $showdocs, $formid, $expandtree, $orderby);
?>
	</script>
<?php
	} /* }}} */

	function printNewTreeNavigationHtml($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0, $orderby='') { /* {{{ */
		echo "<div id=\"jqtree".$formid."\" data-url=\"/op/op.Ajax.php?command=subtree&showdocs=".$showdocs."&orderby=".$orderby."\"></div>\n";
	} /* }}} */

	/**
	 * Create a tree of folders using jqtree.
	 *
	 * The tree can contain folders only or include documents.
	 *
	 * @param integer $folderid current folderid. If set the tree will be
	 *   folded out and the all folders in the path will be visible
	 * @param integer $accessmode use this access mode when retrieving folders
	 *   and documents shown in the tree
	 * @param boolean $showdocs set to true if tree shall contain documents
	 *   as well.
	 */
	function printNewTreeNavigationJs($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0, $orderby='') { /* {{{ */
		function jqtree($path, $folder, $user, $accessmode, $showdocs=1, $expandtree=0, $orderby='') {
			if($path || $expandtree) {
				if($path)
					$pathfolder = array_shift($path);
				$subfolders = $folder->getSubFolders($orderby);
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>($subfolder->hasSubFolders() || ($subfolder->hasDocuments() && $showdocs)) ? true : false, 'is_folder'=>true);
					if($expandtree || $pathfolder->getID() == $subfolder->getID()) {
						if($showdocs) {
							$documents = $folder->getDocuments($orderby);
							$documents = SeedDMS_Core_DMS::filterAccess($documents, $user, $accessmode);
							foreach($documents as $document) {
								$node2 = array('label'=>$document->getName(), 'id'=>$document->getID(), 'load_on_demand'=>false, 'is_folder'=>false);
								$children[] = $node2;
							}
						}
						$node['children'] = jqtree($path, $subfolder, $user, $accessmode, $showdocs, $expandtree, $orderby);
					}
					$children[] = $node;
				}
				return $children;
			} else {
				$subfolders = $folder->getSubFolders($orderby);
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>($subfolder->hasSubFolders() || ($subfolder->hasDocuments() && $showdocs)) ? true : false, 'is_folder'=>true);
					$children[] = $node;
				}
				return $children;
			}
			return array();
		}

		if($folderid) {
			$folder = $this->params['dms']->getFolder($folderid);
			$path = $folder->getPath();
			$folder = array_shift($path);
			$node = array('label'=>$folder->getName(), 'id'=>$folder->getID(), 'load_on_demand'=>true, 'is_folder'=>true);
			if(!$folder->hasSubFolders()) {
				$node['load_on_demand'] = false;
				$node['children'] = array();
			} else {
				$node['children'] = jqtree($path, $folder, $this->params['user'], $accessmode, $showdocs, $expandtree, $orderby);
				if($showdocs) {
					$documents = $folder->getDocuments($orderby);
					$documents = SeedDMS_Core_DMS::filterAccess($documents, $this->params['user'], $accessmode);
					foreach($documents as $document) {
						$node2 = array('label'=>$document->getName(), 'id'=>$document->getID(), 'load_on_demand'=>false, 'is_folder'=>false);
						$node['children'][] = $node2;
					}
				}
			}
			/* Nasty hack to remove the highest folder */
			if(isset($this->params['remove_root_from_tree']) && $this->params['remove_root_from_tree']) {
				foreach($node['children'] as $n)
					$tree[] = $n;
			} else {
				$tree[] = $node;
			}
			
		} else {
			$root = $this->params['dms']->getFolder($this->params['rootfolderid']);
			$tree = array(array('label'=>$root->getName(), 'id'=>$root->getID(), 'load_on_demand'=>true, 'is_folder'=>true));
		}

?>
var data = <?php echo json_encode($tree); ?>;
$(function() {
	$('#jqtree<?php echo $formid ?>').tree({
		saveState: true,
		data: data,
		saveState: 'jqtree<?php echo $formid; ?>',
		openedIcon: $('<i class="fa fa-arrow-circle-down"></i>'),
		closedIcon: $('<i class="fa fa-arrow-circle-right"></i>'),
		_onCanSelectNode: function(node) {
			if(node.is_folder) {
				folderSelected<?php echo $formid ?>(node.id, node.name);
			} else
				documentSelected<?php echo $formid ?>(node.id, node.name);
		},
		autoOpen: true,
		drapAndDrop: true,
    onCreateLi: function(node, $li) {
        // Add 'icon' span before title
				if(node.is_folder)
					$li.find('.jqtree-title').before('<i class="fa fa-folder-o" rel="folder_' + node.id + '"></i> ').attr('rel', 'folder_' + node.id).attr('formtoken', '<?php echo createFormKey('movefolder'); ?>');
				else
					$li.find('.jqtree-title').before('<i class="fa fa-file"></i> ');
    }
	});
	// Unfold tree if folder is opened
	$('#jqtree<?php echo $formid ?>').tree('openNode', $('#jqtree<?php echo $formid ?>').tree('getNodeById', <?php echo $folderid ?>), false);
  $('#jqtree<?php echo $formid ?>').bind(
		'tree.click',
		function(event) {
			var node = event.node;
			$('#jqtree<?php echo $formid ?>').tree('openNode', node);
//			event.preventDefault();
			if(node.is_folder) {
				folderSelected<?php echo $formid ?>(node.id, node.name);
			} else
				documentSelected<?php echo $formid ?>(node.id, node.name);
		}
	);
});
<?php
	} /* }}} */

	function printTreeNavigation($folderid, $showtree){ /* {{{ */
		if ($showtree==1){
			$this->contentHeading("<a href=\"/out/out.ViewFolder.php?folderid=". $folderid."&showtree=0\"><i class=\"fa fa-minus-circle\"></i></a>", true);
			$this->contentContainerStart();
?>
	<script language="JavaScript">
	function folderSelected(id, name) {
		window.location = '/out/out.ViewFolder.php?folderid=' + id;
	}
	</script>
<?php
			$this->printNewTreeNavigation($folderid, M_READ, 0, '');
			$this->contentContainerEnd();
		} else {
			$this->contentHeading("<a href=\"/out/out.ViewFolder.php?folderid=". $folderid."&showtree=1\"><i class=\"fa fa-plus-circle\"></i></a>", true);
		}
	} /* }}} */

	/**
	 * Return clipboard content rendered as html
	 *
	 * @param array clipboard
	 * @return string rendered html content
	 */
	function mainClipboard($clipboard, $previewer){ /* {{{ */
		$dms = $this->params['dms'];
		$content = '';
		$foldercount = $doccount = 0;
		if($clipboard['folders']) {
			foreach($clipboard['folders'] as $folderid) {
				/* FIXME: check for access rights, which could have changed after adding the folder to the clipboard */
				if($folder = $dms->getFolder($folderid)) {
					$comment = $folder->getComment();
					if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
					$content .= "<tr draggable=\"true\" rel=\"folder_".$folder->getID()."\" class=\"folder table-row-folder\" formtoken=\"".createFormKey('movefolder')."\">";
					$content .= "<td><a draggable=\"false\" href=\"out.ViewFolder.php?folderid=".$folder->getID()."&showtree=".showtree()."\"><i class=\"fa fa-folder\"></i></a></td>\n";
					$content .= "<td><a draggable=\"false\" href=\"out.ViewFolder.php?folderid=".$folder->getID()."&showtree=".showtree()."\">" . htmlspecialchars($folder->getName()) . "</a>";
					if($comment) {
						$content .= "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
					}
					$content .= "</td>\n";
					$content .= "<td>\n";
					$content .= "<div class=\"list-action\"><a class=\"removefromclipboard\" rel=\"F".$folderid."\" msg=\"".getMLText('splash_removed_from_clipboard')."\" _href=\"/op/op.RemoveFromClipboard.php?folderid=".(isset($this->params['folder']) ? $this->params['folder']->getID() : '')."&id=".$folderid."&type=folder\" title=\"".getMLText('rm_from_clipboard')."\"><i class=\"icon-remove\"></i></a></div>";
					$content .= "</td>\n";
					$content .= "</tr>\n";
					$foldercount++;
				}
			}
		}
		if($clipboard['docs']) {
			foreach($clipboard['docs'] as $docid) {
				/* FIXME: check for access rights, which could have changed after adding the document to the clipboard */
				if($document = $dms->getDocument($docid)) {
					$comment = $document->getComment();
					if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
					if($latestContent = $document->getLatestContent()) {
						$previewer->createPreview($latestContent);
						$version = $latestContent->getVersion();
						$status = $latestContent->getStatus();
						
						$content .= "<tr draggable=\"true\" rel=\"document_".$docid."\" class=\"table-row-document\" formtoken=\"".createFormKey('movedocument')."\">";

						if (file_exists($dms->contentDir . $latestContent->getPath())) {
							$content .= "<td class=\"align-center\"><a draggable=\"false\" href=\"/op/op.Download.php?documentid=".$docid."&version=".$version."\">";
							if($previewer->hasPreview($latestContent)) {
								$content .= "<img draggable=\"false\" class=\"mimeicon\" width=\"40\"src=\"/op/op.Preview.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."&width=40\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
							} else {
								$content .= "<img draggable=\"false\" class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
							}
							$content .= "</a></td>";
						} else
							$content .= "<td><img draggable=\"false\" class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\"></td>";
						
						$content .= "<td><a draggable=\"false\" href=\"out.ViewDocument.php?documentid=".$docid."&showtree=".showtree()."\">" . htmlspecialchars($document->getName()) . "</a>";
						if($comment) {
							$content .= "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
						}
						$content .= "</td>\n";
						$content .= "<td>\n";
						$content .= "<div class=\"list-action\"><a class=\"removefromclipboard\" rel=\"D".$docid."\" msg=\"".getMLText('splash_removed_from_clipboard')."\" _href=\"/op/op.RemoveFromClipboard.php?folderid=".(isset($this->params['folder']) ? $this->params['folder']->getID() : '')."&id=".$docid."&type=document\" title=\"".getMLText('rm_from_clipboard')."\"><i class=\"icon-remove\"></i></a></div>";
						$content .= "</td>\n";
						$content .= "</tr>";
						$doccount++;
					}
				}
			}
		}

		/* $foldercount or $doccount will only count objects which are
		 * actually available
		 */
		if($foldercount || $doccount) {
			$content = "<table class=\"table\">".$content;
			$content .= "</table>";
		} else {
		}
		$content .= "<div class=\"alert add-clipboard-area\">".getMLText("drag_icon_here")."</div>";
		return $content;
	} /* }}} */

	/**
	 * Print clipboard in div container
	 *
	 * @param array clipboard
	 */
	function printClipboard($clipboard, $previewer){ /* {{{ */
		$this->contentHeading(getMLText("clipboard"), true);
		echo "<div id=\"main-clipboard\">\n";
		echo $this->mainClipboard($clipboard, $previewer);
		echo "</div>\n";
	} /* }}} */

	/**
	 * Print button with link for deleting a document
	 *
	 * This button is used in document listings (e.g. on the ViewFolder page)
	 * for deleting a document. In seeddms version < 4.3.9 this was just a
	 * link to the out/out.RemoveDocument.php page which asks for confirmation
	 * an than calls op/op.RemoveDocument.php. Starting with version 4.3.9
	 * the button just opens a small popup asking for confirmation and than
	 * calls the ajax command 'deletedocument'. The ajax call is called
	 * in the click function of 'button.removedocument'. That button needs
	 * to have two attributes: 'rel' for the id of the document, and 'msg'
	 * for the message shown by notify if the document could be deleted.
	 *
	 * @param object $document document to be deleted
	 * @param string $msg message shown in case of successful deletion
	 * @param boolean $return return html instead of printing it
	 * @return string html content if $return is true, otherwise an empty string
	 */
	function printDeleteDocumentButton($document, $msg, $return=false){ /* {{{ */
		$docid = $document->getID();
		$content = '';
    $content .= '<a class="btn btn-danger btn-sm delete-document-btn btn-action" rel="'.$docid.'" msg="'.getMLText($msg).'" confirmmsg="'.htmlspecialchars(getMLText("confirm_rm_document", array ("documentname" => $document->getName())), ENT_QUOTES).'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("rm_document").'"><i class="fa fa-times"></i></a>';
		if($return)
			return $content;
		else
			echo $content;
		return '';
	} /* }}} */

	function printDeleteDocumentButtonJs(){ /* {{{ */
		echo "
		$(document).ready(function () {
//			$('.delete-document-btn').click(function(ev) {
			$('body').on('click', 'a.delete-document-btn', function(ev){
				id = $(ev.currentTarget).attr('rel');
				confirmmsg = $(ev.currentTarget).attr('confirmmsg');
				msg = $(ev.currentTarget).attr('msg');
				formtoken = '".createFormKey('removedocument')."';
				bootbox.confirm({
    		message: confirmmsg,
    		buttons: {
        	confirm: {
            label: \"<i class='fa fa-times'></i> ".getMLText("rm_document")."\",
            className: 'btn-danger'
        	},
        	cancel: {
            label: \"".getMLText("cancel")."\",
            className: 'btn-default'
        	}
    		},
	    		callback: function (result) {
	    			if (result) {
	    				$.get('/op/op.Ajax.php',
							{ command: 'deletedocument', id: id, formtoken: formtoken },
							function(data) {
								if(data.success) {
									$('#table-row-document-'+id).hide('slow');
									noty({
										text: msg,
										type: 'success',
										dismissQueue: true,
										layout: 'topRight',
										theme: 'defaultTheme',
										timeout: 1500,
									});
								} else {
									noty({
										text: data.message,
										type: 'error',
										dismissQueue: true,
										layout: 'topRight',
										theme: 'defaultTheme',
										timeout: 3500,
									});
								}
							},
							'json'
							);
	    			}	
					}
				});
			});
		});
		";
	} /* }}} */

	/**
	 * Print button with link for deleting a folder
	 *
	 * This button works like document delete button
	 * {@link SeedDMS_Bootstrap_Style::printDeleteDocumentButton()}
	 *
	 * @param object $folder folder to be deleted
	 * @param string $msg message shown in case of successful deletion
	 * @param boolean $return return html instead of printing it
	 * @return string html content if $return is true, otherwise an empty string
	 */
	function printDeleteFolderButton($folder, $msg, $return=false){ /* {{{ */
		$folderid = $folder->getID();
		$content = '';
		$content .= '<a type="button" class="btn btn-danger btn-sm delete-folder-btn btn-action" rel="'.$folderid.'" msg="'.getMLText($msg).'" confirmmsg="'.htmlspecialchars(getMLText("confirm_rm_folder", array ("foldername" => $folder->getName())), ENT_QUOTES).'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("rm_folder").'"><i class="fa fa-times"></i></a>';
		if($return)
			return $content;
		
		else
			echo $content;
		return '';
	} /* }}} */

	function printDeleteFolderButtonJs(){ /* {{{ */
		echo "
		$(document).ready(function () {
//			$('.delete-folder-btn').click(function(ev) {
			$('body').on('click', 'a.delete-folder-btn', function(ev){
				id = $(ev.currentTarget).attr('rel');
				confirmmsg = $(ev.currentTarget).attr('confirmmsg');
				msg = $(ev.currentTarget).attr('msg');
				formtoken = '".createFormKey('removefolder')."';
				bootbox.confirm({
    		message: confirmmsg,
    		buttons: {
        	confirm: {
            label: \"<i class='fa fa-times'></i> ".getMLText("rm_folder")."\",
            className: 'btn-danger'
        	},
        	cancel: {
            label: \"".getMLText("cancel")."\",
            className: 'btn-default'
        	}
    		}, 
	    		callback: function (result) {
	    			if (result) {
	    				$.get('/op/op.Ajax.php',
								{ command: 'deletefolder', id: id, formtoken: formtoken },
									function(data) {
										if(data.success) {
											$('#table-row-folder-'+id).hide('slow');
												noty({
												text: msg,
												type: 'success',
												dismissQueue: true,
												layout: 'topRight',
												theme: 'defaultTheme',
												timeout: 1500,
											});
										} else {
											noty({
											text: data.message,
											type: 'error',
											dismissQueue: true,
											layout: 'topRight',
											theme: 'defaultTheme',
											timeout: 3500,
										});
									}
								},
								'json'
							);
						}	
	    		}
	    	});
			});
		});
		";
	} /* }}} */

	function printLockButton($document, $msglock, $msgunlock, $return=false) { /* {{{ */
		$docid = $document->getID();
		if($document->isLocked()) {
			$icon = 'unlock';
			$msg = $msgunlock;
			$title = 'unlock_document';
		} else {
			$icon = 'lock';
			$msg = $msglock;
			$title = 'lock_document';
		}
		$content = '';
    $content .= '<a class="btn btn-warning btn-sm lock-document-btn btn-action" rel="'.$docid.'" msg="'.getMLText($msg).'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText($title).'"><i class="fa fa-'.$icon.'"></i></a>';
		if($return)
			return $content;
		else
			echo $content;
		return '';
	} /* }}} */

	/**
	 * Output left-arrow with link which takes over a number of ids into
	 * a select box.
	 *
	 * Clicking in the button will preset the comma seperated list of ids
	 * in data-ref as options in the select box with name $name
	 *
	 * @param string $name id of select box
	 * @param array $ids list of option values
	 */
	function printSelectPresetButtonHtml($name, $ids) { /* {{{ */
?>
	<span id="<?php echo $name; ?>_btn" class="selectpreset_btn" style="cursor: pointer;" title="<?php printMLText("takeOver".$name); ?>" data-ref="<?php echo $name; ?>" data-ids="<?php echo implode(",", $ids);?>"><i class="icon-arrow-left"></i></span>
<?php
	} /* }}} */

	/**
	 * Javascript code for select preset button
	 */
	function printSelectPresetButtonJs() { /* {{{ */
?>
$(document).ready( function() {
	$('.selectpreset_btn').click(function(ev){
		ev.preventDefault();
		if (typeof $(ev.currentTarget).data('ids') != 'undefined') {
			target = $(ev.currentTarget).data('ref');
			// Use attr() instead of data() because data() converts to int which cannot be split
			items = $(ev.currentTarget).attr('data-ids');
			arr = items.split(",");
			for(var i in arr) {
				$("#"+target+" option[value='"+arr[i]+"']").attr("selected", "selected");
			}
//			$("#"+target).trigger("chosen:updated");
			$("#"+target).trigger("change");
		}
	});
});
<?php
	} /* }}} */

	/**
	 * Output left-arrow with link which takes over a string into
	 * a input field.
	 *
	 * Clicking on the button will preset the string
	 * in data-ref the value of the input field with name $name
	 *
	 * @param string $name id of select box
	 * @param string $text text
	 */
	function printInputPresetButtonHtml($name, $text, $sep='') { /* {{{ */
?>
	<span id="<?php echo $name; ?>_btn" class="inputpreset_btn" style="cursor: pointer;" title="<?php printMLText("takeOverAttributeValue"); ?>" data-ref="<?php echo $name; ?>" data-text="<?php echo is_array($text) ? implode($sep, $text) : htmlspecialchars($text);?>"<?php if($sep) echo "data-sep=\"".$sep."\""; ?>><i class="icon-arrow-left"></i></span>
<?php
	} /* }}} */

	/**
	 * Javascript code for input preset button
	 * This code workѕ for input fields and single select fields
	 */
	function printInputPresetButtonJs() { /* {{{ */
?>
$(document).ready( function() {
	$('.inputpreset_btn').click(function(ev){
		ev.preventDefault();
		if (typeof $(ev.currentTarget).data('text') != 'undefined') {
			target = $(ev.currentTarget).data('ref');
			value = $(ev.currentTarget).data('text');
			sep = $(ev.currentTarget).data('sep');
			if(sep) {
				// Use attr() instead of data() because data() converts to int which cannot be split
				arr = value.split(sep);
				for(var i in arr) {
					$("#"+target+" option[value='"+arr[i]+"']").attr("selected", "selected");
				}
			} else {
				$("#"+target).val(value);
			}
		}
	});
});
<?php
	} /* }}} */

	/**
	 * Output left-arrow with link which takes over a boolean value
	 * into a checkbox field.
	 *
	 * Clicking on the button will preset the checkbox
	 * in data-ref the value of the input field with name $name
	 *
	 * @param string $name id of select box
	 * @param string $text text
	 */
	function printCheckboxPresetButtonHtml($name, $text) { /* {{{ */
?>
	<span id="<?php echo $name; ?>_btn" class="checkboxpreset_btn" style="cursor: pointer;" title="<?php printMLText("takeOverAttributeValue"); ?>" data-ref="<?php echo $name; ?>" data-text="<?php echo is_array($text) ? implode($sep, $text) : htmlspecialchars($text);?>"<?php if($sep) echo "data-sep=\"".$sep."\""; ?>><i class="icon-arrow-left"></i></span>
<?php
	} /* }}} */

	/**
	 * Javascript code for checkboxt preset button
	 * This code workѕ for checkboxes
	 */
	function printCheckboxPresetButtonJs() { /* {{{ */
?>
$(document).ready( function() {
	$('.checkboxpreset_btn').click(function(ev){
		ev.preventDefault();
		if (typeof $(ev.currentTarget).data('text') != 'undefined') {
			target = $(ev.currentTarget).data('ref');
			value = $(ev.currentTarget).data('text');
			if(value) {
				$("#"+target).attr('checked', '');
			} else {
				$("#"+target).removeAttribute('checked');
			}
		}
	});
});
<?php
	} /* }}} */

	/**
	 * Return HTML of a single row in the document list table
	 *
	 * @param object $document
	 * @param object $previewer
	 * @param boolean $skipcont set to true if embrasing tr shall be skipped
	 */
	function documentListRow($document, $previewer, $skipcont=false, $version=0) { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		//$showtree = $this->params['showtree'];
		$workflowmode = $this->params['workflowmode'];
		$previewwidth = $this->params['previewWidthList'];
		$enableClipboard = $this->params['enableclipboard'];

		$content = '';

		$owner = $document->getOwner();
		$comment = $document->getComment();
		if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
		$docID = $document->getID();

		if(!$skipcont)
			$content .= "<tr id=\"table-row-document-".$docID."\" class=\"table-row-document\" rel=\"document_".$docID."\" formtoken=\"".createFormKey('movedocument')."\" draggable=\"true\">";

		if($version)
			$latestContent = $document->getContentByVersion($version);
		else
			$latestContent = $document->getLatestContent();

		if($latestContent) {
			$previewer->createPreview($latestContent);
			$version = $latestContent->getVersion();
			$status = $latestContent->getStatus();
			$needwkflaction = false;
			if($workflowmode == 'advanced') {
				$workflow = $latestContent->getWorkflow();
				if($workflow) {
					$needwkflaction = $latestContent->needsWorkflowAction($user);
				}
			}
			
			/* Retrieve attacheѕ files */
			$files = $document->getDocumentFiles();

			/* Retrieve linked documents */
			$links = $document->getDocumentLinks();
			$links = SeedDMS_Core_DMS::filterDocumentLinks($user, $links);

			$content .= "<td class=\"align-center\">";
			if (file_exists($dms->contentDir . $latestContent->getPath())) { 

				/*************** If the document status is equal to "released" the download will be available ***************/
				if ($status['status'] == 2 && !$document->isLocked()) { 
					$content .= "<a draggable=\"false\" href=\"/op/op.Download.php?documentid=".$docID."&version=".$version."\">";
					if($previewer->hasPreview($latestContent)) {
						$content .= "<img draggable=\"false\" class=\"mimeicon\" width=\"".$previewwidth."\"src=\"/op/op.Preview.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."&width=".$previewwidth."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
					} else {
						//$content .= "<i class=\"fa fa-file-pdf-o fa-2x\"></i>";
						$content .= "<img draggable=\"false\" class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
					}
					$content .= "</a>";
				}
				/******************************************************************************************************/

			} else {

					if($previewer->hasPreview($latestContent)) {
						$content .= "<img draggable=\"false\" class=\"mimeicon\" width=\"".$previewwidth."\"src=\"/op/op.Preview.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."&width=".$previewwidth."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
					} else {
						//$content .= "<i class=\"fa fa-file-pdf-o fa-2x\"></i>";
						$content .= "<img draggable=\"false\" class=\"mimeicon\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
					}

			} 

			$content .= "</td>";

			$content .= "<td>";

			////////////////
			if ($status['status'] == 2 && !$document->isLocked()) {
				if (htmlspecialchars($latestContent->getMimeType()) == 'application/pdf' ) {
					$content .= "<a href=\"#\" draggable=\"false\" class=\"preview-doc-btn btn-action doc-link\" id=\"".$docID."\" rel=\"".$latestContent->getVersion()."\" title=\"".htmlspecialchars($document->getName())." - ".getMLText("current_version").": ".$latestContent->getVersion()."\">" . htmlspecialchars($document->getName()) . "</a>";
				}	else {
					$content .= "<a draggable=\"false\" class=\"doc-link\" target=\"_self\" href=\"../op/op.ViewOnline.php?documentid=".$docID."&version=". $latestContent->getVersion()."\">" . htmlspecialchars($document->getName()) . "</a>";
				}

			} else {
				$content .= "<a href=\"#\" class=\"doc-link doc-disable\" draggable=\"false\" class=\"\" id=\"".$docID."\" rel=\"".$latestContent->getVersion()."\">" . htmlspecialchars($document->getName()) . "</a>";
			}
			////////////////

			$content .= "<br/><span style=\"font-size: 85%; font-style: italic; color: #666; \">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('version')." <b>".$version."</b> - ".($document->expires() ? ", ".getMLText('expires').": <b>".getReadableDate($document->getExpires())."</b>" : "")."</span>";

			if($comment) {
				$content .= "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
			}

			$content .= "</td>\n";

			$content .= "<td nowrap>";
			$attentionstr = '';
			if ( $document->isLocked() ) {
				$attentionstr .= "<img src=\"".$this->getImgPath("lock.png")."\" title=\"". getMLText("locked_by").": ".htmlspecialchars($document->getLockingUser()->getFullName())."\"> ";
			}
			if ( $needwkflaction ) {
				$attentionstr .= "<img src=\"".$this->getImgPath("attention.gif")."\" title=\"". getMLText("workflow").": ".htmlspecialchars($workflow->getName())."\"> ";
			}
			if($attentionstr)
				$content .= $attentionstr."<br />";
			$content .= "<small>";
			if(count($files))
				$content .= count($files)." ".getMLText("linked_files")."<br />";
			if(count($links))
				$content .= count($links)." ".getMLText("linked_documents")."<br />";
			if($status["status"] == S_IN_WORKFLOW && $workflowmode == 'advanced') {
				$workflowstate = $latestContent->getWorkflowState();
				if ($workflowstate) {
					$content .= '<span title="'.getOverallStatusText($status["status"]).': '.$workflowstate->getName().'">'.$workflowstate->getName().'</span>';
				}
			} else {
				$content .= getOverallStatusText($status["status"]);
			}
			$content .= "</small></td>";
//				$content .= "<td>".$version."</td>";
			$content .= "<td>";
			$content .= "<div class=\"list-action\">";
			if($document->getAccessMode($user) >= M_ALL) {
				$content .= $this->printDeleteDocumentButton($document, 'splash_rm_document', true);
			} else {
				$content .= '<span style="padding: 2px; color: #CCC;"><i class="icon-remove"></i></span>';
			}
			if($document->getAccessMode($user) >= M_READWRITE) {
				$content .= '<a type="button" href="/out/out.EditDocument.php?documentid='.$docID.'&showtree=1" class="btn btn-success btn-sm btn-action" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("edit_document_props").'"><i class="fa fa-pencil"></i></a>';
			} 

			if($document->getAccessMode($user) >= M_READWRITE) {
				$content .= $this->printLockButton($document, 'splash_document_locked', 'splash_document_unlocked', true);
			}
			if($enableClipboard) {
				$content .= '<a type="button" class="btn btn-success btn-sm addtoclipboard btn-action" rel="D'.$docID.'" msg="'.getMLText('splash_added_to_clipboard').'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("add_to_clipboard").'"><i class="fa fa-copy"></i></a>';
			}

			////////
			$content .= "<a type=\"button\" class=\"btn btn-info btn-sm\" href=\"out.ViewDocument.php?documentid=".$docID."\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".getMLText("view")."\"><i class=\"fa fa-eye\"></i></a>";
			/*if ($status['status'] == 2 ) {
				if (htmlspecialchars($latestContent->getMimeType()) == 'application/pdf' ) {
					$content .= '<a type="button" class="btn btn-info btn-sm preview-doc-btn btn-action" id="'.$docID.'" rel="'.$latestContent->getVersion().'" title="'.htmlspecialchars($document->getName()).' - '.getMLText('current_version').': '.$latestContent->getVersion().'"><i class="fa fa-eye"></i></a>';
				}	
			}*/
			/////////

			if($document->getAccessMode($user) >= M_ALL) {
				$content .= '<a type="button" href="/out/out.DocumentAccess.php?documentid='.$docID.'&showtree=1" class="btn btn-success btn-sm access-folder-btn btn-action " rel="'.$docID.'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("edit_document_access").'"><i class="fa fa-user-times"></i></a>';
			}

			if($document->getAccessMode($user) >= M_READWRITE) {
				$content .= '<a type="button" class="btn btn-primary btn-sm move-doc-btn btn-action" rel="'.$docID.'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("move_document").'"><i class="fa fa-arrows"></i></a>';				
			}

			$content .= "</div>";
			$content .= "</td>";
		}
		if(!$skipcont)
			$content .= "</tr>\n";

		return $content;
	} /* }}} */

	function documentMoveOption($document){ /* {{{ */
		$content = '';
		$content .= "<tr>";
		$content .= "<td>";
		$content .= "</td>";
		$content .= "</tr>";
	} /* }}} */

	function folderListRow($subFolder) { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
//		$folder = $this->params['folder'];
		$showtree = $this->params['showtree'];
		$enableRecursiveCount = $this->params['enableRecursiveCount'];
		$maxRecursiveCount = $this->params['maxRecursiveCount'];
		$enableClipboard = $this->params['enableclipboard'];

		$owner = $subFolder->getOwner();
		$comment = $subFolder->getComment();
		if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";

		$content = '';
		$content .= "<tr id=\"table-row-folder-".$subFolder->getID()."\" draggable=\"true\" rel=\"folder_".$subFolder->getID()."\" class=\"folder table-row-folder\" formtoken=\"".createFormKey('movefolder')."\">";
	//	$content .= "<td><img src=\"images/folder_closed.gif\" width=18 height=18 border=0></td>";
		$content .= "<td class=\"align-center\"><a _rel=\"folder_".$subFolder->getID()."\" draggable=\"false\" href=\"out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\"><i class=\"fa fa-folder fa-2x\"></i></a></td>\n";
		$content .= "<td><a draggable=\"false\" _rel=\"folder_".$subFolder->getID()."\" href=\"out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\">" . htmlspecialchars($subFolder->getName()) . "</a>";
		$content .= "<br /><span style=\"font-size: 85%; font-style: italic; color: #666;\">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('creation_date').": <b>".date('Y-m-d', $subFolder->getDate())."</b></span>";
		if($comment) {
			$content .= "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
		}
		$content .= "</td>\n";
//		$content .= "<td>".htmlspecialchars($owner->getFullName())."</td>";
		$content .= "<td colspan=\"1\" nowrap><small>";
		if($enableRecursiveCount) {
			if($user->isAdmin()) {
				/* No need to check for access rights in countChildren() for
				 * admin. So pass 0 as the limit.
				 */
				$cc = $subFolder->countChildren($user, 0);
				$content .= $cc['folder_count']." ".getMLText("folders")."<br />".$cc['document_count']." ".getMLText("documents");
			} else {
				$cc = $subFolder->countChildren($user, $maxRecursiveCount);
				if($maxRecursiveCount > 5000)
					$rr = 100.0;
				else
					$rr = 10.0;
				$content .= (!$cc['folder_precise'] ? '~'.(round($cc['folder_count']/$rr)*$rr) : $cc['folder_count'])." ".getMLText("folders")."<br />".(!$cc['document_precise'] ? '~'.(round($cc['document_count']/$rr)*$rr) : $cc['document_count'])." ".getMLText("documents");
			}
		} else {
			/* FIXME: the following is very inefficient for just getting the number of
			 * subfolders and documents. Making it more efficient is difficult, because
			 * the access rights need to be checked.
			 */
			$subsub = $subFolder->getSubFolders();
			$subsub = SeedDMS_Core_DMS::filterAccess($subsub, $user, M_READ);
			$subdoc = $subFolder->getDocuments();
			$subdoc = SeedDMS_Core_DMS::filterAccess($subdoc, $user, M_READ);
			$content .= count($subsub)." ".getMLText("folders")."<br />".count($subdoc)." ".getMLText("documents");
		}
		$content .= "</small></td>";
//		$content .= "<td></td>";
		$content .= "<td>";
		$content .= "<div class=\"list-action\">";
		if($subFolder->getAccessMode($user) >= M_ALL) {
			$content .= $this->printDeleteFolderButton($subFolder, 'splash_rm_folder', true);
		}

		if($subFolder->getAccessMode($user) >= M_READWRITE) {
			$content .= '<a type="button" href="/out/out.EditFolder.php?folderid='.$subFolder->getID().'" class="btn btn-success btn-sm btn-action " data-toggle="tooltip" data-placement="bottom" title="'.getMLText("edit_folder_props").'"><i class="fa fa-pencil"></i></a>';
		}

		if($subFolder->getAccessMode($user) >= M_ALL) {
			$content .= '<a type="button" class="btn btn-primary btn-sm move-folder-btn btn-action" rel="'.$subFolder->getID().'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("move_folder").'"><i class="fa fa-arrows"></i></a>';
		}

		if($subFolder->getAccessMode($user) >= M_ALL) {
			$content .= '<a type="button" href="/out/out.FolderAccess.php?folderid='.$subFolder->getID().'&showtree=1" class="btn btn-warning btn-sm access-folder-btn btn-action " rel="'.$subFolder->getID().'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("edit_folder_access").'"><i class="fa fa-user-times"></i></a>';
		}

		if($enableClipboard) {
			$content .= '<a type="button" class="btn btn-default btn-sm addtoclipboard btn-action" rel="F'.$subFolder->getID().'" msg="'.getMLText('splash_added_to_clipboard').'" data-toggle="tooltip" data-placement="bottom" title="'.getMLText("add_to_clipboard").'"><i class="fa fa-clone"></i></a>';
		}
		$content .= "</div>";
		$content .= "</td>";
		$content .= "</tr>\n";
		return $content;
	} /* }}} */

	/**
	 * Output HTML Code for jumploader
	 *
	 * @param string $uploadurl URL where post data is send
	 * @param integer $folderid id of folder where document is saved
	 * @param integer $maxfiles maximum number of files allowed to upload
	 * @param array $fields list of post fields
	 */
	function printUploadApplet($uploadurl, $attributes, $maxfiles=0, $fields=array()){ /* {{{ */
?>
<applet id="jumpLoaderApplet" name="jumpLoaderApplet"
code="jmaster.jumploader.app.JumpLoaderApplet.class"
archive="jl_core_z.jar"
width="715"
height="400"
mayscript>
  <param name="uc_uploadUrl" value="<?php echo $uploadurl; ?>"/>
  <param name="ac_fireAppletInitialized" value="true"/>
  <param name="ac_fireUploaderSelectionChanged" value="true"/>
  <param name="ac_fireUploaderFileStatusChanged" value="true"/>
  <param name="ac_fireUploaderFileAdded" value="true"/>
  <param name="uc_partitionLength" value="<?php echo $this->params['partitionsize'] ?>"/>
<?php
	if($maxfiles) {
?>
  <param name="uc_maxFiles" value="<?php echo $maxfiles ?>"/>
<?php
	}
?>
</applet>
<div id="fileLinks">
</div>

<!-- callback methods -->
<script language="javascript">
    /**
     * applet initialized notification
     */
    function appletInitialized(applet) {
        var uploader = applet.getUploader();
        var attrSet = uploader.getAttributeSet();
        var attr;
<?php
	foreach($attributes as $name=>$value) {
?>
        attr = attrSet.createStringAttribute( '<?php echo $name ?>', '<?php echo $value ?>' );
        attr.setSendToServer(true);
<?php
	}
?>
    }
    /**
     * uploader selection changed notification
     */
    function uploaderSelectionChanged( uploader ) {
        dumpAllFileAttributes();
    }
    /**
     * uploader file added notification
     */
    function uploaderFileAdded( uploader ) {
        dumpAllFileAttributes();
    }
    /**
     * file status changed notification
     */
    function uploaderFileStatusChanged( uploader, file ) {
        traceEvent( "uploaderFileStatusChanged, index=" + file.getIndex() + ", status=" + file.getStatus() + ", content=" + file.getResponseContent() );
        if( file.isFinished() ) { 
            var serverFileName = file.getId() + "." + file.getName(); 
            var linkHtml = "<a href='/uploaded/" + serverFileName + "'>" + serverFileName + "</a> " + file.getLength() + " bytes"; 
            var container = document.getElementById( "fileLinks"); 
            container.innerHTML += linkHtml + "<br />"; 
        } 
    }
    /**
     * trace event to events textarea
     */
    function traceEvent( message ) {
        document.debugForm.txtEvents.value += message + "\r\n";
    }
</script>

<!-- debug auxiliary methods -->
<script language="javascript">
    /**
     * list attributes of file into html
     */
    function listFileAttributes( file, edit, index ) {
        var attrSet = file.getAttributeSet();
        var content = "";
        var attr;
				var value;
				if(edit)
					content += "<form name='form" + index + "' id='form" + index + "' action='#' >";
        content += "<table>";
				content += "<tr class='dataRow' colspan='2'><td class='dataText'><b>" + file.getName() + "</b></td></tr>";

<?php
	if(!$fields || (isset($fields['name']) && $fields['name'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('name') ?></td>";
				if(attr = attrSet.getAttributeByName('name'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<input id='name" + index + "' name='name' type='input' value='" + value + "' />";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['comment']) && $fields['comment'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('comment') ?></td>";
				if(attr = attrSet.getAttributeByName('comment'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<textarea id='comment" + index + "' name='comment' cols='40' rows='2'>" + value + "</textarea>";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['reqversion']) && $fields['reqversion'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('version') ?></td>";
				if(attr = attrSet.getAttributeByName('reqversion'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<input id='reqversion" + index + "' name='reqversion' type='input' value='" + value + "' />";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['version_comment']) && $fields['version_comment'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('comment_for_current_version') ?></td>";
				if(attr = attrSet.getAttributeByName('version_comment'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<textarea id='version_comment" + index + "' name='version_comment' cols='40' rows='2'>" + value + "</textarea>";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['keywords']) && $fields['keywords'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('keywords') ?></td>";
				if(attr = attrSet.getAttributeByName('keywords'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit) {
					value = "<textarea id='keywords" + index + "' name='keywords' cols='40' rows='2'>" + value + "</textarea>";
					value += "<br /><a href='javascript:chooseKeywords(\"form" + index + ".keywords" + index +"\");'><?php echo getMLText("use_default_keywords");?></a>";
				}
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['categories']) && $fields['categories'])) {
?>
				content += "<tr class='dataRow'>";
				content += "<td class='dataField'><?php echo getMLText('categories') ?></td>";
				if(attr = attrSet.getAttributeByName('categoryids'))
					value = attr.getStringValue();
				else
					value = '';
				if(attr = attrSet.getAttributeByName('categorynames'))
					value2 = attr.getStringValue();
				else
					value2 = '';
				if(edit) {
					value = "<input type='hidden' id='categoryidform" + index + "' name='categoryids' value='" + value + "' />";
					value += "<input disabled id='categorynameform" + index + "' name='categorynames' value='" + value2 + "' />";
					value += "<br /><a href='javascript:chooseCategory(\"form" + index + "\", \"\");'><?php echo getMLText("use_default_categories");?></a>";
				} else {
					value = value2;
				}
        content += "<td class='dataText'>" + value + "</td>";
				content += "</tr>";
<?php
	}
?>

				if(edit) {
					content += "<tr class='dataRow'>";
					content += "<td class='dataField'></td>";
					content += "<td class='dataText'><input type='button' value='Set' onClick='updateFileAttributes("+index+")'/></td>";
					content += "</tr>";
        	content += "</table>";
        	content += "</form>";
				} else {
        	content += "</table>";
				}
        return content;
    }
    /**
     * return selected file if and only if single file selected
     */
    function getSelectedFile() {
        var file = null;
        var uploader = document.jumpLoaderApplet.getUploader();
        var selection = uploader.getSelection();
        var numSelected = selection.getSelectedItemCount();
        if( numSelected == 1 ) {
            var selectedIndex = selection.getSelectedItemIndexAt( 0 );
            file = uploader.getFile( selectedIndex );
        }
        return file;
    }
    /**
     * dump attributes of all files into html
     */
     function dumpAllFileAttributes() {
         var content = "";
         var uploader = document.jumpLoaderApplet.getUploader();
         var files = uploader.getAllFiles();
         var file = getSelectedFile();
				 if(file) {
					 for (var i = 0; i < uploader.getFileCount() ; i++) { 
						 if(uploader.getFile(i).getIndex() == file.getIndex())
							 content += listFileAttributes( uploader.getFile(i), 1, i );
						 else
							 content += listFileAttributes( uploader.getFile(i), 0, i );
					 }
					 document.getElementById( "fileList" ).innerHTML = content;
				 }
    }
     /**
      * update attributes for the selected file
      */
      function updateFileAttributes(index) {
        var uploader = document.jumpLoaderApplet.getUploader();
        var file = uploader.getFile( index );
        if( file != null ) {
				  var attr;
					var value;
          var attrSet = file.getAttributeSet();
					value = document.getElementById("name"+index);
          attr = attrSet.createStringAttribute( 'name', (value.value) ? value.value : "" );
          attr.setSendToServer( true );
					value = document.getElementById("comment"+index);
          attr = attrSet.createStringAttribute( 'comment', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("reqversion"+index);
          attr = attrSet.createStringAttribute( 'reqversion', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("version_comment"+index);
          attr = attrSet.createStringAttribute( 'version_comment', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("keywords"+index);
          attr = attrSet.createStringAttribute( 'keywords', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					value = document.getElementById("categoryidform"+index);
          attr = attrSet.createStringAttribute( 'categoryids', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					value = document.getElementById("categorynameform"+index);
          attr = attrSet.createStringAttribute( 'categorynames', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					dumpAllFileAttributes();
        } else {
            alert( "Single file should be selected" );
        }
     }
</script>
<form name="debugForm">
<textarea name="txtEvents" style="visibility: hidden;width:715px; font:10px monospace" rows="1" wrap="off" id="txtEvents"></textarea></p>
</form>
<p></p>
<p id="fileList"></p>
<?php
	} /* }}} */

	function show(){ /* {{{ */
		parent::show();
	} /* }}} */

	/**
	 * Output a protocol
	 *
	 * @param object $attribute attribute
	 */
	protected function printProtocol($latestContent, $type="") { /* {{{ */
		$dms = $this->params['dms'];
		$document = $latestContent->getDocument();
?>
		<legend><?php printMLText($type.'_log'); ?></legend>
		<table class="table condensed">
			<tr><th><?php printMLText('name'); ?></th><th><?php printMLText('last_update'); ?>, <?php printMLText('comment'); ?></th><th><?php printMLText('status'); ?></th></tr>
<?php
		switch($type) {
		case "review":
			$statusList = $latestContent->getReviewStatus(10);
			break;
		case "approval":
			$statusList = $latestContent->getApprovalStatus(10);
			break;
		default:
			$statusList = array();
		}
		foreach($statusList as $rec) {
			echo "<tr>";
			echo "<td>";
			switch ($rec["type"]) {
				case 0: // individual.
					$required = $dms->getUser($rec["required"]);
					if (!is_object($required)) {
						$reqName = getMLText("unknown_user")." '".$rec["required"]."'";
					} else {
						$reqName = htmlspecialchars($required->getFullName()." (".$required->getLogin().")");
					}
					break;
				case 1: // Approver is a group.
					$required = $dms->getGroup($rec["required"]);
					if (!is_object($required)) {
						$reqName = getMLText("unknown_group")." '".$rec["required"]."'";
					}
					else {
						$reqName = "<i>".htmlspecialchars($required->getName())."</i>";
					}
					break;
			}
			echo $reqName;
			echo "</td>";
			echo "<td>";
			echo "<i style=\"font-size: 80%;\">".$rec['date']." - ";
			$updateuser = $dms->getUser($rec["userID"]);
			if(!is_object($required))
				echo getMLText("unknown_user");
			else
				echo htmlspecialchars($updateuser->getFullName()." (".$updateuser->getLogin().")");
			echo "</i>";
			if($rec['comment'])
				echo "<br />".htmlspecialchars($rec['comment']);
			switch($type) {
			case "review":
				if($rec['file']) {
					echo "<br />";
					echo "<a href=\"/op/op.Download.php?documentid=".$document->getID()."&reviewlogid=".$rec['reviewLogID']."\" class=\"btn btn-mini\"><i class=\"icon-download\"></i> ".getMLText('download')."</a>";
				}
				break;
			case "approval":
				if($rec['file']) {
					echo "<br />";
					echo "<a href=\"/op/op.Download.php?documentid=".$document->getID()."&approvelogid=".$rec['approveLogID']."\" class=\"btn btn-mini\"><i class=\"icon-download\"></i> ".getMLText('download')."</a>";
				}
				break;
			}
			echo "</td>";
			echo "<td>";
			switch($type) {
			case "review":
				echo getReviewStatusText($rec["status"]);
				break;
			case "approval":
				echo getApprovalStatusText($rec["status"]);
				break;
			default:
			}
			echo "</td>";
			echo "</tr>";
		}
?>
				</table>
<?php
	} /* }}} */

	/**
	 * Show progressbar
	 *
	 * @param double $value value
	 * @param double $max 100% value
	 */
	protected function getProgressBar($value, $max=100.0) { /* {{{ */
		if($max > $value) {
			$used = (int) ($value/$max*100.0+0.5);
			$free = 100-$used;
		} else {
			$free = 0;
			$used = 100;
		}
		$html = '
		<div class="progress">
			<div class="bar bar-danger" style="width: '.$used.'%;"></div>
		  <div class="bar bar-success" style="width: '.$free.'%;"></div>
		</div>';
		return $html;
	} /* }}} */

	/**
	 * Output a timeline for a document
	 *
	 * @param object $document document
	 */
	protected function printTimelineJs($timelineurl, $height=300, $start='', $end='', $skip=array()) { /* {{{ */
		if(!$timelineurl)
			return;
?>
		var timeline;
		var data;

		// specify options
		var options = {
			'width':  '100%',
			'height': '100%',
<?php
		if($start) {
			$tmp = explode('-', $start);
			echo "\t\t\t'min': new Date(".$tmp[0].", ".($tmp[1]-1).", ".$tmp[2]."),\n";
		}
		if($end) {
			$tmp = explode('-', $end);
			echo "'\t\t\tmax': new Date(".$tmp[0].", ".($tmp[1]-1).", ".$tmp[2]."),\n";
		}
?>
			'editable': false,
			'selectable': true,
			'style': 'box',
			'locale': '<?php echo $this->params['session']->getLanguage() ?>',

		};

		function onselect() {
			var sel = timeline.getSelection();
			if (sel.length) {
				if (sel[0].row != undefined) {
					var row = sel[0].row;
					/*console.log(timeline.getItem(sel[0].row));*/
					item = timeline.getItem(sel[0].row);
					console.log(item);
					$('div.ajax').trigger('update', {documentid: item.docid, version: item.version, statusid: item.statusid, statuslogid: item.statuslogid, fileid: item.fileid});
				}
			}
		}
		$(document).ready(function () {
		// Instantiate our timeline object.
		timeline = new links.Timeline(document.getElementById('timeline'), options);
		links.events.addListener(timeline, 'select', onselect);
		$.getJSON(
			'<?php echo $timelineurl ?>', 
			function(data) {
				$.each( data, function( key, val ) {
					val.start = new Date(val.start);
				});
				timeline.draw(data);
			}
		);
		});
<?php
	} /* }}} */

	protected function printTimelineHtml($height) { /* {{{ */
?>
	<div id="timeline" style="height: <?php echo $height ?>px;"></div>
<?php
	} /* }}} */

	protected function printTimeline($timelineurl, $height=300, $start='', $end='', $skip=array()) { /* {{{ */
		echo "<script type=\"text/javascript\">\n";
		$this->printTimelineJs($timelineurl, $height, $start, $end, $skip);
		echo "</script>";
		$this->printTimelineHtml($height);
	} /* }}} */

	protected function printPopupBox($title, $content, $ret=false) { /* {{{ */
		$id = md5(uniqid());
		/*
		$this->addFooterJS('
$("body").on("click", "span.openpopupbox", function(e) {
	$(""+$(e.target).data("href")).toggle();
//	$("div.popupbox").toggle();
});
');
		 */
		$html = '
		<span class="openpopupbox" data-href="#'.$id.'">'.$title.'</span>
		<div id="'.$id.'" class="popupbox" style="display: none;">
		'.$content.'
			<span class="closepopupbox"><i class="icon-remove"></i></span>
		</div>';
		if($ret)
			return $html;
		else
			echo $html;
	} /* }}} */

	protected function printAccordion($title, $content) { /* {{{ */
		$id = substr(md5(uniqid()), 0, 4);
?>
		<div class="accordion" id="accordion<?php echo $id; ?>">
      <div class="accordion-group">
        <div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion<?php echo $id; ?>" href="#collapse<?php echo $id; ?>">
						<?php echo $title; ?>
          </a>
        </div>
				<div id="collapse<?php echo $id; ?>" class="accordion-body collapse" style="height: 0px;">
          <div class="accordion-inner">
<?php
		echo $content;
?>
          </div>
        </div>
      </div>
    </div>
<?php
	} /* }}} */

	/*  testing */
	function printTree($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0, $orderby='') { /* {{{ */
		function tree($path, $folder, $user, $accessmode, $showdocs=1, $expandtree=0, $orderby='') {
			if($path || $expandtree) {
				if($path)
					$pathfolder = array_shift($path);
				$subfolders = $folder->getSubFolders($orderby);
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>($subfolder->hasSubFolders() || ($subfolder->hasDocuments() && $showdocs)) ? true : false, 'is_folder'=>true);
					if($expandtree || $pathfolder->getID() == $subfolder->getID()) {
						if($showdocs) {
							$documents = $folder->getDocuments($orderby);
							$documents = SeedDMS_Core_DMS::filterAccess($documents, $user, $accessmode);
							foreach($documents as $document) {
								$node2 = array('label'=>$document->getName(), 'id'=>$document->getID(), 'is_folder'=>false);
								$children[] = $node2;
							}
						}
						$node['children'] = tree($path, $subfolder, $user, $accessmode, $showdocs, $expandtree, $orderby);
					}
					$children[] = $node;
				}
				return $children;
			} else {
				$subfolders = $folder->getSubFolders($orderby);
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>($subfolder->hasSubFolders() || ($subfolder->hasDocuments() && $showdocs)) ? true : false, 'is_folder'=>true);
					$children[] = $node;
				}
				return $children;

			}
			return array();
		}

		if($folderid) {
			$folder = $this->params['dms']->getFolder($folderid);
			$path = $folder->getPath();
			$folder = array_shift($path);
			$node = array('label'=>$folder->getName(), 'id'=>$folder->getID(), 'is_folder'=>true);
			if(!$folder->hasSubFolders()) {
				$node['load_on_demand'] = false;
				$node['children'] = array();
			} else {
				$node['children'] = tree($path, $folder, $this->params['user'], $accessmode, $showdocs, $expandtree, $orderby);
				if($showdocs) {
					$documents = $folder->getDocuments($orderby);
					$documents = SeedDMS_Core_DMS::filterAccess($documents, $this->params['user'], $accessmode);
					foreach($documents as $document) {
						$node2 = array('label'=>$document->getName(), 'id'=>$document->getID(), 'is_folder'=>false);
						$node['children'][] = $node2;
					}
				}
			}
			/* Nasty hack to remove the highest folder */
			if(isset($this->params['remove_root_from_tree']) && $this->params['remove_root_from_tree']) {
				foreach($node['children'] as $n)
					$tree[] = $n;
			} else {
				$tree[] = $node;
			}
			
		} else {
			$root = $this->params['dms']->getFolder($this->params['rootfolderid']);
			$tree = array(array('label'=>$root->getName(), 'id'=>$root->getID(), 'is_folder'=>true));
		}

		return $tree;

	} /* }}} */


}
?>
