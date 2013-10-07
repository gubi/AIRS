<?php
/**
* Converts and translate date format
* 
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
function converti_data($data, $lang = "it", $order = "day_first", $type = "short"){
	$dayNamesMin = array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
	$dayNamesShort = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
	$dayNames = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	$monthNamesShort = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
	$monthNames = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	
	$dayNamesMin_it = array("Do", "Lu", "Ma", "Me", "Gio", "Ve", "Sa");
	$dayNamesShort_it = array("Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab");
	$dayNames_it = array("Domenica", "Luned&igrave;", "Marted&igrave;", "Mercoled&igrave;", "Gioved&igrave;", "Venerd&igrave;", "Sabato");
	$monthNamesShort_it = array("Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic");
	$monthNames_it = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
	
	if ($lang !== "it"){
		switch($order){
			case "day_first":
				switch($type){
					case "short":
						$data = str_replace($dayNamesShort_it, $dayNamesShort, $data);
						$data = str_replace($monthNamesShort_it, $monthNamesShort, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin_it, $dayNamesMin, $data);
						break;
					default:
						$data = str_replace($dayNames_it, $dayNames, $data);
						$data = str_replace($monthNames_it, $monthNames, $data);
				}
				break;
			case "month_first":
				switch($type){
					case "short":
						$data = str_replace($monthNamesShort_it, $monthNamesShort, $data);
						$data = str_replace($dayNamesShort_it, $dayNamesShort, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin_it, $dayNamesMin, $data);
						break;
					default:
						$data = str_replace($monthNames_it, $monthNames, $data);
						$data = str_replace($dayNames_it, $dayNames, $data);
				}
				break;
			case "day":
				switch($type){
					case "short":
						$data = str_replace($dayNamesShort_it, $dayNamesShort, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin_it, $dayNamesMin, $data);
						break;
					default:
						$data = str_replace($dayNames_it, $dayNames, $data);
				}
				break;
			case "month":
				switch($type){
					case "short":
						$data = str_replace($monthNamesShort_it, $monthNamesShort, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin_it, $dayNamesMin, $data);
						break;
					default:
						$data = str_replace($monthNames_it, $monthNames, $data);
				}
				break;
		}
	} else {
		switch($order){
			case "day_first":
				switch($type){
					case "short":
						$data = str_replace($dayNamesShort_it, $dayNamesShort, $data);
						$data = str_replace($monthNamesShort_it, $monthNamesShort, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin_it, $dayNamesMin, $data);
						break;
					default:
						$data = str_replace($dayNames_it, $dayNames, $data);
						$data = str_replace($monthNames_it, $monthNames, $data);
				}
				break;
			case "month_first":
				switch($type){
					case "short":
						$data = str_replace($monthNamesShort, $monthNamesShort_it, $data);
						$data = str_replace($dayNamesShort, $dayNamesShort_it, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin, $dayNamesMin_it, $data);
						break;
					default:
						$data = str_replace($monthNames, $monthNames_it, $data);
						$data = str_replace($dayNames, $dayNames_it, $data);
				}
				break;
			case "day":
				switch($type){
					case "short":
						$data = str_replace($dayNamesShort_it, $dayNamesShort, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin_it, $dayNamesMin, $data);
						break;
					default:
						$data = str_replace($dayNames_it, $dayNames, $data);
				}
				break;
			case "month":
				switch($type){
					case "short":
						$data = str_replace($monthNamesShort, $monthNamesShort_it, $data);
						break;
					case "min":
						$data = str_replace($dayNamesMin, $dayNamesMin_it, $data);
						break;
					default:
						$data = str_replace($monthNames, $monthNames_it, $data);
				}
				break;
		}
	}
	return $data;
}
?>