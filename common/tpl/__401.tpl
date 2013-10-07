<?php
header("HTTP/1.1 401 Unauthorized");

$content_title = $i18n["page_title_401"];
$content_subtitle = $i18n["page_subtitle_401"];
$content_last_edit = "";

$page_content_401 = $i18n["page_content_401"];
$page_name_safety = $i18n["page_name_safety"];
$page_name_encryption_key = $i18n["page_name_encryption_key"];
$page_title_encryption_key = $i18n["page_title_encryption_key"];
$more_info_about_encryption_key = $i18n["more_info_about_encryption_key"];
$page_name_login = $i18n["page_name_login"];

$content_body = <<<__401
$page_content_401
<br />
<br />
<br />
<form action="" method="post">
	<fieldset>
		<legend>Dati per l'accesso</legend>
		<table cellspacing="5" cellpadding="5" style="width: 100%;">
			<tr>
				<th><label for="username">Username</label>
				<td>
					<input type="text" name="username" id="username" value="" autofocus="autofocus" required="required" />
					<input type="hidden" name="ref" id="ref" value="{REFERER_PAGE}" />
				</td>
			</tr>
			<tr>
				<th><label for="password">Password</label>
				<td>
					<input type="password" name="password" id="password" value="" required="required" />
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th><img src="common/media/img/document_sans_security_64_ccc.png" /></th>
				<td>
					<input type="password" size="36" name="key" id="key" value="" placeholder="$page_title_encryption_key" autocomplete="off" required="required" /><br/>
					<a style="margin: 10px 0;" href="$page_name_safety/$page_name_encryption_key" target="_blank">$more_info_about_encryption_key</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="login_btn" value="$page_name_login" />
				</td>
			</tr>
		</table>
	</fieldset>
</form>
__401;
$content_body = str_replace("{REFERER_PAGE}", $GLOBALS["referer_page"], $content_body);
?>