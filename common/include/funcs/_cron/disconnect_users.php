<?php
header("Content-type: text/plain; charset=utf-8");
if(strlen($_SERVER["DOCUMENT_ROOT"]) == 0){
	$_SERVER["DOCUMENT_ROOT"] = "/var/www-dev/";
}

$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "common/include/conf/airs.conf", 1);
require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/.mysql_connect.inc.php");

$pdo_l = db_connect("system_logs");
$pdo = db_connect("");

$check_login_log = $pdo_l->query("select distinct(`user`) from `airs_login`");
if ($check_login_log->rowCount() > 0){
	while ($dato_check_login_log = $check_login_log->fetch()){
		if($dato_check_login_log["user"] !== ""){
			$check_user_session_length = $pdo->query("select `session_length` from `airs_users` where `username` = '" . addslashes($dato_check_login_log["user"]) . "'");
			if ($check_user_session_length->rowCount() > 0){
				while ($dato_user_session_length = $check_user_session_length->fetch()){
					//print $dato_user_session_length["session_length"] . "\n";
					$check_last_login = $pdo_l->query("select * from `airs_login` where `action` = 'login' and `user` = '" . addslashes($dato_check_login_log["user"]) . "' order by `data` desc limit 1");
					if ($check_last_login->rowCount() > 0){
						while ($dato_last_login = $check_last_login->fetch()){
							if ($dato_last_login["data"] == date("Y-m-d")){
								$from_time = strtotime($dato_last_login["ora"]);
								$to_time = strtotime(date("H:i:s"));
								$difference = round(abs($to_time - $from_time) / 6);
								if ($difference > $dato_user_session_length["session_length"]){
									$query_update = $pdo->prepare("update `airs_users` set `is_connected` = '0' where `username` = '" . addslashes($dato_check_login_log["user"]) . "'");
									if (!$query_update->execute()) {
										print "no";
									}
								}
							} else {
								$query_update = $pdo->prepare("update `airs_users` set `is_connected` = '0' where `username` = '" . addslashes($dato_check_login_log["user"]) . "'");
								if (!$query_update->execute()) {
									print "no";
								}
							}
						}
					}
				}
			}
		}
	}
}
?>