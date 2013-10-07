<?php
header("HTTP/1.1 404 Not Found");

$user_data = $pdo->query("select `name`, `lastname`, `email` from `airs_users` where `username` = '" . addslashes(strtolower($GLOBALS["page_id"])) . "'");
if($user_data->rowCount() > 0){
	while($dato_user = $user_data->fetch()){
		$contact = ucwords(strtolower($dato_user["name"] . " " . $dato_user["lastname"])) . " <" . $dato_user["email"] . ">";
	}
	$content_last_edit = "";
	$page_content_404_user = str_replace("{CONTACT}", $contact, $i18n["page_content_404_user"]);
	$content_body = <<<__404_user
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px">
				<img src="common/media/img/document_user_cancel_128_ccc.png" />
			</td>
			<td valign="top" style="font-size: 1.1em;">
				$page_content_404_user
			</td>
		</tr>
	</table>
__404_user;
} else {
	$page_content_404_no_user = $i18n["page_content_404_no_user"];
	$content_body = <<<__404_user
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px">
				<img src="common/media/img/user_half_cancel_128_ccc.png" />
			</td>
			<td valign="top" style="font-size: 1.1em;">
				$page_content_404_no_user
			</td>
		</tr>
	</table>
__404_user;
}
require_once("common/include/conf/replacing_object_data.php");
?>