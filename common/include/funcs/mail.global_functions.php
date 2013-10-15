<?php
/**
* Common Mailbox functions
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
function checkAttachments($estMsg){
	if (strlen($estMsg -> parts[1] -> dparameters[0] -> value) == 0){
		return false;
	} else {
		return true;
	}
}
function just_subj($strHead){
	if(ereg("=\?.{0,}\?[Bb]\?", $strHead)){
		$arrHead = split("=\?.{0,}\?[Bb]\?", $strHead);
		while(list($key, $value) = each($arrHead)){
			if(ereg("\?=", $value)){
				$arrTemp = split("\?=", $value);
				$arrTemp[0] = base64_decode($arrTemp[0]);
				$arrHead[$key] = join("",$arrTemp);
			}
		}
		$strHead = join("", $arrHead);
	}
	if(ereg("=\?.{0,}\?Q\?", $strHead)){
		$strHead = quoted_printable_decode($strHead);
		$strHead = ereg_replace("=\?.{0,}\?[Qq]\?", "", $strHead);
		$strHead = ereg_replace("\?=", "", $strHead);
		$strHead = utf8_encode($strHead);
	}
	$strHead = str_replace(" @ ", "@", $strHead);
	$strHead = str_replace("_", " ", $strHead);
	$strHead = str_replace("\"", "", $strHead);
	return $strHead;
}
function get_mime_type(&$structure) {
	$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
	if($structure->subtype) {
		 return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
	 }
	 return "TEXT/PLAIN";
}

function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) {
	if (!$structure) {
		$structure = imap_fetchstructure($stream, $msg_number);
	}
	if($structure) {
		if($mime_type == get_mime_type($structure)) {
			if(!$part_number) {
				$part_number = "1";
			}
			
			$text = imap_fetchbody($stream, $msg_number, $part_number);
			if($structure->encoding == 0) {
				return utf8_encode($text);
			} else if ($structure->encoding == 1) {
				return $text;
			} else if ($structure->encoding == 2) {
				return imap_binary($text);
			} else if ($structure->encoding == 3) {
				return imap_base64($text);
			} else if ($structure->encoding == 4) {
				return utf8_encode(imap_qprint($text));
			} else {
				return $text;
			}
		}
		if ($structure->type == 1) { /* multipart */
			while (list($index, $sub_structure) = each($structure->parts)) {
				if ($part_number) {
					$prefix = $part_number . '.';
				}
				$data = get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
				if ($data) {
					return $data;
				}
			}
		}
	}
	return false;
}
function create_part_array($structure, $prefix="") {
	//print_r($structure);
	if (sizeof($structure->parts) > 0) {	// There some sub parts
		foreach ($structure->parts as $count => $part) {
			add_part_to_array($part, $prefix.($count+1), $part_array);
		}
	}else{	// Email does not have a seperate mime attachment for text
		$part_array[] = array('part_number' => $prefix.'1', 'part_object' => $obj);
	}
   return $part_array;
}
// Sub function for create_part_array(). Only called by create_part_array() and itself.
function add_part_to_array($obj, $partno, & $part_array) {
	$part_array[] = array('part_number' => $partno, 'part_object' => $obj);
	if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
		//print_r($obj);
		if (sizeof($obj->parts) > 0) {	// Check to see if the email has parts
			foreach ($obj->parts as $count => $part) {
				// Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
				if (sizeof($part->parts) > 0) {
					foreach ($part->parts as $count2 => $part2) {
						add_part_to_array($part2, $partno.".".($count2+1), $part_array);
					}
				}else{	// Attached email does not have a seperate mime attachment for text
					$part_array[] = array('part_number' => $partno.'.'.($count+1), 'part_object' => $obj);
				}
			}
		}else{	// Not sure if this is possible
			$part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
		}
	} else{	// If there are more sub-parts, expand them out.
		if (sizeof($obj->parts) > 0) {
			foreach ($obj->parts as $count => $p) {
				add_part_to_array($p, $partno.".".($count+1), $part_array);
			}
		}
	}
}
?>