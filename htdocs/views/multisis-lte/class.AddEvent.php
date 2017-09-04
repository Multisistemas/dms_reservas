<?php
/**
 * Implementation of AddEvent view
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
 * Class which outputs the html page for AddEvent view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddEvent extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript; charset=UTF-8');
?>
function checkForm()
{
	msg = new Array();
	if (document.form1.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
<?php
	if (isset($settings->_strictFormCheck) && $settings->_strictFormCheck) {
?>
	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
<?php
	}
?>
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

$(document).ready(function() {
	$('body').on('submit', '#form1', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */

		$this->htmlStartPage(getMLText("add_event"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar(0,0,2);
		$this->contentStart();

		$expdate = date('Y-m-d');
?>
<div class="gap-15"></div>
<div class="row">
	<div class="col-md-12">

	<?php $this->startBoxPrimary(getMLText("add_event")); ?>

	<form action="../op/op.AddEvent.php" id="form1" name="form1" method="post">

		<div class="control-form-group">
			<label><?php printMLText("from");?>:</label>
			<div><?php //$this->printDateChooser(-1, "from");?>
    		<span class="input-append date span12" id="fromdate" data-date="<?php echo $expdate; ?>" data-date-format="yyyy-mm-dd">
      		<input class="form-control" name="from" type="text" value="<?php echo $expdate; ?>">
      		<span class="add-on"><i class="icon-calendar"></i></span>
    		</span>
			</div>
		</div>

		<div class="form-group">
			<label><?php printMLText("to");?>:</label>
			<div><?php //$this->printDateChooser(-1, "to");?>
    		<span class="input-append date span12" id="todate" data-date="<?php echo $expdate; ?>" data-date-format="yyyy-mm-dd">
      		<input class="form-control" name="to" type="text" value="<?php echo $expdate; ?>">
      		<span class="add-on"><i class="icon-calendar"></i></span>
    		</span>
			</div>
		</div>


		<div class="form-group">
			<label><?php printMLText("name");?>:</label>
			<div><input class="form-control" type="text" name="name" size="60"></div>
		</div>


		<div class="form-group">
			<label><?php printMLText("comment");?>:</label>
			<div><textarea class="form-control" name="comment" rows="4" cols="80"></textarea></div>
		</div>
		
		<div class="box-footer">
			<button class="btn history-back"><?php echo getMLText('back'); ?></button>
			<button class="btn btn-info" type="submit"><i class="fa fa-save"></i> <?php printMLText("add_event");?></button>
		</div>

	</form>

	<?php $this->endsBoxPrimary(); ?>
	</div>
</div>

<?php
		echo "</div>";

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
