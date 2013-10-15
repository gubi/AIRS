<?php
/**
* Generates template for no automation started
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
$content_title = "Ooops... Ancora nessuna automazione";
$content_subtitle = "Non vi sono risultati per questa automazione";
$content_last_edit = "";
$content_body = <<<__No_res
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/recent_changes_128_ccc.png" />
		</td>
		<td style="font-size: 1.1em;">
			Il contenuto che si vuole visualizzare ancora non contiene risultati elencabili ma probabilmente è in fase di scansione.<br />
			<br />
			&Egrave; facile che non siano ancora state fatte automazioni al riguardo perch&eacute; il lavoro di scansione sia stato programmato in un tempo posticipato.<br />
		</td>
	</tr>
</table>
__No_res;
$content_body = str_replace("{REFERER_PAGE}", $GLOBALS["referer_page"], $content_body);
?>