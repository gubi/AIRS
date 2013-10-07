<?php
header("HTTP/1.1 404 Not Found");

$content_title = $i18n["page_title_404"];
$content_subtitle = $i18n["page_subtitle_404"];
$content_last_edit = "";

$page_content_404 = str_replace("{LINK}", $GLOBALS["page_uri"], $i18n["page_content_404"]);
if ($GLOBALS["user_level"] == 0){
	$create_it = "<br />" . $i18n["page_content_404_create_it"] . " <a href=\"./" . $i18n["page_name_special_login"] . "\">" . $i18n["page_link_logn_verbose"] . "</a>";
}
$content_body = <<<__404
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/document_sans_cancel_128_ccc.png" />
		</td>
		<td valign="top" style="font-size: 1.1em;">
			$page_content_404.<br />
			$create_it
		</td>
	</tr>
</table>
__404;
require_once("common/include/conf/replacing_object_data.php");
?>