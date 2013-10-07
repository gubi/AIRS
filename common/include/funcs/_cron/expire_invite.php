<?php
/**
* Remove user that not confirm inviting subscription
* 
* PHP versions 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS_manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8");
if(strlen($_SERVER["DOCUMENT_ROOT"]) == 0){
	$_SERVER["DOCUMENT_ROOT"] = "/var/www-dev/";
}
$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "common/include/conf/airs.conf", 1);
require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/.mysql_connect.inc.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/funcs/mail.send.php");

$pdo = db_connect("");
$expire_invite = $pdo->query("select * from `airs_users` where `encryption_key` = ''");
if($expire_invite->rowCount() > 0){
	while($dato_expire = $expire_invite->fetch()){
		$expired_user_name = ucwords($dato_expire["name"]);
		$expired_user_fullname = ucwords($dato_expire["name"] . " " . $dato_expire["lastname"]);
		$expired_user_email = $dato_expire["email"];
		
		$up_now = strtotime(date("Y-m-d H:i:s"));
		$up_date = strtotime($dato_expire["date"]);
		$expire_date = date("d/m/Y", $up_date);
		$time_diff = round(($up_now - $up_date)/3600, 2);
		if($time_diff >= 48){
			$inviting_user = $pdo->query("select * from `airs_users` where `username` = '" . $dato_expire["created_by"] . "'");
			while($dato_inviting = $inviting_user->fetch()){
				$inviting_name = ucwords($dato_inviting["name"]);
				$inviting_fullname = ucwords($dato_inviting["name"] . " " . $dato_inviting["lastname"]);
				$inviting_email = ucwords($dato_inviting["email"]);
			}
			$remove_user = $pdo->prepare("delete from `airs_users` where `id` = '" . addslashes($dato_expire["id"]) . "'");
			$remove_user->bindParam(1, addslashes($_POST["user_level"]));
			if (!$remove_user->execute()) {
				$content_body = "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
			} else {
				$to = $expired_user_fullname . " <" . $expired_user_email . ">";
				$to_inv = $inviting_fullname . " <" . $inviting_email . ">";
				$subject = "L'invito di registrazione al sistema AIRS è scaduto!";
				$subject_inv = "L'invito per " . $expired_user_fullname . " al sistema AIRS è scaduto!";
				
				if (substr($expired_user_name, -1) == "a"){
					$was_txt = "stata";
					$invited_txt = "invitata";
					$informed_txt = "informata";
					$interested_txt = "interessata";
					$him_txt = "lei";
					$him_art = "la";
					$that = "le";
				} else {
					$was_txt = "stato";
					$invited_txt = "invitato";
					$informed_txt = "informato";
					$interested_txt = "interessato";
					$him_txt = "lui";
					$him_art = "lo";
					$that = "gli";
				}
				$text = $expired_user_name . ",\neri " . $was_txt . " " . $invited_txt . " da " . $inviting_name . " ad iscriverti al Sistema,\n";
				$text .= "ma purtroppo è scaduto il tempo limite per aderire (48 ore) e sei stato rimosso dal database.\n\n";
				$text .= "Tuttavia, ricorda che non è mai detta l'ultima poiché puoi sempre richiedere un nuovo invito da questo link: https://airs.inran.it/Sistema/Richiedi_invito_alla_registrazione \n\n";
				$text .= "È stato un piacere conoscerti, alla prossima occasione!";
				
				ob_start();
				require_once("../../../../include/funcs/mail.send.php");
				$send_mail_status = send_mail($to, $subject, $text);
				$output = ob_get_clean();
				
				if(trim($output) == "OK"){
					$text2 = "Ciao " . $inviting_name . ", il " . $expire_date . " hai invitato " . $expired_user_name . " a registrarsi al Sistema, ma ad oggi non sembra essersi " . $interested_txt . " alla cosa.\n";
					$text2 .= "La pre-registrazione che " . $that . " avevi fatto valeva 2 giorni (48 ore), e ora che tale tempo è trascorso è " . $was_txt . " rimosso dal database.\n";
					$text2 .= "Ti ricordo comunque, che in ogni momento potrà richiederne un'altra, e che ovviamente è già " . $was_txt . " " . $informed_txt . " di questo.\n\n";
				}
				$send_mail_status = send_mail($to_inv, $subject_inv, $text2);
			}
		}
	}
}
?>