<?php
header("HTTP/1.1 405 Method Not Allowed");

$content_title = $i18n["page_title_405"];
$content_subtitle = $i18n["page_subtitle_405"];
$content_last_edit = "";
$page_content_405 = $i18n["page_content_405"];

$content_body = <<<__405
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/document_sans_cancel_128_ccc.png" />
		</td>
		<td valign="top" style="font-size: 1.1em;">
			$page_content_405
		</td>
	</tr>
</table>
__405;
require_once("common/include/conf/replacing_object_data.php");
?>