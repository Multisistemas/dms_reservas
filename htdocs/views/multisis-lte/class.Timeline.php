<?php
/**
 * Implementation of Timeline view
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
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");

/**
 * Class which outputs the html page for Timeline view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Timeline extends SeedDMS_Bootstrap_Style {

	function iteminfo() { /* {{{ */
		$dms = $this->params['dms'];
		$document = $this->params['document'];
		$version = $this->params['version'];
		$cachedir = $this->params['cachedir'];
		$previewwidthlist = $this->params['previewWidthList'];
		$previewwidthdetail = $this->params['previewWidthDetail'];
		$timeout = $this->params['timeout'];

		if($document && $version) {
		//	$this->contentHeading(getMLText("timeline_selected_item"));
				print "<table id=\"viewfolder-table\" class=\"table table-bordered table-condensed\">";
				print "<thead>\n<tr>\n";
				print "<th></th>\n";	
				print "<th>".getMLText("name")."</th>\n";
				print "<th>".getMLText("status")."</th>\n";
				print "<th>".getMLText("action")."</th>\n";
				print "</tr>\n</thead>\n<tbody>\n";
				$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidthdetail, $timeout);
				echo $this->documentListRow($document, $previewer);

				echo "</tbody>\n</table>\n";
		}
	} /* }}} */

	function data() { /* {{{ */
		$dms = $this->params['dms'];
		$skip = $this->params['skip'];
		$fromdate = $this->params['fromdate'];
		$todate = $this->params['todate'];

		if($fromdate) {
			$from = makeTsFromLongDate($fromdate.' 00:00:00');
		} else {
			$from = time()-7*86400;
		}

		if($todate) {
			$to = makeTsFromLongDate($todate.' 23:59:59');
		} else {
			$to = time()-7*86400;
		}

		if($data = $dms->getTimeline($from, $to)) {
			foreach($data as $i=>$item) {
				switch($item['type']) {
				case 'add_version':
					$msg = getMLText('timeline_full_'.$item['type'], array('document'=>htmlspecialchars($item['document']->getName()), 'version'=> $item['version']));
					break;
				case 'add_file':
					$msg = getMLText('timeline_full_'.$item['type'], array('document'=>htmlspecialchars($item['document']->getName())));
					break;
				case 'status_change':
					$msg = getMLText('timeline_full_'.$item['type'], array('document'=>htmlspecialchars($item['document']->getName()), 'version'=> $item['version'], 'status'=> getOverallStatusText($item['status'])));
					break;
				default:
					$msg = '???';
				}
				$data[$i]['msg'] = $msg;
			}
		}

		$jsondata = array();
		foreach($data as $item) {
			if($item['type'] == 'status_change')
				$classname = $item['type']."_".$item['status'];
			else
				$classname = $item['type'];
			if(!$skip || !in_array($classname, $skip)) {
				$d = makeTsFromLongDate($item['date']);
				$jsondata[] = array(
					'start'=>date('c', $d),
					'content'=>$item['msg'],
					'className'=>$classname,
					'docid'=>$item['document']->getID(),
					'version'=>isset($item['version']) ? $item['version'] : '',
					'statusid'=>isset($item['statusid']) ? $item['statusid'] : '',
					'statuslogid'=>isset($item['statuslogid']) ? $item['statuslogid'] : '',
					'fileid'=>isset($item['fileid']) ? $item['fileid'] : ''
				);
			}
		}
		header('Content-Type: application/json');
		echo json_encode($jsondata);
	} /* }}} */

	function js() { /* {{{ */
		$fromdate = $this->params['fromdate'];
		$todate = $this->params['todate'];
		$skip = $this->params['skip'];

		if($fromdate) {
			$from = makeTsFromLongDate($fromdate.' 00:00:00');
		} else {
			$from = time()-7*86400;
		}

		if($todate) {
			$to = makeTsFromLongDate($todate.' 23:59:59');
		} else {
			$to = time();
		}

		header('Content-Type: application/javascript');
?>
function folderSelected(id, name) {
	window.location = '../out/out.ViewFolder.php?folderid=' + id;
}

$(document).ready(function () {
	$('#update').click(function(ev){
		ev.preventDefault();
		$.getJSON(
			'out.Timeline.php?action=data&' + $('#form1').serialize(), 
			function(data) {
				$.each( data, function( key, val ) {
					val.start = new Date(val.start);
				});
				timeline.setData(data);
				timeline.redraw();
//				timeline.setVisibleChartRange(0,0);
			}
		);
	});
});
<?php
		$this->printDeleteDocumentButtonJs();
		$timelineurl = 'out.Timeline.php?action=data&fromdate='.date('Y-m-d', $from).'&todate='.date('Y-m-d', $to).'&skip='.urldecode(http_build_query(array('skip'=>$skip)));
		$this->printTimelineJs($timelineurl, 550, ''/*date('Y-m-d', $from)*/, ''/*date('Y-m-d', $to+1)*/, $skip);
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$fromdate = $this->params['fromdate'];
		$todate = $this->params['todate'];
		$skip = $this->params['skip'];

		//$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/bootbox/bootbox.min.js"></script>'."\n", 'js');

		if($fromdate) {
			$from = makeTsFromLongDate($fromdate.' 00:00:00');
		} else {
			$from = time()-7*86400;
		}

		if($todate) {
			$to = makeTsFromLongDate($todate.' 23:59:59');
		} else {
			$to = time();
		}

		$this->htmlAddHeader('<link href="../styles/'.$this->theme.'/plugins/timeline/timeline.css" rel="stylesheet">'."\n", 'css');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/timeline/timeline-min.js"></script>'."\n", 'js');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/timeline/timeline-locales.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("admin_tools"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
<div class="gap-10"></div>
<div class="row">
<div class="col-md-12">
<?php $this->startBoxSolidPrimary(getMLText("timeline")); ?>
<div class="row">
<form action="../out/out.Timeline.php" class="form form-inline" name="form1" id="form1">
	<div class="col-md-6">
		<div class="control-group">
			<label class="control-label" for="startdate"><?php printMLText('date') ?></label>
			<div>
			<label><?php printMLText("from"); ?>: </label>
	       <span class="input-append date" style="" id="fromdate" data-date="<?php echo date('Y-m-d', $from); ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
				 <input type="text" class="form-control" name="fromdate" value="<?php echo date('Y-m-d', $from); ?>"/>
					<span class="add-on"><i class="icon-calendar"></i></span>
				</span>
			<label><?php printMLText("to"); ?>: </label>
	      <span class="input-append date" style="" id="todate" data-date="<?php echo date('Y-m-d', $to); ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
				<input type="text" class="form-control" name="todate" value="<?php echo date('Y-m-d', $to); ?>"/>
					<span class="add-on"><i class="icon-calendar"></i></span>
				</span>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="control-group">
			<label class="control-label" for="skip"><?php printMLText('exclude_items') ?></label>
			<div class="controls">
				<input type="checkbox" name="skip[]" value="add_file" <?php echo ($skip &&  in_array('add_file', $skip)) ? 'checked' : '' ?>> <?php printMLText('timeline_skip_add_file') ?><br />
				<input type="checkbox" name="skip[]" value="status_change_0" <?php echo ($skip && in_array('status_change_0', $skip)) ? 'checked' : '' ?>> <?php printMLText('timeline_skip_status_change_0') ?><br />
				<input type="checkbox" name="skip[]" value="status_change_1" <?php echo ($skip && in_array('status_change_1', $skip)) ? 'checked' : '' ?>> <?php printMLText('timeline_skip_status_change_1') ?><br />
				<input type="checkbox" name="skip[]" value="status_change_2" <?php echo ($skip && in_array('status_change_2', $skip)) ? 'checked' : '' ?>> <?php printMLText('timeline_skip_status_change_2') ?><br />
				<input type="checkbox" name="skip[]" value="status_change_3" <?php echo ($skip && in_array('status_change_3', $skip)) ? 'checked' : '' ?>> <?php printMLText('timeline_skip_status_change_3') ?><br />
				<input type="checkbox" name="skip[]" value="status_change_-1" <?php echo ($skip && in_array('status_change_-1', $skip)) ? 'checked' : '' ?>> <?php printMLText('timeline_skip_status_change_-1') ?><br />
				<input type="checkbox" name="skip[]" value="status_change_-3" <?php echo ($skip && in_array('status_change_-3', $skip)) ? 'checked' : '' ?>> <?php printMLText('timeline_skip_status_change_-3') ?><br />
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="control-group">
		<label class="control-label" for="enddate"></label>
		<div class="controls">
			<button id="update" type="_submit" class="btn btn-success"><i class="fa fa-refresh"></i> <?php printMLText("update"); ?></button>
		</div>
	</div>
	</div>

</div>
</form>
<div class="gap-20"></div>
<div class="row">
	<div class="col-md-12">
		<div class="ajax" data-view="Timeline" data-action="iteminfo"></div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?php $this->contentHeading(getMLText("timeline")); ?>
		<?php $this->printTimelineHtml(400); ?>
	</div>
</div>
<?php

		$this->endsBoxSolidPrimary();
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
