<?php
class System_automation {
	public $params;
	
	public function __construct($params) {
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		if(strlen($_SERVER["DOCUMENT_ROOT"]) == 0){
			$_SERVER["DOCUMENT_ROOT"] = "/var/www-dev/";
		}
		require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/funcs/mail.send.php");
		
		//require_once("browser.php");
		$this->params = $params;
	}
	private function get_config() {
		if(strlen($_SERVER["DOCUMENT_ROOT"]) == 0){
			$_SERVER["DOCUMENT_ROOT"] = "/var/www-dev/";
		}
		$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "common/include/conf/airs.conf", 1);
		return $config;
	}
	private function db_connect($db) {
		if(strlen($_SERVER["DOCUMENT_ROOT"]) == 0){
			$_SERVER["DOCUMENT_ROOT"] = "/var/www-dev/";
		}
		require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/.mysql_connect.inc.php");
		require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/funcs/_create_link.php");
		return db_connect($db);
	}
	private function i18n($word) {
		$config = $this->get_config();
		$pdoi18n = $this->db_connect("i18n");
		
		$i18ndb = $pdoi18n->query("select * from `" . $config["language"]["default_language_name"] . "`");
		if($i18ndb->rowCount() > 0){
			while($i18nf = $i18ndb->fetch()){
				if(!is_array($i18nf["key"])) {
					$i18n[$i18nf["key"]] = mb_convert_encoding($i18nf["value"], "HTML-ENTITIES", "UTF-8");
				} else {
					foreach($i18nf["key"] as $i18nkk => $i18nvv){
						$i18n[$i18nf["key"]][$i18nkk] = mb_convert_encoding($i18nvv, "HTML-ENTITIES", "UTF-8");
					}
				}
			}
			return $i18n[$word];
		} else {
			return "error: " . $pdoi18n->errorCode();
		}
	}
	private function get_user_data() {
		$pdo = $this->db_connect("");
		$check_user = $pdo->query("select * from `airs_users` where `username` = '" . $this->params["user"] . "'");
		if ($check_user->rowCount() > 0){
			while ($dato_check_user = $check_user->fetch()) {
				$this->user_data["name"] = ucwords(strtolower($dato_check_user["name"]));
				$this->user_data["lastname"] = ucwords(strtolower($dato_check_user["lastname"]));
				$this->user_data["username"] = $dato_check_user["username"];
				$this->user_data["email"] = $dato_check_user["email"];
				$this->user_data["newsletter_frequency"] = $dato_check_user["newsletter_frequency"];
				
				$this->user_data["contact"] = ucwords(strtolower($dato_check_user["name"] . " " . $dato_check_user["lastname"])) . "<" . $dato_check_user["email"] . ">";
			}
		} else {
			$this->user_data = array();
		}
		return $this->user_data;
	}
	private function set_frequency($frequency, $type = "action"){
		$action = "";
		
		$giorni = array("Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab");
		$data = date("Y-m-d");
		$giorno = $giorni[date("w",strtotime($data))];
		$ora24 = date("G");
		$ora12 = date("g");
		
		switch($frequency){
			case "force":			// Force executions
				$action = true;
				$frequency_txt = "";
				break;
			case "only_one":		// Only one execution
				$action = true;
				$frequency_txt = "";
				break;
			case "half_hour":		// Half hour
				$action = true;
				$frequency_txt = "";
				break;
			case "1hours":			// One hour
				$action = (date("i") == "00") ? true : false;
				$frequency_txt = "orario ";
				break;
			case "3hours":			// 3 hours
				$action = ($ora12 % 3 == 0) ? true : false;
				$frequency_txt = "";
				break;
			case "6hours":			// 6 hours
				$action = ($ora12 % 6 == 0) ? true : false;
				$frequency_txt = "";
				break;
			case "daily":			// One day
				$action = ($ora24 == 0) ? true : false;
				$frequency_txt = "giornaliero ";
				break;
			case "weekly":			// One week
				$action = ($giorno == "Dom") ? true : false;
				$frequency_txt = "settimanale ";
				break;
			case "fortnightly":		// 15 days
				$action = (date("W") % 2 == 0) ? true : false;
				$frequency_txt = "bi-settimanale ";
				break;
			case "monthly":		// One month
				$action = (date("j") == 1) ? true : false;
				$frequency_txt = "mensile ";
				break;
			default:
				$action = false;
				$frequency_txt = "";
				break;
		}
		if ($type == "action"){
			return $action;
		} else {
			return $frequency_txt;
		}
	}
	
	public function start() {
		$check_automation = $this->check_automation();
		//print_r($check_automation);
		if($check_automation["total"] && $check_automation["total"] > 0){
			ob_start();
			ob_implicit_flush(true);
			set_time_limit(0);
				$this->sleep_echo($check_automation);
			ob_end_flush();
		}
	}
	private function exec_routines($check_automation, $i) {
		$config = $this->get_config();
		
		if (isset($this->params["force_run"]) && trim($this->params["force_run"]) == "true"){
			$frequency = "force";
		} else {
			$frequency = $check_automation[$i]["frequency"];
		}
		if ($this->set_frequency($frequency)){
			if ($check_automation[$i]["frequency"] == "only_one"){
				if ($check_automation[$i]["startdate"] >= 0 && $check_automation[$i]["starttime"] >= 0){
					if ($check_automation[$i]["runs"] == 0){
						$act = true;
					} else {
						$act = false;
					}
				} else {
					$act = false;
				}
			} else {
				if ($check_automation[$i]["startdate"] >= 0){
					$act = true;
				} else {
					$act = false;
				}
			}
		}
		if ($act == true){
			$pdo = $this->db_connect("");
			
			// Exec actions
			if ($check_automation[$i]["is_db_query"] == 1){
				$db_query = $pdo->prepare($check_automation[$i]["action"]);
				$db_query->execute();
				
				$log["type"] = "database";
				$log["user"] = $check_automation[$i]["user"];
				$log["action"] = "interrogazione del database: " . $check_automation[$i]["action"];
			} else if ($check_automation[$i]["is_uri_execution"] == 1){
				if (substr($check_automation[$i]["action"], 0, 6) == "common"){
					$scan_uri = $config["system"]["default_host_uri"] . $check_automation[$i]["action"];
				} else {
					$scan_uri = $check_automation[$i]["action"];
				}
				// RIVEDERE IL BROWSE.PHP PERCHÉ NON SEGUE I 302
				//browse($scan_uri, $GLOBALS["decrypted_user"]);
				$ft = file_get_contents($scan_uri);
				$parsed_url = parse_url($check_automation[$i]["action"]);
				
				$log["type"] = "scan";
				$log["user"] = $check_automation[$i]["user"];
				$log["action"] = "Acquisizione contenuti da: " . urldecode(strstr($parsed_url["query"], "http"));
			} else {
				eval(utf8_decode($check_automation[$i]["action"]));
				
				$log["type"] = "function";
				$log["user"] = $check_automation[$i]["user"];
				$log["action"] = "Avvio della funzione: " . $check_automation[$i]["action"];
				
			}
			$db_query = $pdo->prepare("update `airs_automation` set `runs` = '" . ($check_automation[$i]["runs"] + 1) . "' where `id` = '" . $check_automation[$i]["id"] . "'");
			$db_query->execute();
			
			return $log;
		}
	}
	private function sleep_echo($check_automation = array()) {
		$secs = (int)$check_automation["total"];
		$buffer = str_repeat(".", 4096);
		
		for ($i = 0; $i < $secs; $i++) {
			$logs[] = $this->exec_routines($check_automation, $i);
			//$log[] = $check_automation[$i]["action"];
			print "STARTED :: Automazione n° " . ($i + 1) . " eseguita il " . date("d-m-Y \a\l\l\\e H:i:s", time()) . " da: " . $this->params["user"] . "\n";
			print "ACTION: " . $check_automation[$i]["action"] . "\n";
			print $buffer . "\n";
			ob_flush();
			flush();
			sleep(1);
			
				$actions_txt .= "* " . $logs[$i]["action"] . "\n";
				$actions_html .= "<li>" . $logs[$i]["action"] . "</li>\n";
				if (($this->params["force_run"]) && trim($this->params["force_run"]) == "true"){
					$frequency_txt = $this->set_frequency("force", "frequency_txt");
				} else {
					$frequency_txt = $this->set_frequency($logs[$i]["frequency"], "frequency_txt");
				}
			if(($i + 1) == $secs){
				switch($secs) {
					case 0:
						$message_txt = "è stata inoltrata una richiesta di operazione automatica\nma la sua esecuzione non ha prodotto variazioni registrabili.";
						$message_html = "&egrave; stata inoltrata una richiesta di operazione automatica ma la sua esecuzione non ha prodotto variazioni registrabili.<br /><br /><hr /><br />";
						break;
					case 1:
						$message_txt = "è stata inoltrata una richiesta di operazione automatica\ne la sua esecuzione ha registrato una sola variazione:" . $actions_txt . "\n\n" . str_repeat("-", 100) . "\n";
						$message_html = "&egrave; stata inoltrata una richiesta di operazione automatica e la sua esecuzione ha registrato una sola variazione:<br /><br /><hr /><ul>" . create_link($actions_html) . "</ul><br /><br /><hr /><br />";
						break;
					default:
						$message_txt = "è stata inoltrata una richiesta di operazione automatica\ne la sua esecuzione ha registrato " . $secs . " scansioni.\nA seguire tutti i dettagli delle operazioni:\n\n" .  str_repeat("-", 100) . "\n" . $actions_txt . "\n\n" . str_repeat("-", 100) . "\n";
						$message_html = "&egrave; stata inoltrata una richiesta di operazione automatica e la sua esecuzione ha registrato " . $secs . " scansioni.<br />A seguire l'elenco delle operazioni effettuate:<br /><br /><hr /><ul>" . create_link($actions_html) . "</ul><br /><br /><hr /><br />";
						break;
				}
				$user_data = $this->get_user_data();
				$text = $user_data["name"] . ",\n" . $message_txt;
				$html_text = $user_data["name"] . ",<br />" . $message_html;
				$subject = "Report " . $frequency_txt . "di automazione";
				$to = ucwords(strtolower($user_data["name"] . " " . $user_data["lastname"])) . "<" . $user_data["email"] . ">";
				
				if(send_mail($to, $subject, $text, $html_text)) {
					return "OK";
				}
				print "END\n";
				print_r($log);
			}
		}
	}
	private function check_automation() {
		$automation = array();
		
		$pdo = $this->db_connect("");
		if(!isset($this->params["id"])){
			$check_automation_query = "select * from `airs_automation` where `type` = '" . addslashes($this->params["type"]) . "' and `user` = '" . addslashes($this->params["user"]) . "'";
		} else {
			$check_automation_query = "select * from `airs_automation` where `research_id` = '" . addslashes($this->params["id"]) . "'";
		}
		$check_automation = $pdo->prepare($check_automation_query);
		if($check_automation->execute()) {
			$c = -1;
			while($dato_check_automation = $check_automation->fetch()){
				$c++;
				$automation["total"] = $check_automation->rowCount();
				$automation[$c]["id"] = $dato_check_automation["id"];
				$automation[$c]["action"] = $dato_check_automation["action"];
				$automation[$c]["frequency"] = $dato_check_automation["frequency"];
				$automation[$c]["is_db_query"] = $dato_check_automation["is_db_query"];
				$automation[$c]["is_uri_execution"] = $dato_check_automation["is_uri_execution"];
				$automation[$c]["startdate"] = $dato_check_automation["startdate"];
				$automation[$c]["starttime"] = $dato_check_automation["starttime"];
				$automation[$c]["runs"] = $dato_check_automation["runs"];
				$automation[$c]["user"] = $dato_check_automation["user"];
			}
			return $automation;
		} else {
			$config = $this->get_config();
			
			$subject = "Errore di connessione al database!";
			$text = "Ciao,\nsi è verificato un problema con la connessione al database durante il check delle automazioni.\n\nLa query eseguita che ha riportato l'errore è la seguente:\n> " . $check_automation_query;
			$html_text = "Ciao,<br />si &egrave; verificato un problema con la connessione al database durante il check delle automazioni.<br /><br />La query eseguita che ha riportato l'errore &egrave; la seguente:<br /><tt>" . $check_automation_query . "</tt>";
			
			if(send_mail($config["mail"]["Errors-To"], $subject, $text, $html_text)) {
				print "Error: bad sql query.\nAn e-mail was sent to administrator";
				exit();
			}
		}
	}
}
?>