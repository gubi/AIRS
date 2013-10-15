<?php
/**
* Generates main feed page
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$content_body .= <<<EditoRSS_graph
<script src="{ABSOLUTE_PATH}common/js/graphs/Highstock-1.2.5/js/highstock.js"></script>
<script src="{ABSOLUTE_PATH}common/js/graphs/Highstock-1.2.5/js/highcharts-more.js"></script>
<script src="{ABSOLUTE_PATH}common/js/graphs/Highstock-1.2.5/js/modules/exporting.js"></script>
<script language="javascript" type="text/javascript">
var chart;
$(document).ready(function() {
	Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
		return {
			radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
			stops: [
				[0, color],
				[1, Highcharts.Color(color).brighten(-0.3).get('rgb')]
			]
		};
	});
	$.getJSON('{ABSOLUTE_PATH}common/include/funcs/_ajax/EditoRSS/jsonp.php?&callback=?', function(data) {
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'connection_chart',
				plotBorderWidth: null,
				plotShadow: true,
				backgroundColor:'rgba(255, 255, 255, 0.1)',
				margin: [0, 0, 0, 0],
				spacingTop: 0,
				spacingBottom: 0,
				spacingLeft: 0,
				spacingRight: 0
			},
			title: {
				text: 'News acquisite'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.y}</b>',
				percentageDecimals: 1
			},
			legend: {
				layout: 'vertical',
				align: 'left',
				verticalAlign: 'top',
			},
			scrollbar: {
				enabled: true
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						color: '#000000',
						connectorColor: '#000000',
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.y;
						}
					},
					showInLegend: true
				}
			},
			series: [{
				type: 'pie',
				name: 'News acquisite',
				data: data
			}]
		});
	});
});
</script>
<div id="connection_chart"></div>
EditoRSS_graph;
require_once("common/include/conf/replacing_object_data.php");
?>