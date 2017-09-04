<?php
/**
 * Implementation of EditUserData view
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
 * Class which outputs the html page for EditUserData view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditUserPassword extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');
?>
function checkForm()
{
	msg = new Array();
	if ($("#currentpwd").val() == "") msg.push("<?php printMLText("js_no_pwd");?>");
	if ($("#pwd").val() == "") msg.push("<?php printMLText("js_no_pwd");?>");
	if ($("#pwdconf").val() == "") msg.push("<?php printMLText("js_no_pwd");?>");

	if ($("#pwd").val() != $("#pwdconf").val()) msg.push("<?php printMLText("js_pwd_not_conf");?>");
	if ($("#fullname").val() == "") msg.push("<?php printMLText("js_no_name");?>");
	if ($("#email").val() == "") msg.push("<?php printMLText("js_no_email");?>");
//	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
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

$(document).ready( function() {

	$('body').on('submit', '#form', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
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
		rules: {
			fullname: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			pwdconf: {
				equalTo: "#pwd"
			}
		},
		messages: {
			fullname: "<?php printMLText("js_no_name");?>",
			email: {
				required: "<?php printMLText("js_no_email");?>",
				email: "<?php printMLText("js_invalid_email");?>"
			},
			pwdconf: "<?php printMLText("js_unequal_passwords");?>",
		},
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$enableuserimage = $this->params['enableuserimage'];
		$enablelanguageselector = $this->params['enablelanguageselector'];
		$enablethemeselector = $this->params['enablethemeselector'];
		$passwordstrength = $this->params['passwordstrength'];
		$httproot = $this->params['httproot'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("my_account"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    <?php 
    $this->startBoxPrimary(getMLText("edit_user_details"));
?>
<form action="../op/op.EditUserData.php" enctype="multipart/form-data" method="post" id="form">
	<div class="form-group">
		<label><?php printMLText("current_password");?>:</label>
		<div class="controls">
			<input class="form-control" id="currentpwd" type="password" name="currentpwd" size="30">
		</div>
	</div>
	<div class="form-group">
		<label><?php printMLText("new_password");?>:</label>
		<div class="controls">
			<input class="pwd form-control" type="password" rel="strengthbar" id="pwd" name="pwd" size="30">
		</div>
	</div>
<?php
	if($passwordstrength) {
?>
	<div class="form-group">
		<label><?php printMLText("password_strength");?>:</label>
		<div class="controls">
			<div id="strengthbar" class="progress" style="width: 220px; height: 30px; margin-bottom: 8px;"><div class="bar bar-danger" style="width: 0%;"></div></div>
		</div>
	</div>
<?php
	}
?>
	<div class="form-group">
		<label><?php printMLText("confirm_pwd");?>:</label>
		<div class="controls">
			<input class="form-control" id="pwdconf" type="Password" id="pwdconf" name="pwdconf" size="30">
		</div>
	</div>

	<div class="box-footer">
		<button class="btn history-back"><?php echo getMLText('back'); ?></button>
		<button class="btn btn-info" type="submit"><i class="fa fa-save"></i> <?php printMLText("save");?></button>
	</div>
</form>

<?php $this->endsBoxPrimary(); ?>

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
