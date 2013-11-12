<?php
/**
* Check mails
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
* @package	AIRS_Mailbox
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8");

function mail_data($inbox, $msg_id, $with_body = false){
	$headers = imap_headerinfo($inbox, $msg_id);
	$struttura = imap_fetchstructure($inbox, $msg_id);
	
		$mail["id"] = $msg_id;
		$mail["special"] = "<img src=\"common/media/img/star_outline_16_ccc.png\" />";
		$mail["from"] = "<a href=\"./Mailbox/Scrivi#" . trim(ucwords(just_subj($headers->fromaddress))) . "\">" . trim(ucwords(just_subj($headers->fromaddress))) . "</a>";
		$mail["fetchfrom"] = trim(ucwords(just_subj($headers->fetchfrom)));
		$mail["to"] = "<a href=\"./Mailbox/Scrivi#" . trim(ucwords(just_subj($headers->toaddress))) . "\">" . trim(ucwords(just_subj($headers->toaddress))) . "</a>";
		$mail["cc"] = "<a href=\"./Mailbox/Scrivi#" . trim(ucwords(just_subj($headers->ccaddress))) . "\">" . trim(ucwords(just_subj($headers->ccaddress))) . "</a>";
		$mail["bcc"] = "<a href=\"./Mailbox/Scrivi#" . trim(ucwords(just_subj($headers->bccaddress))) . "\">" . trim(ucwords(just_subj($headers->bccaddress))) . "</a>";
		$mail["sender"] = "<a href=\"./Mailbox/Scrivi#" . trim(ucwords(just_subj($headers->senderaddress))) . "\">" . trim(ucwords(just_subj($headers->senderaddress))) . "</a>";
		$mail["replyto"] = "<a href=\"./Mailbox/Scrivi#" . trim(ucwords(just_subj($headers->reply_toaddress))) . "\">" . trim(ucwords(just_subj($headers->reply_toaddress))) . "</a>";
			$mail["Recent"] = $headers->Recent;
			$mail["Unseen"] = $headers->Unseen;
			$mail["Flagged"] = $headers->Flagged;
			$mail["Answered"] = $headers->Answered;
			$mail["Deleted"] = $headers->Deleted;
			$mail["Draft"] = $headers->Draft;
	
	$mail["Msgno"] = trim($headers->Msgno);
	$mail["Size"] = $headers->Size;
	$mail["full_date"] = date("j/n/Y G:i", strtotime($headers->MailDate));
	if(trim($headers->Unseen) == "U"){
		$mail["date"] = "<div id=\"date" . $msg_id . "\" class=\"item\">" . str_replace(date("j/n/Y"), "", date("j/n/Y G:i", strtotime($headers->MailDate))) . "</div>";
		$mail["subject"] = "<div id=\"subject" . $msg_id . "\" class=\"item\">" . trim(mb_convert_encoding($headers->subject, "UTF-8", "QUOTED-PRINTABLE")) . "</div>";
		$mail["read"] = "<a href=\"javascript: void(0);\" onclick=\"read_unread('" . $msg_id . "');\" id=\"read_" . $msg_id . "\" class=\"read_marker\"><div>&bull;</div></a>";
	} else {
		$mail["date"] = "<div id=\"date" . $msg_id . "\" class=\"item read\">" . str_replace(date("j/n/Y"), "", date("j/n/Y G:i", strtotime($headers->MailDate))) . "</div>";
		$mail["subject"] = "<div id=\"subject" . $msg_id . "\" class=\"item read\">" . trim(mb_convert_encoding($headers->subject, "UTF-8", "QUOTED-PRINTABLE")) . "</div>";
		$mail["read"] = "<a href=\"javascript: void(0);\" onclick=\"read_unread('" . $msg_id . "');\" id=\"read_" . $msg_id . "\" class=\"read_marker read\"><div>&#9702;</div></a>";
	}
	$mail["fetchsubject"] = trim(mb_convert_encoding($headers->fetchsubject, "UTF-8", "QUOTED-PRINTABLE"));
	$mail["type"] = $struttura->type;
	$mail["subtype"] = $struttura->subtype;
	if($with_body){
		switch($struttura->subtype) {
			case "PLAIN":
				$text = imap_fetchbody($inbox, $msg_id, 1);
				break;
			case "MIXED":
			case "HTML":
				$text = imap_fetchbody($inbox, $msg_id, 1);
				break;
			default:
				$text = imap_fetchbody($inbox, $msg_id, 2);
				break;
		}
		if($struttura->encoding == 3) {
			$text = imap_base64($text);         
		} else {
			$text = imap_qprint($text);
		}
		$ignore = array("NextPart", "Content-Type", "charset=", "Content-Transfer");
		//print $text;
		if($struttura->subtype == "MIXED"){
			$text = utf8_decode(mb_convert_encoding(trim($text), "UTF-8", "ASCII"));
			$text = nl2br($text) . "<br />";
		} elseif($struttura->subtype == "ALTERNATIVE" || $struttura->subtype == "HTML"){
			$text = html_entity_decode(strip_tags(preg_replace("/\s{2,}/", "", $text), "<b><i><u><sup><sub><em><strong><a><br><ul><ol><li><p><blockquote>")) . "<br />";
			$text = nl2br(preg_replace("/(\r?\n){2,}/", "\n", $text));
			//print $text;
			$text = mb_convert_encoding(trim($text), "UTF-8", "ASCII");
		} else {
			$arrtext = explode("\r", $text);
			foreach($arrtext as $key => $val){
				foreach($ignore as $wrong) {
					if(eregi($wrong, $val)) {
						unset($arrtext[$key]);
					}
				}
				$cval = strip_tags(trim($val));
				if(empty($cval)) {
					unset($arrtext[$key]);
				}
			}
			$arrtext = array_unique($arrtext);
			//print_r($arrtext);
			$text = implode("\n", $arrtext);
			$text = mb_convert_encoding(trim($text), "UTF-8", "ASCII");
			$text = preg_replace("/\>\s(.*)\n/", "<blockquote>$1</blockquote>", $text);
			$text = nl2br(str_replace(array("\r\n", "\n\n", "<br>"), "\n", $text)) . "<br />";
			//print $text;
		}
		$text = preg_replace("/(il giorno .* ha scritto\:)((?:.|[\r\n])+)/i", '<div class="wrote">$1<blockquote>$2</blockquote></div>', $text);
		$text = preg_replace("/(--<br \/>)((?:.|[\r\n])+)/", '<div class="signature">$1$2</div>', $text);
		$mail["body"] = $text;
	}
	$attachments = array();
	$mail["attachment_icon"] = "";
	if(isset($struttura->parts) && count($struttura->parts)) {
		for($i = 0; $i < count($struttura->parts); $i++) {
			$attachments[$i] = array(
			      'is_attachment' => false,
			      'filename' => '',
			      'name' => '',
			      'attachment' => ''
			);
			if($struttura->parts[$i]->ifdparameters) {
				foreach($struttura->parts[$i]->dparameters as $object) {
					if(strtolower($object->attribute) == 'filename') {
						$attachments[$i]['is_attachment'] = true;
						$mail["attachment_icon"] = "<img src=\"common/media/img/attachment_16.png\" />";
						$attachments[$i]['filename'] = $object->value;
					}
				}
			}
			if($struttura->parts[$i]->ifparameters) {
				foreach($struttura->parts[$i]->parameters as $object) {
					if(strtolower($object->attribute) == 'name') {
						$attachments[$i]['is_attachment'] = true;
						$mail["attachment_icon"] = "<img src=\"common/media/img/attachment_16.png\" />";
						$attachments[$i]['name'] = $object->value;
					}
				}
			}
		}
	}
	$count_attachments = 0;
	foreach($attachments as $i => $att) {
		if($attachments[$i]["is_attachment"] == "1"){
			$count_attachments++;
		}
	}
	$mail["attachments_count"] = $count_attachments;
	$mail["attachments"] = $attachments;
	
	return $mail;
}
if(isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	require_once("../../mail.global_functions.php");
	require_once("../../../conf/mail_data.php");
	
	foreach($_GET as $k => $v){
		$_POST[$k] = $v;
	}
	$page = isset($_POST["page"]) ? $_POST["page"] : 1;
	$query = isset($_POST["query"]) ? $_POST["query"] : false;
	$qtype = isset($_POST["qtype"]) ? $_POST["qtype"] : false;

	$rp = isset($_POST['rp']) ? $_POST['rp'] : 15;
	$start = (($page-1) * $rp);
	
	if(isset($_GET["check_dir"]) && strlen(trim($_GET["check_dir"])) > 0){
		$mail_mailbox_name = $_GET["check_dir"];
	} else {
		$mail_mailbox_name = $GLOBALS["mail_mailbox_name"];
	}
	if($inbox = @imap_open("{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "/" . $GLOBALS["mail_flag"] . "}" . $mail_mailbox_name . "", $GLOBALS["user_mail_account"], $GLOBALS["user_mail_pssword"])){
		$mail["page"] = $page;
		$mail["total"] = imap_num_msg($inbox);
		$tb_count = $paging[$page]["start"]-1;
		$count_unseen = 0;
		if(isset($_GET["query"]) && trim($_GET["query"]) !== ""){
			switch($_GET["qtype"]){
				case "subject":
					$target = "SUBJECT";
					break;
				case "body":
					$target = "BODY";
					break;
				case "from":
					$target = "FROM";
					break;
				case "date":
					$target = "SINCE";
					break;
			}
			$search_results = imap_search($inbox, $target . " \"" . $_GET["query"] . "\"", SE_FREE, "UTF-8");
			if(is_array($search_results)){
				$mail["total_ids"][] = $search_results;
				$mail["unseen_count"] = 0;
				$i = 0;
				rsort($search_results);
				foreach($search_results as $ids){
					$i++;
					if($i == 1){
						$mail["rows"][$i] = mail_data($inbox, $ids, true);
					} else {
						$mail["rows"][$i] = mail_data($inbox, $ids);
					}
				}
			} else {
				$mail["total_ids"][] = 0;
				$mail["unseen_count"] = 0;
			}
		} else {
			if(isset($_GET["check_all"]) && trim($_GET["check_all"]) == "true") {
				$o  = 0;
				// Select all messages
				$total_pages = ceil($mail["total"]/$rp);
				for($p = 1; $p <= $total_pages; $p++){
					$paging[$p]["start"] = $mail["total"]-$rp*($p-1);
					$paging[$p]["stop"] = $paging[$p]["start"]-($rp-1);
					if($paging[$p]["stop"] < 0){
						$paging[$p]["stop"] = 1;
					}
				}
				if($mail["total"] == 0){
					$mail["total_ids"][] = 0;
					$mail["unseen_count"] = 0;
				} else {
					for($i = $paging[$page]["start"]; $i >= $paging[$page]["stop"]; $i--){
						$o++;
						$tb_count++;
						$mail["total_ids"][] = $i;
						$headers = imap_headerinfo($inbox, $i);
						if(trim($headers->Unseen) == "U"){
							$count_unseen++;
						}
						$mail["unseen_count"] = $count_unseen;
						if($o == 1){
							$mail["rows"][$i] = mail_data($inbox, $i, true);
						} else {
							$mail["rows"][$i] = mail_data($inbox, $i);
						}
					}
				}
			} else {
				// Select one message
				$tb_count++;
				$mail["total_ids"][] = $tb_count+1;
				if(!isset($_GET["msg_id"]) || trim($_GET["msg_id"]) == ""){
					$msg_id = $mail["total"];
				} else {
					$msg_id = (int)$_GET["msg_id"];
				}
				$mail["unseen_count"] = 0;
				$mail["rows"][$tb_count] = mail_data($inbox, $msg_id, true);
				// Flags to read
				$status = imap_clearflag_full($inbox, $msg_id, '\\Unseen');
				$status = imap_clearflag_full($inbox, $msg_id, '\\Flagged');
				$status = imap_setflag_full($inbox, $msg_id, "\\Seen");
			}
		}
		if($_GET["test"] == "true"){
			print_r($mail);
		} else {
			print json_encode($mail);
		}
		imap_close($inbox);
	} else {
		print "Non si hanno i privilegi necessari per poter accedere a questo indirizzo.";
	}
}
?>