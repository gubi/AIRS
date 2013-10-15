<?php
/**
* Read last mail
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
* @package	AIRS_Mailbox
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
if (trim($messaggi_totali) == ""){
	require_once("common/include/funcs/mail.global_functions.php");
}
if($inbox = imap_open("{mail.inran.it:143/notls}INBOX", "airs.dev", "dicembre.2010")){
	if (trim($messaggi_totali) == ""){
		$messaggi_totali = imap_num_msg($inbox);
		
		$headers = imap_header($inbox, $messaggi_totali);
		$struttura = imap_fetchstructure($inbox, $messaggi_totali);
		$mittente = $headers->fromaddress;
		$mittente = ucwords(just_subj($mittente));
		$oggetto = $headers->subject;
		$body = imap_body($inbox, $messaggi_totali);
		$data = date("j/n/Y G:i:s",strtotime($headers->MailDate));
		
		if (checkAttachments($struttura)){
			$att_img = "<img style=\"vertical-align: top;\" src=\"common/media/img/attach.png\" />";
		} else {
			$att_img = "&nbsp;&nbsp;";
		}
		$mail_box = "In totale hai " . $messaggi_totali . " messaggi ricevuti, nessuno dei quali &egrave; stato letto";
		
		$mail_box .= <<<Mail_box
		<table style="float: right; padding: 2px 0;" cellpadding="2" cellspacing="0">
			<tr>
				<td>
					<a href="javascript:void(0)" title="Marca tutti i messaggi come letti"><img src="common/media/img/viewer_text_16.png" /></a>
				</td>
				<td>
					<a href="javascript:void(0)" title="Recupera un'e-mail"><img src="common/media/img/security_open_16_333.png" /></a>
				</td>
			</tr>
		</table>
		<table style="width: 100%; padding: 2px 0;" class="table_mail" cellpadding="5" cellspacing="0">
			<tr>
				<td>Mittente</td>
				<td style="width: 50%;">Oggetto</td>
				<td>Data</td>
			</tr>
			<tr>
				<th style="border-bottom: #ccc 1px solid; border-left: #ccc 1px solid;">$att_img $mittente</th>
				<th style="border-bottom: #ccc 1px solid;">$oggetto</th>
				<th style="border-bottom: #ccc 1px solid; border-right: #ccc 1px solid; width: 126px">$data</th>
			</tr>
		</table>
Mail_box;
	} else {
		$headers = imap_header($inbox, $m);
		$struttura = imap_fetchstructure($inbox, $m);
		$mittente = $headers->fromaddress;
		$mittente = just_subj($mittente);
		$oggetto = $headers->subject;
		$body = imap_body($inbox, $m);
		$data = date("j/n/Y G:i:s", strtotime($headers->date));
		
		if (checkAttachments($struttura)){
			$att_img = "<img style=\"vertical-align: top;\" src=\"common/media/img/attach.png\" />";
		} else {
			$att_img = "&nbsp;&nbsp;";
		}
		$mail_box .= <<<Mail_box
		<table style="float: right; padding: 2px 0;" cellpadding="2" cellspacing="0">
			<tr>
				<td>
					<a href="javascript:void(0)" title="Marca tutti i messaggi come letti"><img src="common/media/img/viewer_text_16.png" /></a>
				</td>
				<td>
					<a href="javascript:void(0)" title="Recupera un'e-mail"><img src="common/media/img/security_open_16_333.png" /></a>
				</td>
			</tr>
		</table>
		<table style="width: 100%; padding: 2px 0;" class="table_mail" cellpadding="5" cellspacing="0">
			<tr>
				<td>Mittente</td>
				<td style="width: 50%;">Oggetto</td>
				<td>Data</td>
			</tr>
			<tr>
				<th style="border-bottom: #ccc 1px solid; border-left: #ccc 1px solid;">$att_img $mittente</th>
				<th style="border-bottom: #ccc 1px solid;">$oggetto</th>
				<th style="border-bottom: #ccc 1px solid; border-right: #ccc 1px solid; width: 126px;">$data</th>
			</tr>
		</table>
Mail_box;
	}
} else {
	$mail_box = "Si &egrave; verificato un errore di connessione alla casella di posta elettronica.";
}
?>