<?php

class i18n {
	public $key;
	
	public function __construct() {
		$this->config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/common/include/conf/airs.conf", 1);
		foreach($this->config as $configk => $configv){
			foreach($configv as $configkk => $configvv){
				$this->config[$configk][$configkk] = utf8_encode($configvv);
			}
		}
	}
	private function db_connect($db) {
		require_once($_SERVER["DOCUMENT_ROOT"] . "/common/include/.mysql_connect.inc.php");
		return db_connect($db);
	}
	public function get(){
		$pdoi18 = $this->db_connect("i18n");
		$pdo = $this->db_connect("");
		$i18ndb = $pdoi18->query("select * from `" . $this->config["language"]["default_language_name"] . "`");
		while($i18nf = $i18ndb->fetch()){
			if(!is_array($i18nf["key"])) {
				$i18n[$i18nf["key"]] = mb_convert_encoding($i18nf["value"], "HTML-ENTITIES", "UTF-8");
			} else {
				foreach($i18nf["key"] as $i18nkk => $i18nvv){
					$i18n[$i18nf["key"]][$i18nkk] = mb_convert_encoding($i18nvv, "HTML-ENTITIES", "UTF-8");
				}
			}
		}
		return $i18n;
	}
}
$ii18n = new i18n("it");
foreach($ii18n->get() as $i18nk => $i18nv){
	$i18n[$i18nk] = $i18nv;
}
?>