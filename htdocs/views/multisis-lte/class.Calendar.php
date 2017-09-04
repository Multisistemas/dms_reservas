<?php
/**
 * Implementation of Calendar view
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
 * Class which outputs the html page for Calendar view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Calendar extends SeedDMS_Bootstrap_Style {

	function generateCalendarArrays() { /* {{{ */
		$this->monthNames = array( getMLText("january"),
												 getMLText("february"),
												 getMLText("march"),
												 getMLText("april"),
												 getMLText("may"), 
												 getMLText("june"),
												 getMLText("july"), 
												 getMLText("august"), 
												 getMLText("september"), 
												 getMLText("october"), 
												 getMLText("november"), 
												 getMLText("december") );
												
		$this->dayNamesLong = array( getMLText("sunday"),
													 getMLText("monday"),
													 getMLText("tuesday"),
													 getMLText("wednesday"), 
													 getMLText("thursday"),
													 getMLText("friday"), 
													 getMLText("saturday") );
		/* Set abbreviated weekday names. If no translation is availabe, use
		 * the first three chars from the long name
		 */
		$this->dayNames = array( getMLText("sunday_abbr", array(), substr($this->dayNamesLong[0], 0, 3)),
													 getMLText("monday_abbr", array(), substr($this->dayNamesLong[1], 0, 3)),
													 getMLText("tuesday_abbr", array(), substr($this->dayNamesLong[2], 0, 3)),
													 getMLText("wednesday_abbr", array(), substr($this->dayNamesLong[3], 0, 3)), 
													 getMLText("thursday_abbr", array(), substr($this->dayNamesLong[4], 0, 3)),
													 getMLText("friday_abbr", array(), substr($this->dayNamesLong[5], 0, 3)), 
													 getMLText("saturday_abbr", array(), substr($this->dayNamesLong[6], 0, 3)) );
	
	} /* }}} */

	// Calculate the number of days in a month, taking into account leap years.
	function getDaysInMonth($month, $year) { /* {{{ */
		if ($month < 1 || $month > 12) return 0;

		$daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$d = $daysInMonth[$month - 1];

		if ($month == 2){
		
			if ($year%4 == 0){
			
				if ($year%100 == 0){
				
					if ($year%400 == 0) $d = 29;
				}
				else $d = 29;
			}
		}
		return $d;
	} /* }}} */

	// Adjust dates to allow months > 12 and < 0 and day<0 or day>days of the month
	function adjustDate(&$day,&$month,&$year) { /* {{{ */
		$d=getDate(mktime(12,0,0, $month, $day, $year));
		$month=$d["mon"];
		$day=$d["mday"];
		$year=$d["year"];
	} /* }}} */

	// Generate the HTML for a given month
	function getMonthHTML($month, $year) { /* {{{ */
		if (!isset($this->monthNames)) $this->generateCalendarArrays();
		if (!isset($this->dayNames)) $this->generateCalendarArrays();

		$startDay = $this->firstdayofweek;

		$day=1;
		$this->adjustDate($day,$month,$year);

		$daysInMonth = $this->getDaysInMonth($month, $year);

		$date = getdate(mktime(12, 0, 0, $month, 1, $year));

		$first = $date["wday"];
		$monthName = $this->monthNames[$month - 1];

		$s = "<table class=\"table table-bordered table-condensed background-white \">\n";
		
		$s .= "<tr>\n";
		$s .= "<td style=\"border-top: 0px;\" class=\"align-center th-info-background\" colspan=\"7\"><a class=\"color-month\" href=\"../out/out.Calendar.php?mode=m&year=".$year."&month=".$month."\">".$monthName."</a></td>\n"; ;
		$s .= "</tr>\n";

		$s .= "<tr>\n";
		$s .= "<th class=\"header align-center\">" . $this->dayNames[($startDay)%7] . "</th>\n";
		$s .= "<th class=\"header align-center\">" . $this->dayNames[($startDay+1)%7] . "</th>\n";
		$s .= "<th class=\"header align-center\">" . $this->dayNames[($startDay+2)%7] . "</th>\n";
		$s .= "<th class=\"header align-center\">" . $this->dayNames[($startDay+3)%7] . "</th>\n";
		$s .= "<th class=\"header align-center\">" . $this->dayNames[($startDay+4)%7] . "</th>\n";
		$s .= "<th class=\"header align-center\">" . $this->dayNames[($startDay+5)%7] . "</th>\n";
		$s .= "<th class=\"header align-center\">" . $this->dayNames[($startDay+6)%7] . "</th>\n";
		$s .= "</tr>\n";

		// Get the events for the month
		$events = getEventsInInterval(mktime(0,0,0, $month, 1, $year), mktime(23,59,59, $month, $daysInMonth, $year));
		$eventClass = "";
		$eventTitle = "";

		// We need to work out what date to start at so that the first appears in the correct column
		$d = $startDay + 1 - $first;
		while ($d > 1) $d -= 7;

		// Make sure we know when today is, so that we can use a different CSS style
		$today = getdate(time());

		while ($d <= $daysInMonth)
		{
			$s .= "<tr>\n";

			for ($i = 0; $i < 7; $i++){

				$class = ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]) ? "today" : "";

				//Scan events for highlight
				$xdate=mktime(0, 0, 0, $month, $d, $year); // Current date in the cicle

				foreach ($events as $event){
					if ($d > 0 && $d <= $daysInMonth){
						if (((int)$xdate >= (int)$event["start"]) && ((int)$xdate <= (int)$event["stop"])){
							$eventClass = "is-event";
							$start = date('d/m/Y', $event["start"]);
							$stop = date('d/m/Y', $event["stop"]);
							$eventTitle = getMLText("event").": ".$event["name"].". ";
							$eventTitle .= getMLText("event_start_date").": ".$start.". ";
							$eventTitle .= getMLText("event_stop_date").": ".$stop.".";
						} else {
							$eventClass = "";
						}
					}
				}
				
				if ($eventTitle != "") {
					$s .= "<td class=\"".$class." ".$eventClass." align-center\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"".$eventTitle."\">";
				} else {
					$s .= "<td class=\"".$class." ".$eventClass." align-center\">";	
				}   
						
				if ($d > 0 && $d <= $daysInMonth){
					$s .= "<a href=\"../out/out.Calendar.php?mode=w&year=".$year."&month=".$month."&day=".$d."\">".$d."</a>";
				}
				else $s .= "&nbsp;";
				
				$s .= "</td>\n";       
				$d++;
				$eventTitle = ""; 
			}
			$s .= "</tr>\n";    
		}

		$s .= "</table>\n";

		return $s;  	
	} /* }}} */

	function printYearTable($year) { /* {{{ */

		print "<div class=\"row\">\n";
	
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(1 , $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(2 , $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(3 , $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(4 , $year) ."</div>\n";

		print "</div>\n";

		print "<div class=\"row\">\n";

		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(5 , $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(6 , $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(7 , $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(8 , $year) ."</div>\n";

		print "</div>\n";

		print "<div class=\"row\">\n";

		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(9 , $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(10, $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(11, $year) ."</div>\n";
		print "<div class=\"col-md-3\" valign=\"top\">" . $this->getMonthHTML(12, $year) ."</div>\n";

		print "</div>\n";

	} /* }}} */

	function js() { /* {{{ */
		header('Content-Type: application/javascript');
		?>
		
		<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$mode = $this->params['mode'];
		$year = $this->params['year'];
		$month = $this->params['month'];
		$day = $this->params['day'];
		$this->firstdayofweek = $this->params['firstdayofweek'];

		$this->adjustDate($day,$month,$year);

		$this->htmlStartPage(getMLText("calendar"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar(0,0,1);
		$this->contentStart();

    ?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">

    <?php 
    $pagination = "";

    if ($mode=="y") { 

			$pagination .= "<div class=\"pull-right\">";
			$pagination .= "<div class=\"btn-group\">";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"/out/out.Calendar.php?mode=y&year=".($year-1)."\"><i class=\"fa fa-chevron-left\"></i></a>";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"/out/out.Calendar.php?mode=y\"><i class=\"fa fa-calendar\"></i></a>";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"/out/out.Calendar.php?mode=y&year=".($year+1)."\"><i class=\"fa fa-chevron-right\"></i></a>";
			$pagination .= "</div>";
			$pagination .= "</div>";

    	$this->startCalendarBox(getMLText("year_view").": ".$year, $pagination);	

    } else if ($mode=="m") {

    	if (!isset($this->monthNames)) $this->generateCalendarArrays();

    	$pagination .= "<div class=\"pull-right\">";
			$pagination .= "<div class=\"btn-group\">";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"../out/out.Calendar.php?mode=m&year=".($year)."&month=".($month-1)."\"><i class=\"fa fa-chevron-left\"></i></a>";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"../out/out.Calendar.php?mode=m\"><i class=\"fa fa-calendar\"></i></li>";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"../out/out.Calendar.php?mode=m&year=".($year)."&month=".($month+1)."\"><i class=\"fa fa-chevron-right\"></i></a>";
			$pagination .= "</div>";
			$pagination .= "</div>";

    	$this->startCalendarBox(getMLText("month_view").": ".$this->monthNames[$month-1]. " ".$year, $pagination);

    } else {

    	$pagination .= "<div class=\"pull-right\">";
			$pagination .= "<div class=\"btn-group\">";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"../out/out.Calendar.php?mode=w&year=".($year)."&month=".($month)."&day=".($day-7)."\"><i class=\"fa fa-chevron-left\"></i></a>";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"../out/out.Calendar.php?mode=w\"><i class=\"fa fa-calendar\"></i></a>";
			$pagination .= "<a type=\"button\" class=\"btn btn-primary btn-sm btn-flat\" href=\"../out/out.Calendar.php?mode=w&year=".($year)."&month=".($month)."&day=".($day+7)."\"><i class=\"fa fa-chevron-right\"></i></a>";
			$pagination .= "</div>";
			$pagination .= "</div>";

    	$this->startCalendarBox(getMLText("week_view").": ".getReadableDate(mktime(12, 0, 0, $month, $day, $year)), $pagination);
    }


    
    /* ------------------------------- Generate the views ------------------------------------- */
		if ($mode=="y"){ // Year view

			$this->printYearTable($year);

		}else if ($mode=="m"){ // Month view

			if (!isset($this->dayNamesLong)) $this->generateCalendarArrays();
			if (!isset($this->monthNames)) $this->generateCalendarArrays();
					
			$days=$this->getDaysInMonth($month, $year);
			$today = getdate(time());
			
			$events = getEventsInInterval(mktime(0,0,0, $month, 1, $year), mktime(23,59,59, $month, $days, $year));
			$collapsed = "";

			$this->startBoxCollapsablePrimary("<a href=\"../out/out.Calendar.php?mode=w&year=".($year)."&month=".($month)."&day=1\">".date('W', mktime(12, 0, 0, $month, 1, $year)).". ".getMLText('calendar_week')."</a>", "collapsed-box");

			$fd = getdate(mktime(12, 0, 0, $month, 1, $year));
			for($i=0; $i<$fd['wday']-1; $i++)
				echo "<tr><td colspan=\"2\"></td></tr>";
			
			for ($i=1; $i<=$days; $i++){

				// highlight today
				$class = ($year == $today["year"] && $month == $today["mon"] && $i == $today["mday"]) ? "todayHeader" : "header";
				if ($class=="todayHeader") {
					$class="today-week"; 
				} else {
					$class="today-week-default";
				}

				// separate weeks
				$date = getdate(mktime(12, 0, 0, $month, $i, $year));
				if (($date["wday"]==$this->firstdayofweek) && ($i!=1)) {
					$this->endsBoxCollapsablePrimary();

					$this->startBoxCollapsablePrimary("<a href=\"../out/out.Calendar.php?mode=w&year=".($year)."&month=".($month)."&day=".($i)."\">".date('W', mktime(12, 0, 0, $month, $i, $year)).". ".getMLText('calendar_week')."</a>", "collapsed-box");
				}
				
				echo "<div class=\"week-day-border\">";
				echo "<h5 class=\"".$class." h5-no-margin\">".$i.". - ".$this->dayNamesLong[$date["wday"]]."</h5>";
				
				$xdate=mktime(0, 0, 0, $month, $i, $year);
				foreach ($events as $event){
					
					if (($event["start"]<=$xdate)&&($event["stop"]>=$xdate)){
						print "<table class=\"table table-bordered table-striped table-no-margin\">";
						print "<tr>";
						print "<td width='20%'>";
						print "<i class=\"fa fa-calendar\"></i> ".getMLText("event").": ";
						print "</td>";
						print "<td width='80%'>";
						if (strlen($event['name']) > 25) $event['name'] = substr($event['name'], 0, 50) . "...";
						print "<a href=\"../out/out.ViewEvent.php?id=".$event['id']."\">".htmlspecialchars($event['name'])."</a>";
						print "</td>";
						print "</tr>";
						print "</table>";
					}
					
				}
				echo "</div>";
				
			}
			$this->endsBoxCollapsablePrimary();

			
		}else {  // Week view

			if (!isset($this->dayNamesLong)) $this->generateCalendarArrays();
			if (!isset($this->monthNames)) $this->generateCalendarArrays();
			
			// get the week interval - TODO: $GET
			$datestart=getdate(mktime(0,0,0,$month,$day,$year));
			while($datestart["wday"]!=$this->firstdayofweek){
				$datestart=getdate(mktime(0,0,0,$datestart["mon"],$datestart["mday"]-1,$datestart["year"]));
			}
				
			$datestop=getdate(mktime(23,59,59,$month,$day,$year));
			if ($datestop["wday"]==$this->firstdayofweek){
				$datestop=getdate(mktime(23,59,59,$datestop["mon"],$datestop["mday"]+1,$datestop["year"]));
			}
			while($datestop["wday"]!=$this->firstdayofweek){
				$datestop=getdate(mktime(23,59,59,$datestop["mon"],$datestop["mday"]+1,$datestop["year"]));
			}
			$datestop=getdate(mktime(23,59,59,$datestop["mon"],$datestop["mday"]-1,$datestop["year"]));
			
			$starttime=mktime(0,0,0,$datestart["mon"],$datestart["mday"],$datestart["year"]);
			$stoptime=mktime(23,59,59,$datestop["mon"],$datestop["mday"],$datestop["year"]);
			
			$today = getdate(time());
			$events = getEventsInInterval($starttime,$stoptime);
			
			for ($i=$starttime; $i<$stoptime; $i += 86400){
				
				$date = getdate($i);
				
				// for daylight saving time TODO: could be better
				if ( ($i!=$starttime) && ($prev_day==$date["mday"]) ){
					$i += 3600;
					$date = getdate($i);
				}
				
				// highlight today
				$class = ($date["year"] == $today["year"] && $date["mon"] == $today["mon"] && $date["mday"]  == $today["mday"]) ? "info" : "";
				
				echo "<div class=\"col-md-12\">";
				$this->startBoxPrimary($this->dayNamesLong[$date["wday"]]." - ".getReadableDate($i));

				echo "<table class='table table-bordered'>\n";
				echo "<tr>";
				echo "<th width=\"70%\" class=\"align-center th-info-background\">".getMLText("event")."</th>";
				echo "<th width=\"30%\" class=\"align-center th-info-background\">".getMLText("actions")."</th>";
				echo "</tr>";
				foreach ($events as $event){
					
					if (($event["start"]<=$i)&&($event["stop"]>=$i)){
						
						echo "<tr>";
						print "<td><span><a href=\"../out/out.ViewEvent.php?id=".$event['id']."\">".htmlspecialchars($event['name'])."</a></span>";
						if($event['comment'])
							echo "<br /><em>".htmlspecialchars($event['comment'])."</em>";
						print "</td>";
						echo "<td class=\"align-center\"><a type=\"button\" class=\"btn btn-danger\" href=\"../out/out.RemoveEvent.php?id=".$event['id']."\"><i class=\"fa fa-times\"></i></a> ";
						echo "<a type=\"button\" class=\"btn btn-success\" href=\"../out/out.EditEvent.php?id=".$event['id']."\"><i class=\"fa fa-pencil\"></i></a></td>";
						echo "</tr>\n";
					}
				}

				echo "</table>\n";
				
				$prev_day=$date["mday"];
				$this->endsBoxPrimary();
				echo "</div>\n";
			}
			

		}

    $this->endsCalendarBox();

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
