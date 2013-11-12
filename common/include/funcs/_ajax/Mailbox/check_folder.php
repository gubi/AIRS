<?php
/**
* Return Mailbox folder tree
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

function explodeTree($array, $delimiter = "_", $baseval = false) {
	if(!is_array($array)) return false;
	$splitRE = "/" . preg_quote($delimiter, "/") . "/";
	$returnArr = array();
	foreach ($array as $key => $val) {
		// Get parent parts and the current leaf
		$parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
		$leafPart = array_pop($parts);
		
		// Build parent structure 
		// Might be slow for really deep and large structures
		$parentArr = &$returnArr;
		foreach ($parts as $part) {
			if (!isset($parentArr[$part])) {
				$parentArr[$part] = array();
			} elseif (!is_array($parentArr[$part])) {
				if ($baseval) {
					$parentArr[$part] = array("__base_val" => $parentArr[$part]);
				} else {
					$parentArr[$part] = array();
				}
			}
			$parentArr = &$parentArr[$part];
		}
		// Add the final part to the structure
		if (empty($parentArr[$leafPart])) {
			//$parentArr[$leafPart] = $val;
			$parentArr[$leafPart] = $val;
		} elseif ($baseval && is_array($parentArr[$leafPart])) {
			$parentArr[$leafPart]["__base_val"] = $val;
		}
	}
	return $returnArr;
}
function returnArr($key, $value){
	if(is_array($value)){
		foreach($value as $k => $val){
			if($k == $key){
				return $val;
			} else {
				return "INBOX." . $key;
			}
		}
	} else {
		return $value;
	}
}
function cleanTree($arr){
	$start = -1;
	foreach($arr as $k => $v){
		$start++;
		if($k == "__base_val") continue;
		$show_val = (is_array($v) ? $v["__base_val"] : $v);
		
		switch(trim($k)){
			case "INBOX":
				$class = "inbox";
				$name = "Posta in arrivo";
				$text = "<a href=\"javascript:void(0);\" class=\"context-menu\" id=\"inbox_menu\" rel=\"INBOX\" name=\"INBOX\"  onclick=\"show('INBOX', 'inbox_menu');\">" . $name . " <span class=\"count\"></span></a>";
				break;
			case "Drafts":
				$class = "drafts";
				$name = "Bozze";
				$text = "<a href=\"javascript:void(0);\" class=\"context-menu\" id=\"drafts_menu\" rel=\"" . returnArr($k, $v) . "\" name=\"" . $k . "\" onclick=\"show('INBOX.Drafts', 'drafts_menu');\">" . $name . " <span class=\"count\"></span></a>";
				break;
			case "Sent":
				$class = "outbox";
				$name = "Posta inviata";
				$text = "<a href=\"javascript:void(0);\" class=\"context-menu\" id=\"outbox_menu\" rel=\"" . returnArr($k, $v) . "\" name=\"" . $k . "\" onclick=\"show('INBOX.Sent', 'outbox_menu');\">" . $name . " <span class=\"count\"></span></a>";
				break;
			case "Spam":
				$class = "spam";
				$name = "Spam";
				$text = "<a href=\"javascript:void(0);\" class=\"context-menu-junk\" id=\"spam_menu\" rel=\"" . returnArr($k, $v) . "\" name=\"" . $k . "\" onclick=\"show('INBOX.Spam', 'spam_menu');\">" . $name . " <span class=\"count\"></span></a>";
				break;
			case "Junk":
				$class = "spam";
				$name = "Indesiderata";
				$text = "<a href=\"javascript:void(0);\" class=\"context-menu-junk\" id=\"junk_menu\" rel=\"" . returnArr($k, $v) . "\" name=\"" . $k . "\" onclick=\"show('INBOX.Junk', 'junk_menu');\">" . $name . " <span class=\"count\"></span></a>";
				break;
			case "Trash":
				$class = "trash";
				$name = "Cestino";
				$text = "<a href=\"javascript:void(0);\" class=\"context-menu-trash\" id=\"trash_menu\" rel=\"" . returnArr($k, $v) . "\" name=\"" . $k . "\" onclick=\"show('INBOX.Trash', 'trash_menu');\">" . $name . " <span class=\"count\"></span></a>";
				break;
			default:
				$class = "folder";
				$name = $k;
				$text = "<a href=\"javascript:void(0);\" class=\"context-menu-folder\" id=\"" . $k . "\" rel=\"" . returnArr($k, $v) . "\" name=\"" . $k . "\"  onclick=\"show('" . returnArr($k, $v) . "', '" . $k . "');\">" . $k . " <span class=\"count\"></span></a>";
				break;
		}
		if(is_array($v)){
			$cleaned[$start]["text"] = $text;
			$cleaned[$start]["name"] = $name;
			$cleaned[$start]["classes"] = $class;
			$cleaned[$start]["children"] = cleanTree($v);
		} else {
			$cleaned[$start]["text"] = $text;
			$cleaned[$start]["name"] = $name;
			$cleaned[$start]["classes"] = $class;
		}
	}
	sort($cleaned);
	return $cleaned;
}
function get_mailboxes($inbox, $folder = ""){
	$list = imap_getmailboxes($inbox, "{" . $GLOBALS["mail_host"] . "}" . $folder, "*");
	
	if (is_array($list)) {
		sort($list);
		foreach ($list as $key => $val) {
			$folder_name = str_replace("{" . $GLOBALS["mail_host"] . "}", "", imap_utf7_decode($val->name));
			$folders[str_replace(".", "/", $folder_name)] = $folder_name;
		}
		return $folders;
	} else {
		return "imap_getmailboxes failed: " . imap_last_error() . "\n";
	}
}
if(isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	require_once("../../../conf/mail_data.php");
	
	if($inbox = @imap_open("{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "/" . $GLOBALS["mail_flag"] . "}" . $GLOBALS["mail_mailbox_name"] . "", $GLOBALS["user_mail_account"], $GLOBALS["user_mail_pssword"])){
		if($_GET["test"] == "true"){
			print_r(explodeTree(get_mailboxes($inbox, ""), "/"));
			print_r(cleanTree(explodeTree(get_mailboxes($inbox, ""), "/")));
			//print json_encode(cleanTree(explodeTree(get_mailboxes($inbox, ""), "/")));
		} else {
			print json_encode(cleanTree(explodeTree(get_mailboxes($inbox, ""), "/")));
		}
	}
	imap_close($inbox);
}
?>