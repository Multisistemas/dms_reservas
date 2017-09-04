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
class SeedDMS_View_EditUserData extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');
?>
function checkForm()
{
	msg = new Array();
	if ($("#fullname").val() == "") msg.push("<?php printMLText("js_no_name");?>");
	if ($("#email").val() == "") msg.push("<?php printMLText("js_no_email");?>");

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
		<label><?php printMLText("name");?>:</label>
		<div class="controls">
			<input class="form-control" type="text" id="fullname" name="fullname" value="<?php print htmlspecialchars($user->getFullName());?>" size="30">
		</div>
	</div>
	<div class="form-group">
		<label><?php printMLText("email");?>:</label>
		<div class="controls">
			<input class="form-control" type="text" id="email" name="email" value="<?php print htmlspecialchars($user->getEmail());?>" size="30">
		</div>
	</div>

	<?php if ($user->_comment != "client-admin") { ?>
	<div class="form-group">
		<label><?php printMLText("biography");?>:</label>
		<div class="controls">
			<textarea class="form-control" name="comment" rows="5" cols="100"><?php print htmlspecialchars($user->getComment());?></textarea>
		</div>
	</div>
	<?php } ?>
	
<?php	
		if ($enableuserimage){	
?>	
	<div class="form-group">
		<label><?php printMLText("user_image");?>:</label>
		<div class="controls">
<?php
			if ($user->hasImage())
				print "<img src=\"".$httproot . "out/out.UserImage.php?userid=".$user->getId()."\">";
			else printMLText("no_user_image");
?>
		</div>
	</div>
	<div class="form-group">
		<label><?php printMLText("new_user_image");?>:</label>
		<div class="controls">
<?php
	$this->printFileChooser('userfile', false, "image/jpeg");
?>
		</div>
	</div>
<?php
		}

		/*if ($enablelanguageselector){
			echo "<div class=\"form-group\">";
			echo "<label>".printMLText("language")."</label>";
			echo "<div class=\"controls\">";
			echo "<select name=\"language\" class=\"form-control\">";
				$languages = getLanguages();
				foreach ($languages as $currLang) {
					print "<option value=\"".$currLang."\" ".(($user->getLanguage()==$currLang) ? "selected" : "").">".getMLText($currLang)."</option>";
				}
			echo "</select>";
			echo "</div>";
			echo "</div>";
		}*/
?>
<?php
		

		if ($enablethemeselector){	
?>
	<div class="form-group">
		<label><?php printMLText("theme");?>:</label>
		<div class="controls">
			<select name="theme" class="form-control">
<?php
			$themes = UI::getStyles();
			foreach ($themes as $currTheme) {
				print "<option value=\"".$currTheme."\" ".(($user->getTheme()==$currTheme) ? "selected" : "").">".$currTheme."</option>";
			}
?>
			</select>
		</div>
	</div>
<?php
		}
?>
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
