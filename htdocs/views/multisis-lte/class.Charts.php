<?php
/**
 * Implementation of Charts view
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
 * Class which outputs the html page for Charts view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Charts extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$data = $this->params['data'];
		$type = $this->params['type'];

		header('Content-Type: application/javascript');

?>
	$("<div id='tooltip'></div>").css({
		position: "absolute",
		display: "none",
		padding: "5px",
		color: "white",
		"background-color": "#000",
		"border-radius": "5px",
		opacity: 0.80
	}).appendTo("body");

<?php
if(in_array($type, array('docspermonth'))) {
?>
	var data = [
<?php
	if($data) {
		foreach($data as $i=>$rec) {
			$key = mktime(12, 0, 0, substr($rec['key'], 5, 2), 1, substr($rec['key'], 0, 4)) * 1000;
			echo '["'.$rec['key'].'",'.$rec['total'].'],'."\n";
		}
	}
?>
	];
	$.plot("#chart", [data], {
		xaxis: {
			mode: "categories",
			tickLength: 0,
		},
		series: {
			bars: {
				show: true,
				align: "center",
				barWidth: 0.8,
			},
		},
		grid: {
			hoverable: true,
			clickable: true
		}
	});

	$("#chart").bind("plothover", function (event, pos, item) {
		if(item) {
			var x = item.datapoint[0];//.toFixed(2),
					y = item.datapoint[1];//.toFixed(2);
			$("#tooltip").html(item.series.xaxis.ticks[x].label + ": " + y)
				.css({top: pos.pageY-35, left: pos.pageX+5})
				.fadeIn(200);
		} else {
			$("#tooltip").hide();
		}
	});
<?php
} elseif(in_array($type, array('docsaccumulated'))) {
?>
	var data = [
<?php
	if($data) {
		foreach($data as $rec) {
			echo '['.htmlspecialchars($rec['key']).','.$rec['total'].'],'."\n";
		}
	}
?>
	];
	var plot = $.plot("#chart", [data], {
		xaxis: { mode: "time" },
		series: {
			lines: {
				show: true
			},
			points: {
				show: true
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		}
	});

	$("#chart").bind("plothover", function (event, pos, item) {
		if(item) {
			var x = item.datapoint[0];//.toFixed(2),
					y = item.datapoint[1];//.toFixed(2);
			$("#tooltip").html($.plot.formatDate(new Date(x), '%e. %b %Y') + ": " + y)
				.css({top: pos.pageY-35, left: pos.pageX+5})
				.fadeIn(200);
		} else {
			$("#tooltip").hide();
		}
	});
<?php
} else {
?>
	var data = [
<?php
	if($data) {
		foreach($data as $rec) {
			echo '{ label: "'.htmlspecialchars($rec['key']).'", data: [[1,'.$rec['total'].']]},'."\n";
		}
	}
?>
	];
$(document).ready( function() {
	$.plot('#chart', data, {
		series: {
			pie: { 
				show: true,
				radius: 1,
				label: {
					show: false,
					radius: 1,
					formatter: labelFormatter,
					threshold: 0.1,
					background: {
						opacity: 0.8
					}
				}
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		},
		legend: {
			show: true,
			container: '#legend'
		}
	});

	$("#chart").bind("plothover", function (event, pos, item) {
		if(item) {
			var x = item.series.data[0][0];//.toFixed(2),
					y = item.series.data[0][1];//.toFixed(2);

			$("#tooltip").html(item.series.label + ": " + y + " (" + Math.round(item.series.percent) + "%)")
				.css({top: pos.pageY-35, left: pos.pageX+5})
				.fadeIn(200);
		} else {
			$("#tooltip").hide();
		}
	});
	function labelFormatter(label, series) {
		return "<div style='font-size:8pt; line-height: 14px; text-align:center; padding:2px; color:black; background: white; border-radius: 5px;'>" + label + "<br/>" + series.data[0][1] + " (" + Math.round(series.percent) + "%)</div>";
	}
});
<?php
}
	} /* }}} */

	function show() { /* {{{ */
		$this->dms = $this->params['dms'];
		$user = $this->params['user'];
		$rootfolder = $this->params['rootfolder'];
		$data = $this->params['data'];
		$type = $this->params['type'];

		$this->htmlAddHeader(
			'<script type="text/javascript" src="../styles/bootstrap/flot/jquery.flot.min.js"></script>'."\n".
			'<script type="text/javascript" src="../styles/bootstrap/flot/jquery.flot.pie.min.js"></script>'."\n".
			'<script type="text/javascript" src="../styles/bootstrap/flot/jquery.flot.categories.min.js"></script>'."\n".
			'<script type="text/javascript" src="../styles/bootstrap/flot/jquery.flot.time.min.js"></script>'."\n");

		$this->htmlStartPage(getMLText("admin_tools"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    <?php 

		$this->startBoxPrimary(getMLText("folders_and_documents_statistic"));

		echo "<div class=\"col-md-3\">\n";
		$this->startBoxSolidPrimary(getMLText("chart_selection"));
		
		foreach(array('docsperuser', 'sizeperuser', 'docspermimetype', 'docspercategory', 'docsperstatus', 'docspermonth', 'docsaccumulated') as $atype) {
			echo "<div><a type=\"button\" class=\"btn btn-default btn-block btn-sm\" href=\"?type=".$atype."\">".getMLText('chart_'.$atype.'_title')."</a></div>\n";
		}
		$this->endsBoxSolidPrimary();
		echo "</div>\n";

		if(in_array($type, array('docspermonth', 'docsaccumulated'))) {
			echo "<div class=\"col-md-9\">\n";
		} else {
			echo "<div class=\"col-md-6\">\n";
		}
		//$this->contentHeading(getMLText('chart_'.$type.'_title'));
		$this->startBoxSolidSuccess(getMLText('chart_'.$type.'_title'));
		//echo "<div class=\"well\">\n";
?>
<div id="chart" style="height: 400px;" class="chart"></div>
<?php
		//echo "</div>\n";
		$this->endsBoxSolidSuccess();
		echo "</div>\n";
		
		if(!in_array($type, array('docspermonth', 'docsaccumulated'))) {
			echo "<div class=\"col-md-3\">\n";
			//$this->contentHeading(getMLText('legend'));
			$this->startBoxSolidPrimary(getMLText('legend'));
			echo "<div class=\"\" id=\"legend\">\n";
			echo "</div>\n";
			$this->endsBoxSolidPrimary();
			//echo "</div>\n";
			echo "</div>\n";
		}

		$this->endsBoxPrimary();

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
