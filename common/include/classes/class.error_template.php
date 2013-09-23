<?php
/**
* Generate warning or error message
*
* @category	SystemScript
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @license	http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
* @link		http://airs.inran.it/
* 
* @SLM_is_core	true
* @SLM_status	testing
 */
 
 class Message {
	private $basic_template = '<table cellspacing="10" cellpadding="10" style="width: 100%;"><tr><td style="width: 128px"><img src="common/media/img/{IMG}" /></td><td valign="top" style="font-size: 1.1em;">{CONTENT}</td></tr></table>';
	private $login_template = <<<Login_template
	{CONTENT}
	<br />
	<br />
	<br />
	<form action="" method="post">
		<fieldset>
			<legend>{LOGIN_LEGEND}</legend>
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
						<input type="password" size="36" name="key" id="key" value="" placeholder="{PAGE_TITLE_ENCRYPTION_KEY}" autocomplete="off" required="required" />
						<p><a style="margin: 10px 0;" href="{PAGE_NAME_SAFETY}/{PAGE_NAME_ENCRYPTION_KEY}" target="_blank">{MORE_INFO_ABOUT_ENCRYPTION_KEY}</a></p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="login_btn" value="{PAGE_NAME_LOGIN}" />
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
Login_template;
	private function adjust_txt($login_template, $error_type, $img, $message) {
		if(is_array($this->i18n)){
			return $login_template = str_replace(
					array(
						"{PAGE_NAME_LOGIN}",
						"{PAGE_NAME_SAFETY}",
						"{PAGE_NAME_ENCRYPTION_KEY}",
						"{PAGE_TITLE_ENCRYPTION_KEY}",
						"{MORE_INFO_ABOUT_ENCRYPTION_KEY}",
						"{LOGIN_LEGEND}",
						"{CONTENT}",
						"{IMG}"
					),
					array(
						$this->i18n["page_name_login"],
						$this->i18n["page_name_safety"],
						$this->i18n["page_name_encryption_key"],
						$this->i18n["page_title_encryption_key"],
						$this->i18n["more_info_about_encryption_key"],
						$this->i18n["legend_login_data"],
						($this->i18n["page_content_" . $error_type] ? $this->i18n["page_content_" . $error_type] : $message),
						$img
					),
					$login_template);
		} else {
			return "Error: i18n is not defined!";
		}
	}
	public function set_i18n($i18n){
		$this->i18n = $i18n;
	}
	
	public function get_title($error_type) {
		return $this->i18n["page_title_" . $error_type];
	}
	public function get_subtitle($error_type) {
		return $this->i18n["page_subtitle_" . $error_type];
	}
	public function generate_error($error_type, $has_login = false) {
		switch ($error_type) {
			case 401:
				header("HTTP/1.1 401 Unauthorized");
				
				$has_login = true;
				$GLOBALS["content_title"] = $this->i18n["page_title_" . $error_type];
				$GLOBALS["content_subtitle"] = $this->i18n["page_subtitle_" . $error_type];
				break;
			case 404:
				header("HTTP/1.1 404 Not Found");
				
				$img = "document_sans_cancel_128_ccc.png";
				$message = str_replace("{LINK}", $GLOBALS["page_uri"], $this->i18n["page_content_404"]) . "<br /><br />" . $this->i18n["page_content_404_create_it"] . " <a href=\"./" . $this->i18n["page_name_special_login"] . "\">" . $this->i18n["page_link_logn_verbose"] . "</a>";
				$GLOBALS["content_title"] = $this->i18n["page_title_" . $error_type];
				$GLOBALS["content_subtitle"] = $this->i18n["page_subtitle_" . $error_type];
				break;
			case 4040:
				header("HTTP/1.1 404 Not Found");
				require_once($_SERVER["DOCUMENT_ROOT"] . "/common/include/.mysql_connect.inc.php");
				
				$pdo = db_connect("");
				$user_data = $pdo->query("select `name`, `lastname`, `email` from `airs_users` where `username` = '" . addslashes(strtolower($GLOBALS["page_id"])) . "'");
				if($user_data->rowCount() > 0){
					while($dato_user = $user_data->fetch()){
						$GLOBALS["content_title"] = ucwords(strtolower($dato_user["name"] . " " . $dato_user["lastname"]));
						$GLOBALS["content_subtitle"] = $this->i18n["page_subtitle_" . $error_type];
						$contact = ucwords(strtolower($dato_user["name"] . " " . $dato_user["lastname"])) . " <" . $dato_user["email"] . ">";
					}
					$img = "document_user_cancel_128_ccc.png";
					$message = str_replace("{CONTACT}", $contact, $this->i18n["page_content_404_user"]);
				} else {
					$GLOBALS["content_title"] = $GLOBALS["page_id"];
					$GLOBALS["content_subtitle"] = $this->i18n["page_subtitle_" . $error_type];
					$img = "user_half_cancel_128_ccc.png";
					$message = $this->i18n["page_content_404_no_user"];
				}
				break;
			case 405:
				header("HTTP/1.1 405 Method Not Allowed");
				
				$GLOBALS["content_title"] = $this->i18n["page_title_" . $error_type];
				$GLOBALS["content_subtitle"] = $this->i18n["page_subtitle_" . $error_type];
				$img = "document_sans_security_128_ccc.png";
				$message = $this->i18n["page_content_405"];
				break;
			case 505:
				header("HTTP/1.1 505 HTTP Version Not Supported");
				
				$GLOBALS["content_title"] = $this->i18n["page_title_" . $error_type];
				$GLOBALS["content_subtitle"] = $this->i18n["page_subtitle_" . $error_type];
				break;
		}
		if($has_login){
			return $this->adjust_txt($this->login_template, $error_type, $img, $message);
		} else {
			return $this->adjust_txt($this->basic_template, $error_type, $img, $message);
		}
	}
 }
 
//$message = new Message();
//$message->set_i18n($i18n);
//print $message->generate_error(404, true);
 ?>