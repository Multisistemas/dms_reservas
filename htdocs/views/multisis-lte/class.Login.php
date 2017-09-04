<?php
/**
 * Implementation of Login view
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
 * Class which outputs the html page for Login view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Login extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
?>
document.form1.login.focus();
function checkForm()
{
	msg = new Array()
	if($("#login").val() == "") msg.push("<?php printMLText("js_no_login");?>");
	if($("#pwd").val() == "") msg.push("<?php printMLText("js_no_pwd");?>");
	if (msg != "") {
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}

function guestLogin()
{
	theme = "multisis-lte";
	/*theme = $("#themeselector").val();*/
	lang = $("#languageselector").val();
	url = "../op/op.Login.php?login=guest";
	if(theme)
		url += "&sesstheme=" + theme;
	if(lang)
		url += "&lang=" + lang;
	if (document.form1.referuri) {
		url += "&referuri=" + escape(document.form1.referuri.value);
	}
	document.location.href = url;
}

$(document).ready( function() {
/*
	$('body').on('submit', '#form', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
*/
	$('body').on('click', '#guestlogin', function(ev){
		ev.preventDefault();
		guestLogin();
	});
	$("#form").validate({
		invalidHandler: function(e, validator) {
			noty({
				text:  (validator.numberOfInvalids() == 1) ? "<?php printMLText("js_form_error");?>".replace('#', validator.numberOfInvalids()) : "<?php printMLText("js_form_errors");?>".replace('#', validator.numberOfInvalids()),
				type: 'error',
				dismissQueue: true,
				layout: 'topRight',
				theme: 'defaultTheme',
				timeout: 1500,
			});
		},
		messages: {
			login: "<?php printMLText("js_no_login");?>",
			pwd: "<?php printMLText("js_no_pwd");?>"
		},
	});

	/*$(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });*/

});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$enableguestlogin = $this->params['enableguestlogin'];
		$enablepasswordforgotten = $this->params['enablepasswordforgotten'];
		$refer = "/out/out.ViewFolder.php?folderid=1&showtree=1";
		$themes = $this->params['themes'];
		$languages = $this->params['languages'];
		$enableLanguageSelector = $this->params['enablelanguageselector'];
		$enableThemeSelector = $this->params['enablethemeselector'];

		$this->htmlAddHeader('<link rel="stylesheet" href="../../styles/'.$this->theme.'/plugins/iCheck/square/blue.css">'."\n", 'css');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');
		
		$this->htmlStartPage(getMLText("sign_in"), "hold-transition login-page");
		
		$this->startLoginContent();
?>
  
<form action="../op/op.Login.php" method="post" name="form1" id="form">
<?php
		if ($refer) {
			echo "<input type='hidden' name='referuri' value='".sanitizeString($refer)."'/>";
		}
?>
		<div class="form-group has-feedback">
			<input class="form-control" type="text" id="login" name="login" placeholder="login" autocomplete="off" required>
			<span class="glyphicon glyphicon-user form-control-feedback"></span>
		</div>
		<div class="form-group has-feedback">
			<input class="form-control" type="Password" id="pwd" name="pwd" placeholder="password" autocomplete="off" required>
			<span class="glyphicon glyphicon-lock form-control-feedback"></span>
		</div>

<?php if($enableLanguageSelector) { ?>
	<div class="form-group has-feedback">
<?php
			print "<select class=\"form-control\" id=\"languageselector\" name=\"lang\">";
			print "<option value=\"\">-";
			foreach ($languages as $currLang) {
				print "<option value=\"".$currLang."\">".getMLText($currLang)."</option>";
			}
			print "</select>";
?>
	</div>

<?php } if($enableThemeSelector) { ?>
	
	<div class="form-group has-feedback">
			
<?php

			print "<select class=\"form-control\" id=\"themeselector\" name=\"sesstheme\">";
			print "<option value=\"\">-";
			foreach ($themes as $currTheme) {
				print "<option value=\"".$currTheme."\">".$currTheme;
			}
			print "</select>";
?>
	</div>

<?php } ?>
	
	<div class="row">
    <!-- /.col -->
    <div class="">
      <button type="submit" class="btn btn-primary btn-block btn-flat"><?php printMLText("submit_login") ?></button>
    </div>
    <!-- /.col -->
  </div>
</form>
<?php
		$this->contentContainerEnd();
		$tmpfoot = array();
		if ($enableguestlogin)
			$tmpfoot[] = "<a href=\"\" id=\"guestlogin\">" . getMLText("guest_login") . "</a>\n";
		if ($enablepasswordforgotten)
			$tmpfoot[] = "<a href=\"../out/out.PasswordForgotten.php\">" . getMLText("password_forgotten") . "</a>\n";
		if($tmpfoot) {
			print "<div class=\"row\"><div class=\"col-md-12 guest-access\"><div class=\"\">";
			print implode(' | ', $tmpfoot);
			print "</div></div></div>";
		}
		
		$this->endLoginContent();
	} /* }}} */
}
?>
