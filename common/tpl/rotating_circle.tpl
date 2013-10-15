<?php
/**
* Generates animations of System core functionality
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

$rotating_circle = <<<Rotating_circle
	<script type="text/javascript">
	$(document).ready(function(){
		$("#home_rotating_arrow").fadeIn(6000);
		$("#logo_ai").animate({
			'-webkit-transform': 'rotate(0deg)',
			'-moz-transform': 'rotate(0deg)',
			'-ms-transform': 'rotate(0deg)',
			'-o-transform': 'rotate(0deg)',
			'transform': 'rotate(0deg)',
			'zoom': 1
		}, 1000);
		$("div[title]").qtip({
			style: {
				classes: "ui-tooltip-dark"
			},
			position: {
				my: "bottom center",
				at: "top center",
				target: "mouse",
				container: false,
				viewport: $(window),
				adjust: {
					method: "flip",
					x: parseInt(0, 10) || 0,
					y: parseInt(0, 10) || -20,
					mouse: true
				},
				effect: true
			}
		});
	});
	</script>
	<div id="home_rotating_arrow">
		<div id="arrows"></div>
		<div id="network_protocol_img" title="Network protocol"></div>
		<div id="search_img" title="Search"></div>
		<div id="recent_changes_img" title="Recent changes"></div>
		<div id="logo_ai"></div>
	</div>
Rotating_circle;
?>