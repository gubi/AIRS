<?php
/**
* Calculate the day-age
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
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
header("Content-type: text/plain;");

if (isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	require_once("../../.mysql_connect.inc.php");
	$pdo = db_connect("");
	$select_user_birth = $pdo->query("select `birth` from `airs_users` where `username` = '" . addslashes($_GET["user"]) . "'");
	if ($select_user_birth->rowCount() > 0){
		while ($dato_select_user_birth = $select_user_birth->fetch()){
			if($dato_select_user_birth["birth"] == "0000-00-00"){
				print "no date";
			} else {
				$birth = explode("-", $dato_select_user_birth["birth"]);
				$giorno = $birth[0];
				$mese = $birth[1];
				$anno = $birth[2];
				
				$mesi = array (1 => "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
				$correggi_num_mese = str_replace("10", "1_", $mese);
				$correggi_num_mese = str_replace("0", "", $correggi_num_mese);
				$correggi_num_mese = str_replace("1_", "10", $correggi_num_mese);
				$nome_mese = $mesi[$correggi_num_mese];
				
				$data_di_nascita = $giorno . " " . $nome_mese . " " . $anno;
				$GLOBALS["birthdate"] = $mese . "/" . $giorno . "/" . $anno;
				$GLOBALS["birthyear"] = $anno;
				$GLOBALS["birthday"] = $giorno;
				$data_giusta_di_nascita = $anno . "-" . $mese . "-" . $giorno;
				$giorni_di_vita = (int)(abs(strtotime($data_giusta_di_nascita) - strtotime("now"))/86400); 

				$dec_complegiorno = $giorni_di_vita/100;
				if (is_float($dec_complegiorno)){
					$prox_complegiorno = ceil($dec_complegiorno)*100;
					$diff_giorni = $prox_complegiorno - $giorni_di_vita;
					
					if ($diff_giorni == 1){
						$next_complegiorno = "Domani ";
					} else if ($diff_giorni > 1 && $diff_giorni <= 10){
						if ($diff_giorni == 3){
							$next_complegiorno = "Fra " . $diff_giorni . " giorni ";
						} else {
							$next_complegiorno = "Tra " . $diff_giorni . " giorni ";
						}
					} else {
						$next_date = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") + $diff_giorni, date("Y")));
					
						list($n_giorno, $n_mese, $n_anno) = explode("/", $next_date);
						$g_da_corr = array("01", "02", "03", "04", "05", "06", "07", "08", "09");
						$g_corr_con = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
						$n_giorno = str_replace($g_da_corr, $g_corr_con, $n_giorno);
						
						if ($n_mese == date("m")){
							$next_complegiorno = "Il " . $n_giorno . " ";
						} else {
							$mesi = array (1=>"Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
							$correggi_num_mese = str_replace("10", "1_", $n_mese);
							$correggi_num_mese = str_replace("0", "", $correggi_num_mese);
							$correggi_num_mese = str_replace("1_", "10", $correggi_num_mese);
							$nome_n_mese = $mesi[$correggi_num_mese];
							
							$next_complegiorno = "Il " . $n_giorno . " " . $nome_n_mese . " ";
						}
					}
					$next_complegiorno .= "compirai " . $prox_complegiorno . " giorni.";
				} else {
					$next_complegiorno = "";
				}
				print 'Oggi hai compiuto <b style="font-size: 1.2em;">' . $giorni_di_vita . '</b> giorni di vita.<br />' . $next_complegiorno;
			}
		}
	} else {
		print "no date";
	}
}
?>