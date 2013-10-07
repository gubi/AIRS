<?php
/**
* Generates template error if user logged (usually registration page)
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

header("HTTP/1.1 405 Method Not Allowed");

$content_title = "Ooops... Errore 405";
$content_subtitle = "Non consentito per un utente registrato";
$content_last_edit = "";
$content_body = <<<__405
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/document_user_cancel_128_ccc.png" />
		</td>
		<td style="font-size: 1.1em;">
			Non è possibile proseguire con questa operazione attraverso la sessione di un altro utente registrato.<br />
			Prima è necessario disconnettersi.<br />
			<br />
			<a href="./Speciale:Esci">Disconnetti</a>
		</td>
	</tr>
</table>
__405;
require_once("common/include/conf/replacing_object_data.php");
?>