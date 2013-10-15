<?php
/**
* Generates form for filter feed
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

$filter_feed = $pdo->query("select * from `editorss_feeds` where `user` = '" . addslashes($decrypted_user) . "' order by `title` asc");
if ($filter_feed->rowCount() > 0){
	$feed_list = "<option value=\"\">-- Seleziona feed --</option>";
	while($dato_filter_feed = $filter_feed->fetch()){
		if (trim($_POST["filter_feed"]) == $dato_filter_feed["id"]){
			$sel = " selected=\"selected\"";
		} else {
			$sel = "";
		}
		$feed_list .= "<option value=\"" . $dato_filter_feed["id"] . "\"" . $sel . ">" . $dato_filter_feed["title"] . "</option>";
	}
}
$filter = <<<Filter
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<script src="{ABSOLUTE_PATH}common/js/jquery_tag-it/tag-it.js" type="text/javascript" charset="utf-8"></script>
<link href="{ABSOLUTE_PATH}common/js/jquery_tag-it/jquery.tagit.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
	$(document).ready(function() {
		$("#tag").tagit({
			fieldName: "tags",
			singleField: true,
			singleFieldNode: $('#filter_tag'),
			removeConfirmation: true,
			allowSpaces: true,
			tagSource: function(search, showChoices) {
				var that = this;
				$.ajax({
					url: "common/include/funcs/_ajax/get_existing_tags.php",
					data: search,
					dataType: "json",
					success: function(choices) {
						showChoices(that._subtractArray(choices, that.assignedTags()));
					}
				});
			}
		}).click(function(){
			$(this).css({
				"border": "#999 1px solid",
				"box-shadow": "0 0 9px #ccc"
			});
		}).find("input").blur(function(){
			$("#tag").css({
				"border": "#ccc 1px solid",
				"box-shadow": "none"
			});
		}).focus(function(){
			$("#tag").css({
				"border": "#999 1px solid",
				"box-shadow": "0 0 9px #ccc"
			});
		});
	});
</script>
<table cellspacing="0" cellpadding="0" style="margin-top: -70px; margin-bottom: 10px; padding-bottom: 30px; width: 100%;">
	<tr>
		<td style="width: 50%;"></td>
		<td>
			<button id="show_hide_filter_table_btn" style="position: absolute; margin-top: 0; margin-right: 2px; right: 26px;" onclick="if($('#filter_table').css('display') == 'none'){ $('#show_hide_filter_table_btn').text('Nascondi'); $('#filter_table').slideDown(300); } else { $('#show_hide_filter_table_btn').text('Filtra risultati'); $('#filter_table').slideUp(300); }">Filtra risultati</button>
			<form method="post" action="" id="filter_table" style="display: none;">
				<fieldset>
					<legend>Filtra per</legend>
					<table cellpadding="5" cellspacing="5">
						<tr>
							<th>Feed</th>
							<td><select name="filter_feed" id="filter_feed">$feed_list</select></td>
						</tr>
						<tr>
							<th>Tag</th>
							<td>
								<input type="hidden" id="filter_tag" name="filter_tag" value="$filter_tag" />
								<ul id="tag"></ul>
							</td>
						</tr>
						<tr>
							<th>Parola chiave</th>
							<td>
								<input type="text" name="filter_key" value="$filter_key" style="width: 98%;" />
							</td>
						</tr>
						<tr>
							<td colspan="2" align="right">
								<input type="submit" name="filter_btn" value="Filtra" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</td>
	</tr>
</table>
Filter;
?>
