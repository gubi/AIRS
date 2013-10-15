<?php
/**
* Generates template for list of video-guides
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
$content_body = <<<Videolist
<script src="{ABSOLUTE_PATH}common/js/jquery-readmore/readmore/jquery.readmore.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	loader("Caricamento dei video disponibili", "show");
	var url = "http://vimeo.com/api/v2/channel/383235/videos.json?callback=?";
	$.post(url, function(data){
		loader("", "hide");
		var colonne = 3;
		$.each(data, function(i, item) {
			if(i > 0){
				var delay_time = i*100;
			} else {
				var delay_time = 0;
			}
			if(data[i].stats_number_of_plays == 1){
				var volte_txt = "volta";
			} else {
				var volte_txt = "volte";
			}
			var tags = data[i].tags;
			
			$("#video_data > table tr").append("<td>
				<table cellspacing=\"0\" cellpadding=\"10\">
					<tr>
						<td class=\"video_title\" align=\"center\"><h1>" + data[i].title + "</h1></td>
					</tr>
					<tr>
						<td class=\"thumb\" align=\"center\" onclick=\"zoombox.open('" + data[i].url + "');\" title=\"Avvia il video\">
							<img src=\"" + data[i].thumbnail_medium + "\" />
						</td>
					</tr>
					<tr>
						<td class=\"description\">
							<div class=\"expandable\">" + data[i].description + "</div>
						</td>
					</tr>
					<tr>
						<td class=\"tags\"><div>" + tags + "</div></td>
					</tr>
					<tr>
						<td>
							<ul>
								<li class=\"clock\">" + data[i].duration + "'</li>
								<li>Visto " + data[i].stats_number_of_plays + " " + volte_txt + "</li>
								<li class=\"comments\">" + data[i].stats_number_of_comments + "</li>
							</ul>
						</td>
					</tr>
				</table>
			</td>");
			
			if (i % colonne == 4) {
				$("#video_data > table table").append("</tr><tr>");
			}
			
			$("#video_data table").delay(delay_time).fadeIn(900);
		});
	}, "jsonp").success(function() {
		$(".expandable").readmore({
			substr_len: 125,
			ellipses: "&nbsp;"
		});
	});
});
</script>
<div id="video_data">
	<table cellpadding="10" cellspacing="0"><tr></tr></table>
</div>
Videolist;

require_once("common/include/conf/replacing_object_data.php");
?>