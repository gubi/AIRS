<?php
/**
* Generates tags from text
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
* @SLM_status	testing
*/
require_once("translate.php");
function remove_last_short_words($tag){
	$the_tag_arr = explode(" ", $tag);
	if (strlen($the_tag_arr[count($the_tag_arr) - 1]) < 4){
		$tags_arr = array_pop($the_tag_arr);
		remove_last_short_words(implode(" ", $the_tag_arr));
	} else {
		$the_all_tag = implode(" ", $the_tag_arr);
		if ($the_all_tag !== null){
			return $the_all_tag;
		}
	}
}
function calculate_tags($text, $translate = false){
	if(strlen($text) > 0){
		$wordcounts = array();
		$text = str_replace(array(" di ", " a ", " da ", " in ", " con ", " su ", " per ", " tra ", " fra ", " il ", " lo ", " la ", " i ", " gli ", " le ", " un ", " uno ", " una ", "\t"),
						array("_di_", "_a_", "_da_", "_in_", "_con_", "_su_", "_per_", "_tra_", "_fra_", "_il_", "_lo_", "_la_", "_i_", "_gli_", "_le_", "_un_", "_uno_", "_una_", " "), $text);
		$words = explode(" ", $text);
		foreach($words as $word){
			// ---------------------------------------------------------------------------------------------------- Pulizia dei tags
			// Aggiusta gli apostrofi
			$word = str_replace(array("`", "’"), "'", $word);
			// Toglie i verbi infiniti
			$no_verbs = array("are", "ere", "ire");
			if (in_array(substr($word, -3), $no_verbs)){
				$word = "";
			}
			// Toglie gli spazi e le tabulazioni 
			$word = str_replace(array("\t+", "\s+", "\S+"), "", trim($word));
			// Toglie gli url e mantiene solo le parole
			$word = preg_replace(
						array(
							'`((?:https?|ftp)://\S+[[:alnum:]]/?)`si',
							'`((?<!//)(www\.\S+[[:alnum:]]/?))`si',
							'`[^a-zA-ZÀÈÉÌÒÙàèéìòù\'\_]`',
							'`\w+\'_`si'
						),
						array(
							""
						),
						$word);
			// Maiuscola la prima lettera del tag
			$word = ucfirst($word);
			if (strlen($word) > 3){
				if (!array_key_exists($word, $wordcounts)){
					$wordcounts[$word] = 0;
				}
				$wordcounts[$word] += 1;
			}
		}
		$min = 1000000;
		$max = -1000000;
		foreach(array_keys($wordcounts) as $word){
			if ($wordcounts[$word] > $max){
				$max = $wordcounts[$word];
			}
			if ($wordcounts[$word] < $min){
				$min = $wordcounts[$word];
			}
		}
		if (($max - $min) != 0){
			$ratio = 18.0 / ($max - $min);
		}
		$wc = array_keys($wordcounts);
		ksort($wc);
		$c = 0;
		foreach($wc as $word){
			$c ++;
			$fs = round($c + ($wordcounts[$word]*$ratio), 2);
				// Sostituisce gli aggenti errati
				$word = str_replace(array("a'",
									" e' ",
									"che'",
									"i'",
									"o'",
									"u'",
									"A'",
									" E' ",
									"CHE'",
									"I'",
									"O'",
									"U'"
								),
								array("à",
									" è ",
									"ché",
									"ì",
									"ò",
									"ù",
									"À",
									" È ",
									"CHÉ",
									"Ì",
									"Ò",
									"Ù"
								), $word);
			$tags[$fs] = $word;
		}
		
		if(is_array($tags)){
			// Traduce i tags in italiano
			foreach($tags as $tag){
				$tag = str_replace("_", " ", $tag);
				if($translate == true){
					$translated_tag = translate($tag);
					$alltags[] = $translated_tag;
				} else {
					$alltags[] = $tag;
				}
			}
			foreach($alltags as $the_tag){
				//$all_tags[] = remove_last_short_words($the_tag);
				$all_tags[] = trim($the_tag);
			}
		}
		
		return $all_tags;
	}
}
?>