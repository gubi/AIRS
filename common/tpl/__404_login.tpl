<?php
header("HTTP/1.1 404 Not Found");

$content_title = $i18n["page_title_404"];
$content_subtitle = $i18n["page_subtitle_404"];
$content_last_edit = "";

$page_content_404 = str_replace("{LINK}", $GLOBALS["page_uri"], $i18n["page_content_404"]);
$create_it = $i18n["page_content_404_create_it"] . " " . $i18n["page_link_logn_verbose"];
$legend_login_data = $i18n["legend_login_data"];
$page_name_safety = $i18n["page_name_safety"];
$page_name_encryption_key = $i18n["page_name_encryption_key"];
$page_title_encryption_key = $i18n["page_title_encryption_key"];
$more_info_about_encryption_key = $i18n["more_info_about_encryption_key"];
$page_name_login = $i18n["page_name_login"];

$content_body = <<<__404
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/document_sans_cancel_128_ccc.png" />
		</td>
		<td valign="top" style="font-size: 1.1em;">
			$page_content_404.<br />
			<br />
			$create_it.
			<br />
			<br />
			<form action="" method="post">
				<fieldset>
					<legend>$legend_login_data</legend>
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
		</td>
	</tr>
</table>
__404;
$content_body = str_replace("{REFERER_PAGE}", $GLOBALS["function_page"], $content_body);
?>