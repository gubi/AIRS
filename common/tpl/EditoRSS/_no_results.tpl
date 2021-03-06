<?php
/**
* Generates no results page
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$content_title = "Ooops... Nessun risultato";
$content_subtitle = "Non vi sono risultati per questa opzione";
$content_last_edit = "";
$content_body = <<<__No_res
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/list_cancel_128_ccc.png" />
		</td>
		<td style="font-size: 1.1em;">
			Il contenuto che si vuole visualizzare non contiene risultati elencabili.<br />
			<br />
			&Egrave; possibile che non siano ancora state fatte automazioni al riguardo oppure che non siano state fatte opportune configurazioni al sistema.<br />
		</td>
	</tr>
</table>
__No_res;
$content_body = str_replace("{REFERER_PAGE}", $GLOBALS["referer_page"], $content_body);
?>