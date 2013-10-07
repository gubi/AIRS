<?php
header("HTTP/1.1 505 HTTP Version Not Supported");

$content_title = $i18n["page_title_505"];
$content_subtitle = $i18n["page_subtitle_505"];
$content_last_edit = "";
$page_content_405 = nl2br($i18n["page_content_505"]);
$ok_im_understand_txt = $i18n["ok_im_understand_txt"];

$content_body = <<<__405
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/security_closed_information_128_ccc.png" />
		</td>
		<td valign="top" style="font-size: 1.1em;">
			$page_content_405
			
			<button onclick="location.href=location.href.replace('http://', 'https://');">$ok_im_understand_txt</button>
		</td>
	</tr>
</table>
__405;
require_once("common/include/conf/replacing_object_data.php");
?>