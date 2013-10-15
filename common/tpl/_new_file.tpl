<?php
/**
* Generates template for create RDF of new file
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

if (!isset($_POST["file_name"])){
	$content_body .= <<<New_file_form
	<script type="text/javascript">
	$(document).ready(function(){
		$("#input_file_name").focus();
	});
	</script>
	<form method="post">
		<table cellpadding="10" cellspacing="10">
			<tr>
				<td style="width: 128px" style="width: 100%;">
					<img src="common/media/img/document_text_plus_128_ccc.png" />
				</td>
				<td valign="top">
					<h1>Modulo di inserimento di un nuovo file</h1>
					<br />
					<br />
					Inserendo nel campo a seguire il nome del file, si procederà con la generazione della sua pagina di riepilogo, assieme agli equivalenti dati rdf.<br />
					<br />
					<b><u>Nota:</u> l'estensione del file non &egrave; richiesta poich&eacute; verr&agrave; determinata automaticamente</b>
					<br />
					<br />
					<input type="text" name="file_name" id="input_file_name" value="" style="width: 75%;" />
				</td>
			</tr>
		</table>
	</form>

New_file_form;
} else {
	require_once("common/include/funcs/get_link.php");
	$file_name = "./File:" . get_link($_POST["file_name"]);
	redirect($file_name);
}
?>