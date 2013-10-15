<?php
/**
* Generates Meeting page
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
* @package	AIRS_Meetings
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

require_once("common/include/lib/bbb-api-php/includes/bbb-api.php");
function is_meeting_time($end_date){
	if ((($end_date == "0000-00-00 00:00:00") ? strtotime($end_date) : strtotime(date("Y-m-d H:i:s"))) >= strtotime(date("Y-m-d H:i:s"))) { return true; } else { return false; }
}
function is_meeting_running($id){
	$bbb = new BigBlueButton();
	try {
		$is_running = $bbb->isMeetingRunningWithXmlResponseArray($id);
	} catch (exception $e) {
		print $e->getMessage();
	}
	$res = json_decode(json_encode($is_running), 1);
	if($is_running["running"][0] == "true") {
		return true;
	} else {
		return false;
	}
}
$bbb = new BigBlueButton();

$meeting_name = addslashes(str_replace("_", " ", $GLOBALS["page_title"]));
if(isset($_POST["log_to_conference_btn"])){
	$pdom = db_connect("meetings");
	$id = $_POST["meetingID"];
	
	if(!is_meeting_running($id) && is_meeting_time($end_date)){
		$current_meetings = $pdom->query("select * from `current_meetings` where `name` = '" . $meeting_name . "'");
		if($current_meetings->rowCount() > 0){
			while($dato_meeting = $current_meetings->fetch()){
				$description = stripslashes($dato_meeting["description"]);
				$attendee_pw = $dato_meeting["attendeePW"];
				$moderator_pw = $dato_meeting["moderatorPW"];
				$welcome_msg = preg_replace("/\n|\r/", "<br/>", $dato_meeting["welcome_message"]);
				$dial_number = $dato_meeting["dialNumber"];
				$voice_bridge = $dato_meeting["voiceBridge"];
				$web_voice = $dato_meeting["webVoice"];
				$logout_url = $dato_meeting["logoutURL"];
				$max_participants = $dato_meeting["maxParticipants"];
				$record = "false";
				$duration = $dato_meeting["duration"];
				$creation_date = $dato_meeting["creation_date"];
				$end_date = $dato_meeting["end_date"];
				$user = $dato_meeting["user"];
				
				if($user == $decrypted_user){
					$main_pwd = $moderator_pw;
				} else {
					$main_pwd = $attendee_pw;
				}
			}
			$creationParams = array(
				"meetingId" => $id,
				"meetingName" => $meeting_name,
				"attendeePw" => $attendee_pw,
				"moderatorPw" => $moderator_pw,
				"welcomeMsg" => urlencode($welcome_msg),
				"dialNumber" => $dial_number,
				"voiceBridge" => $voice_bridge,
				"webVoice" => $web_voice, // NULL at this time
				"logoutUrl" => $logout_url,
				"maxParticipants" => (int)$max_participants,
				"record" => $record,
				"duration" => (int)$duration
			);
			try {
				$result = $bbb->createMeetingWithXmlResponseArray($creationParams);
			} catch (exception $e) {
				print $e->getMessage();
			}
		}
	}
	$parameters["fullName"] = $_POST["fullName"];
	$parameters["meetingID"] = $id;
		if(strlen(trim($_POST["password"])) == 0){
			$pass = "BBBuserPwd";
		} else {
			$pass = $_POST["password"];
		}
	$parameters["password"] = $pass;
	$construct_url = http_build_query($parameters);
	$checksum = sha1("join" . $construct_url . "06a7ff02211a31d2e01fe1f646733677");
	$url = $absolute_path90 . "bigbluebutton/api/join?". $construct_url . "&checksum=" . $checksum;
	
	header("Location: " . $url);
} else {
	$content_title = "<a href=\"./Meeting\" title=\"" . $i18n["go_to_page"] . " Meeting\">Meeting</a>: " . $meeting_name;
	$content_subtitle = $i18n["meeting_schede_subtitle"];

	$pdom = db_connect("meetings");
	$current_meetings = $pdom->query("select * from `current_meetings` where `name` = '" . $meeting_name . "'");
	if($current_meetings->rowCount() > 0){
		while($dato_meeting = $current_meetings->fetch()){
			$id = $dato_meeting["id"];
			$description = stripslashes($dato_meeting["description"]);
			$attendee_pw = $dato_meeting["attendeePW"];
			$moderator_pw = $dato_meeting["moderatorPW"];
			$welcome_msg = $dato_meeting["welcome_message"];
			$dial_number = $dato_meeting["dialNumber"];
			$voice_bridge = $dato_meeting["voiceBridge"];
			$web_voice = $dato_meeting["webVoice"];
			$logout_url = $dato_meeting["logoutURL"];
			$max_participants = $dato_meeting["maxParticipants"];
			$record = "false";
			$duration = $dato_meeting["duration"];
			$creation_date = $dato_meeting["creation_date"];
			$end_date = $dato_meeting["end_date"];
			$user = $dato_meeting["user"];
			
			if($user == $decrypted_user){
				$main_pwd = $moderator_pw;
			} else {
				$main_pwd = $attendee_pw;
			}
		}
		$meeting_content = "<ul>";
			$meeting_content .= "<li>" . $i18n["creation_date"] . ": <b>" . date("d/m/Y \a\l\l\e H:i:s", strtotime($creation_date)) . "</b></li>";
			if($end_date !== "0000-00-00 00:00:00"){
				$the_end_date = date("d/m/Y \a\l\l\e H:i:s", strtotime($end_date));
			} else {
				$the_end_date = "-";
			}
			$meeting_content .= "<li>" . $i18n["end_date"] . ": <b>" . $the_end_date . "</b></li>";
			$start_date = new DateTime(date("Y-m-d H:i:s"));
			$since_start = $start_date->diff(new DateTime($end_date));
			if(is_meeting_time($end_date)){
				$status = "<span style=\"color: #ff0000;\">" . $i18n["expired"] . "</span>";
			} else {
				if(is_meeting_running($id)) {
					$status = "<span style=\"color: #01af00;\">" . $i18n["running"] . "</span>";
				} else {
					$status = "<span style=\"color: #adaf00;\">" . $i18n["open"] . "</span>";
				}
			}
			$meeting_content .= "<li>" . $i18n["status"] . ": <b>" . $status . "</b></li>";
		$meeting_content .= "</ul>";
		$meeting_content .= "<ul>";
			if ($duration == "0"){
				$duration = "<i>" . $i18n["meeting_end_at_session"] . "</i>";
			}
			$meeting_content .= "<li>" . $i18n["meeting_voip_number_voice_call"] . ": <b>" . $dial_number . "</b></li>";
			$meeting_content .= "<li>" . $i18n["meeting_max_participants_number"] . ": <b>" . $max_participants . "</b></li>";
			$meeting_content .= "<li>" . $i18n["duration"] . ": <b>" . $duration . "</b></li>";
		$meeting_content .= "</ul>";
		$leave_blank = $i18n["leave_empty_if_you_not_have_password"];
		if(trim($attendee_pw) !== "BBBuserPwd") {
			$password_tr = '<tr><th>Password:</th><td><input type="password" style="font-size: 1.3em !important;" name="password" value="" /><br />' . $leave_blank . '</td></tr>';
		} else {
			$password_tr = "";
		}
		
		if($since_start->invert == 0 || $end_date == "0000-00-00 00:00:00"){
			$log_to_conference = $i18n["page_name_login"];
			$meeting_content .= <<<Access_form
			<hr />
			<h1>$log_to_conference</h1>
			<br />
			<form method="post">
				<table class="frm">
					<tr>
						<td style="width: 130px;">
							<img src="common/media/img/chat_run_128_ccc.png" />
						</td>
						<td>
							<table style="font-size: 1.3em;">
								<tr>
									<th>Nickname:</th>
									<td>
										<input type="hidden" name="meetingID" value="$id" />
										<input type="text" style="font-size: 1.3em;" name="fullName" placeholder="Nickname" value="$decrypted_user" />
										<input type="submit" name="log_to_conference_btn" style="display: none;" value="$log_to_conference" />
									</td>
								</tr>
								$password_tr
							</table>
						</td>
					</tr>
				</table>
			</form>
Access_form;
		}
		$meeting_img = "chat_information_128_ccc.png";
	} else {
		$meeting_content = $i18n["no_meeting_found"];
		$meeting_img = "chat_cancel_128_ccc.png";
	}
	require_once("Text/Wiki.php");
	require_once("common/include/conf/Wiki/rendering.php");
	$output = $wiki->transform(stripslashes(utf8_decode($description)), "Xhtml");
	$output = utf8_decode(mb_convert_encoding($output, "UTF-8", "HTML-ENTITIES"));
	
	$content_body = <<<Meeting_data
		$output<br /><hr /><table cellspacing="10" cellpadding="10" style="width: 100%;">
			<tr>
				<td style="width: 128px" valign="top">
					<img src="common/media/img/$meeting_img" />
				</td>
				<td valign="top" style="font-size: 1.1em;">
					$meeting_content<br />
				</td>
			</tr>
		</table>
Meeting_data;
}
?>