<?php
/**
* Generates template for e-mail not sent
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

$content_last_edit = "";
$content_body = <<<__SENT
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/mail_cancel_128_ccc.png" />
		</td>
		<td valign="top" style="font-size: 1.1em;">
			<h1>ALT!</h1>
			<br />
			Non è stato possibile proseguire il processo perché si è generato un errore durante l'invio dell'immagine.<br />
			<br />
			Per favore, riprova in un secondo momento, oppure <a href="mailto: {DEVELOPERS_MAIL}">contatta gli sviluppatori</a>
		</td>
	</tr>
</table>
__SENT;
require_once("common/include/conf/replacing_object_data.php");
?>