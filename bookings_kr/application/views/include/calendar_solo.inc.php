<?php
/*
 +-------------------------------------------------------------------+
 |                  H T M L - C A L E N D A R   (v2.16)              |
 |                                                                   |
 | Copyright Gerd Tentler               www.gerd-tentler.de/tools    |
 | Created: May 27, 2003                Last modified: Feb. 12, 2012 |
 +-------------------------------------------------------------------+
 | This program may be used and hosted free of charge by anyone for  |
 | personal purpose as long as this copyright notice remains intact. |
 |                                                                   |
 | Obtain permission before selling the code for this program or     |
 | hosting this software on a commercial website or redistributing   |
 | this software over the Internet or in any other medium. In all    |
 | cases copyright must remain intact.                               |
 +-------------------------------------------------------------------+

 EXAMPLE #1:	$myCal = new CALENDAR();
				echo $myCal->create();

 EXAMPLE #2:	$myCal = new CALENDAR(2004, 12);
				echo $myCal->create();

 EXAMPLE #3:	$myCal = new CALENDAR();
				$myCal->year = 2004;
				$myCal->month = 12;
				echo $myCal->create();

 Returns HTML code
==========================================================================================================
*/
	error_reporting(E_WARNING);
	$cal_ID = 0;

	class CALENDAR {
//========================================================================================================
// Configuration
//========================================================================================================
		/*var $tFontFace = 'Arial, Helvetica';	// title: font family (CSS-spec, e.g. "Arial, Helvetica")
		var $tFontSize = 14;					// title: font size (pixels)
		var $tFontColor = '#FFFFFF';			// title: font color
		var $tBGColor = '#304B90';				// title: background color

		var $hFontFace = 'Arial, Helvetica'; 	// heading: font family (CSS-spec, e.g. "Arial, Helvetica")
		var $hFontSize = 12;					// heading: font size (pixels)
		var $hFontColor = '#FFFFFF';			// heading: font color
		var $hBGColor = '#304B90';				// heading: background color

		var $dFontFace = 'Arial, Helvetica';	// days: font family (CSS-spec, e.g. "Arial, Helvetica")
		var $dFontSize = 14;					// days: font size (pixels)
		var $dFontColor = '#000000';			// days: font color
		var $dBGColor = '#FFFFFF';				// days: background color

		var $wFontFace = 'Arial, Helvetica';	// weeks: font family (CSS-spec, e.g. "Arial, Helvetica")
		var $wFontSize = 12;					// weeks: font size (pixels)
		var $wFontColor = '#FFFFFF';			// weeks: font color
		var $wBGColor = '#304B90';				// weeks: background color

		var $saFontColor = '#0000D0';			// Saturdays: font color
		var $saBGColor = '#F6F6FF';				// Saturdays: background color

		var $suFontColor = '#D00000';			// Sundays: font color
		var $suBGColor = '#FFF0F0';				// Sundays: background color

		var $tdBorderColor = '#FF0000';			// today: border color

		var $borderColor = '#304B90';			// border color
		var $hilightColor = '#FFFF00';			// hilight color (works only in combination with link)

		var $link = '';							// page to link to when day is clicked
		var $linkTarget = '';					// link target frame or window, e.g. parent.myFrame
		var $offset = 1;						// week start: 0 - 6 (0 = Saturday, 1 = Sunday, 2 = Monday ...)
		var $weekNumbers = true;				// view week numbers: true = yes, false = no
		*/
		var $bold='bold';

		var $tFontFace = 'Verdana, Geneva, sans-serif';	// title: font family (CSS-spec, e.g. "Arial, Helvetica")

		var $tFontSize = 14;					// title: font size (pixels)

		var $tFontColor = 'Black';			// title: font color

		var $tBGColor = '#5B2D75';				// title: background color



		var $hFontFace = 'Verdana, Geneva, sans-serif'; 	// heading: font family (CSS-spec, e.g. "Arial, Helvetica")

		var $hFontSize = 12;					// heading: font size (pixels)

		var $hFontColor = '#Black';			// heading: font color

		var $hBGColor = '#5B2D75';				// heading: background color



		var $dFontFace = 'Verdana, Geneva, sans-serif';	// days: font family (CSS-spec, e.g. "Arial, Helvetica")

		var $dFontSize = 12;					// days: font size (pixels)

		var $dFontColor = 'Black';			// days: font color

		var $dBGColor = '#46a748';				// days: background color



		var $wFontFace = 'Verdana, Geneva, sans-serif';	// weeks: font family (CSS-spec, e.g. "Arial, Helvetica")

		var $wFontSize = 12;					// weeks: font size (pixels)

		var $wFontColor = 'white';			// weeks: font color

		var $wBGColor = '#5B2D75';				// weeks: background color



		var $saFontColor = 'Black';			// Saturdays: font color

		var $saBGColor = '#5B2D75';				// Saturdays: background color



		var $suFontColor = 'Black';			// Sundays: font color

		var $suBGColor = '#5B2D75';				// Sundays: background color



		var $tdBorderColor = '#46a748';			// today: border color



		var $borderColor = 'white';			// border color

		var $hilightColor = '#46a748';			// hilight color (works only in combination with link)



		var $link = '';							// page to link to when day is clicked

		var $linkTarget = '';					// link target frame or window, e.g. parent.myFrame

		var $offset = 1;						// week start: 0 - 6 (0 = Saturday, 1 = Sunday, 2 = Monday ...)

		var $weekNumbers = false;				// view week numbers: true = yes, false = no

//--------------------------------------------------------------------------------------------------------
// You should change these variables only if you want to translate them into your language:
//--------------------------------------------------------------------------------------------------------
		// weekdays: must start with Saturday because January 1st of year 1 was a Saturday
		var $weekdays = array("Sa", "Su", "Mo", "Tu", "We", "Th", "Fr");

		// months: must start with January
		var $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

		// error messages
		var $error = array("Year must be 1 - 3999!", "Month must be 1 - 12!");

//--------------------------------------------------------------------------------------------------------
// Don't change from here:
//--------------------------------------------------------------------------------------------------------
		var $year, $month, $size;
		var $mDays = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		var $specDays = array();
		var $specDays2 = array();

//========================================================================================================
// Functions
//========================================================================================================
		function CALENDAR($year = '', $month = '', $week = '') {
			if($year == '' && $month == '') {
				$year = date('Y');
				$month = date('n');
			}
			else if($year != '' && $month == '') $month = 1;
			$this->year = (int) $year;
			$this->month = (int) $month;
			$this->week = (int) $week;
			if($this->linkTarget == '') $this->linkTarget = 'document';
		}

		function set_styles() {
			global $cal_ID;

			$cal_ID++;
			$html = '<style> .cssTitle' . $cal_ID . ' { ';
			if($this->tFontFace) $html .= 'font-family: ' . $this->tFontFace . '; ';
			if($this->tFontSize) $html .= 'font-size: ' . $this->tFontSize . 'px; ';
			if($this->tFontColor) $html .= 'color: ' . $this->tFontColor . '; ';
			if($this->tBGColor) $html .= 'background-color: ' . $this->tBGColor . '; ';
			$html .= '} .cssHeading' . $cal_ID . ' { ';
			if($this->hFontFace) $html .= 'font-family: ' . $this->hFontFace . '; ';
			if($this->hFontSize) $html .= 'font-size: ' . $this->hFontSize . 'px; ';
			if($this->hFontColor) $html .= 'color: ' . $this->hFontColor . '; ';
			if($this->hBGColor) $html .= 'background-color: ' . $this->hBGColor . '; ';
			$html .= '} .cssDays' . $cal_ID . ' { ';
			if($this->dFontFace) $html .= 'font-family: ' . $this->dFontFace . '; ';
			if($this->dFontSize) $html .= 'font-size: ' . $this->dFontSize . 'px; ';
			if($this->dFontColor) $html .= 'color: ' . $this->dFontColor . '; ';
			if($this->dBGColor) $html .= 'background-color: ' . $this->dBGColor . '; ';
			$html .= '} .cssWeeks' . $cal_ID . ' { ';
			if($this->wFontFace) $html .= 'font-family: ' . $this->wFontFace . '; ';
			if($this->wFontSize) $html .= 'font-size: ' . $this->wFontSize . 'px; ';
			if($this->wFontColor) $html .= 'color: ' . $this->wFontColor . '; ';
			if($this->wBGColor) $html .= 'background-color: ' . $this->wBGColor . '; ';
			$html .= '} .cssSaturdays' . $cal_ID . ' { ';
			if($this->dFontFace) $html .= 'font-family: ' . $this->dFontFace . '; ';
			if($this->dFontSize) $html .= 'font-size: ' . $this->dFontSize . 'px; ';
			if($this->saFontColor) $html .= 'color: ' . $this->saFontColor . '; ';
			if($this->saBGColor) $html .= 'background-color: ' . $this->saBGColor . '; ';
			$html .= '} .cssSundays' . $cal_ID . ' { ';
			if($this->dFontFace) $html .= 'font-family: ' . $this->dFontFace . '; ';
			if($this->dFontSize) $html .= 'font-size: ' . $this->dFontSize . 'px; ';
			if($this->suFontColor) $html .= 'color: ' . $this->suFontColor . '; ';
			if($this->suBGColor) $html .= 'background-color: ' . $this->suBGColor . '; ';
			$html .= '} .cssHilight' . $cal_ID . ' { ';
			if($this->dFontFace) $html .= 'font-family: ' . $this->dFontFace . '; ';
			if($this->dFontSize) $html .= 'font-size: ' . $this->dFontSize . 'px; ';
			if($this->dFontColor) $html .= 'color: ' . $this->dFontColor . '; ';
			if($this->hilightColor) $html .= 'background-color: ' . $this->hilightColor . '; ';
			$html .= 'cursor: default; ';
			$html .= '} </style>';

			return $html;
		}

		function leap_year($year) {
			return (!($year % 4) && ($year < 1582 || $year % 100 || !($year % 400))) ? true : false;
		}

		function get_weekday($year, $days) {
			$a = $days;
			if($year) $a += ($year - 1) * 365;
			for($i = 1; $i < $year; $i++) if($this->leap_year($i)) $a++;
			if($year > 1582 || ($year == 1582 && $days > 277)) $a -= 10;
			if($a) $a = ($a - $this->offset) % 7;
			else if($this->offset) $a += 7 - $this->offset;

			return $a;
		}

		function get_week($year, $days) {
			$firstWDay = $this->get_weekday($year, 0);
			if($year == 1582 && $days > 277) $days -= 10;

			return floor(($days + $firstWDay) / 7) + ($firstWDay <= 3);
		}

		function table_cell($content, $class, $date = '', $style = '') {
			global $cal_ID;

			$size = round($this->size * 1.5);
			$html = '<td align=center width=' . $size . ' class="' . $class . '"';

			if($content != '&nbsp;' && stristr($class, 'day')) {
				$link = $this->link;
				$events = array();
				$bgColor = '';

				if(is_array($this->specDays[$content])) {
					foreach($this->specDays[$content] as $arr) {
						if($arr[0]) $bgColor = $arr[0];
						if($arr[1]) $events[] = $arr[1];
						if($arr[2]) $link = $arr[2];
					}
					$html .= ' title="' . join(' &middot; ', $events) . '"';
					if($bgColor) $style .= 'background-color:' . $bgColor . ';';
				}
				if($link) {
					$link .= strstr($link, '?') ? "&date=$date" : "?date=$date";
					$html .= ' onMouseOver="this.className=\'cssHilight' . $cal_ID . '\'"';
					$html .= ' onMouseOut="this.className=\'' . $class . '\'"';
					$html .= ' onClick="' . $this->linkTarget . '.location.href=\'' . $link . '\'"';
				}
			}
			if($style) $html .= ' style="' . $style . '"';
			$html .= '>' . $content . '</td>';

			return $html;
		}

		function table_head($content) {
			global $cal_ID;

			$cols = $this->weekNumbers ? 8 : 7;
			$html = '<tr><td colspan=' . $cols . ' class="cssTitle' . $cal_ID . '" align=center><b>' .
					$content . '</b></td></tr><tr>';
			for($i = 0; $i < count($this->weekdays); $i++) {
				$ind = ($i + $this->offset) % 7;
				$wDay = $this->weekdays[$ind];
				$html .= $this->table_cell($wDay, 'cssHeading' . $cal_ID);
			}
			if($this->weekNumbers) $html .= $this->table_cell('&nbsp;', 'cssHeading' . $cal_ID);
			$html .= '</tr>';

			return $html;
		}

		function viewEvent($from, $to, $color, $title, $link = '') {
			if($from > $to) return;
			if($from < 1 || $from > 31) return;
			if($to < 1 || $to > 31) return;

			while($from <= $to) {
				if(!$this->specDays[$from]) $this->specDays[$from] = array();
				$this->specDays[$from][] = array($color, $title, $link);
				$from++;
			}
		}

		function viewEventEach($weekday, $color, $title, $link = '') {
			if($weekday < 0 || $weekday > 6) return;
			if(!$this->specDays2[$weekday]) $this->specDays2[$weekday] = array();
			$this->specDays2[$weekday][] = array($color, $title, $link);
		}

		function create() {
			global $cal_ID;

			$this->size = ($this->hFontSize > $this->dFontSize) ? $this->hFontSize : $this->dFontSize;
			if($this->wFontSize > $this->size) $this->size = $this->wFontSize;

			list($curYear, $curMonth, $curDay) = explode('-', date('Y-m-d'));

			if($this->year < 1 || $this->year > 3999) $html = '<b>' . $this->error[0] . '</b>';
			else if($this->month < 1 || $this->month > 12) $html = '<b>' . $this->error[1] . '</b>';
			else {
				$this->mDays[1] = $this->leap_year($this->year) ? 29 : 28;
				for($i = $days = 0; $i < $this->month - 1; $i++) $days += $this->mDays[$i];

				$start = $this->get_weekday($this->year, $days);
				$stop = $this->mDays[$this->month-1];

				$html = $this->set_styles();
				$html .= '<table border=0 cellspacing=0 cellpadding=0><tr>';
				$html .= '<td' . ($this->borderColor ? ' bgcolor=' . $this->borderColor	: '') . '>';
				$html .= '<table border=0 cellspacing=1 cellpadding=3>';
				$title = htmlentities($this->months[$this->month-1]) . ' ' . $this->year;
				$html .= $this->table_head($title);
				$daycount = 1;

				if(($this->year == $curYear) && ($this->month == $curMonth)) $inThisMonth = true;
				else $inThisMonth = false;

				if($this->weekNumbers || $this->week) $weekNr = $this->get_week($this->year, $days);

				for($i = 0; $i <= $this->mDays[$this->month-1]; $i++) {
					foreach($this->specDays2 as $j => $arr) {
						if($this->get_weekday($this->year, $days + $i) == $j - $this->offset + 1) {
							if(!$this->specDays[$i]) $this->specDays[$i] = array();
							$this->specDays[$i] = array_merge($this->specDays[$i], $arr);
						}
					}
				}

				while($daycount <= $stop) {
					if($this->week && $this->week != $weekNr) {
						$daycount += 7 - ($daycount == 1 ? $start : 0);
						$weekNr++;
						continue;
					}
					$html .= '<tr>';

					for($i = $wdays = 0; $i <= 6; $i++) {
						$ind = ($i + $this->offset) % 7;
						if($ind == 0) $class = 'cssSaturdays';
						else if($ind == 1) $class = 'cssSundays';
						else $class = 'cssDays';

						$style = '';
						$date = sprintf('%4d-%02d-%02d', $this->year, $this->month, $daycount);

						if(($daycount == 1 && $i < $start) || $daycount > $stop) $content = '&nbsp;';
						else {
							$content = $daycount;
							if($inThisMonth && $daycount == $curDay) {
								$style = 'padding:0px;border:3px solid ' . $this->tdBorderColor . ';';
							}
							else if($this->year == 1582 && $this->month == 10 && $daycount == 4) $daycount = 14;
							$daycount++;
							$wdays++;
						}
						$html .= $this->table_cell($content, $class . $cal_ID, $date, $style);
					}

					if($this->weekNumbers) {
						if(!$weekNr) {
							if($this->year == 1) $content = '&nbsp;';
							else if($this->year == 1583) $content = 51;
							else $content = $this->get_week($this->year - 1, 365);
						}
						else if($this->month == 12 && $weekNr >= 52 && $wdays < 4) $content = 1;
						else $content = $weekNr;

						$html .= $this->table_cell($content, 'cssWeeks' . $cal_ID);
						$weekNr++;
					}
					$html .= '</tr>';
				}
				$html .= '</table></td></tr></table>';
			}
			return $html;
		}
	}
?>
