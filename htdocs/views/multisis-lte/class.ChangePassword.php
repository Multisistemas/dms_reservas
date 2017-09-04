<?php
/**
 * Implementation of ChangePassword view
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
 * Class which outputs the html page for ChangePassword view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ChangePassword extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript; charset=UTF-8');
?>
document.form1.newpassword.focus();
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$referuri = $this->params['referui'];
		$hash = $this->params['hash'];
		$passwordstrength = $this->params['passwordstrength'];

		$this->htmlStartPage(getMLText("change_password"));
		
		$this->startLoginContent();

		$this->contentContainerStart();
?>
<form action="../op/op.ChangePassword.php" method="post" name="form1">
<?php
		if ($referuri) {
			echo "<input type='hidden' name='referuri' value='".$referuri."'/>";
		}
		if ($hash) {
			echo "<input type='hidden' name='hash' value='".$hash."'/>";
		}
?>

		 <div class="form-group">
			<label><?php printMLText("new_password");?>:</label>
			<div><input class="form-control" type="password" rel="strengthbar" name="newpassword" id="password"></div>
		 </div>
<?php
		if($passwordstrength > 0) {
?>
		<div class="form-group">
			<label><?php printMLText("password_strength");?>:</label>
			<div>
				<div id="strengthbar" class="progress" style="width: 220px; height: 30px; margin-bottom: 8px;"><div class="bar bar-danger" style="width: 0%;"></div></div>
			</div>
		</div>
<?php
		}
?>
		<div class="form-group">
			<label><?php printMLText("confirm_pwd");?>:</label>
			<div><input class="form-control" type="password" name="newpasswordrepeat" id="passwordrepeat"></div>
		</div>
		<div class="form-group align-center">
			<div><input class="btn btn-success" type="submit" value="<?php printMLText("submit_password") ?>"></div>
		</div>

</form>
<?php $this->contentContainerEnd(); ?>
<p class="align-center"><a type="button" class="btn btn-info" href="../out/out.Login.php"><?php echo getMLText("login"); ?></a></p>
<?php
		$this->endLoginContent();
	} /* }}} */
}
?>
