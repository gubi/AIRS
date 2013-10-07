<?php
/**
* Generates AIRS search engine page
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

$content_subtitle = "Cerca tra i contenuti del sito";
$content_last_edit = "";
$content_body = <<<Search_page
<table cellspacing="10" cellpadding="10" id="big_search_container">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/search_128_ccc.png" />
		</td>
		<td style="font-size: 1.1em;" id="big_search">
			<form id="search_form" action="" method="post" name="search">
				<input type="search" name="cerca" id="inputfield" accesskey="s" placeholder="Cerca nel sito..." />
				<input type="submit" style="display: none;" value="Cerca" />
			</form>
		</td>
	</tr>
</table>
<br />
<br />
<a href="Speciale:Accedi">Accedi</a> per effettuare ricerche avanzate
Search_page;
require_once("common/include/conf/replacing_object_data.php");
?>