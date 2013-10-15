<?php
/**
* Generates form for add new search engine
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
* @package	AIRS_AIR
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

if (strlen($se_quoting_var) == 0){
	$se_quoting_var = "&quot;";
}
if (strlen($se_not_var) == 0){
	$se_not_var = "&quot;";
}
if (strlen($se_or_var) == 0){
	$se_or_var = "&quot;";
}
$content_body = <<<SE_form
$save_se_script
<div id="add_SE_form">
	<form action="" method="post" id="add_search_engine" onsubmit="return save_se(); return false;">
		<input type="hidden" name="decrypted_user" value="{DECRYPTED_USER}" />
		<input type="hidden" id="se_id" name="se_id" value="$se_id" />
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<fieldset>
						<legend class="edit">Motore di ricerca</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="url" id="se_uri" name="se_uri" placeholder="Indirizzo del Motore di Ricerca" style="width: 99%;" value="$se_uri" /><br />
									<input type="hidden" id="se_old_uri" name="se_old_uri" />
									<a id="check_uri" href="javascript: void(0);" onclick="check_uri()">Scansiona indirizzo</a>
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<input type="text" id="se_title" name="se_title" placeholder="Titolo del Motore di Ricerca" style="width: 99%;" value="$se_title" />
								</td>
							</tr>
							<tr>
								<td>
									<textarea style="width: 99%;" name="se_description" id="se_description" placeholder="Descrizione del Motore di Ricerca">$se_description</textarea>
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<table cellspacing="0" cellpadding="0" style="width: 100%;">
						<tr>
							<td style="width: 50%; padding-right: 5px;" valign="top">
								<fieldset>
									<legend class="query">Variabili nella query di ricerca (<acronym title="Uniform Resource Identifier">URI</acronym>)</legend>
									<table cellspacing="5" cellpadding="5" style="width: 100%;">
										<tr>
											<td style="width: 45%;" >
												<label for="se_search_var">Variabile per le ricerche:</label>
											</td>
											<td>
												<input type="text" id="se_search_var" name="se_search_var" value="$se_search_var" />
											</td>
										</tr>
										<tr>
											<td style="width: 45%;" >
												<label for="se_lang_var">Variabile per la lingua:</label>
											</td>
											<td>
												<input type="text" id="se_lang_var" name="se_lang_var" value="$se_lang_var" />
											</td>
										</tr>
										<tr><td colspan="2">&nbsp;</td></tr>
										<tr>
											<td style="width: 45%;" >
												<label for="se_site_var">Variabile per il filtro su un dominio:</label>
											</td>
											<td>
												<input type="text" id="se_site_var" name="se_site_var" value="$se_site_var" />
											</td>
										</tr>
										<tr>
											<td style="width: 45%;" >
												<label for="se_filetype_var">Variabile per il filtro su un formato di documento:</label>
											</td>
											<td>
												<input type="text" id="se_filetype_var" name="se_filetype_var" value="$se_filetype_var" />
											</td>
										</tr>
										<tr>
											<td style="width: 45%;" >
												<label for="se_country_var">Variabile per il filtro su una ricerca geografica:</label>
											</td>
											<td>
												<input type="text" id="se_country_var" name="se_country_var" value="$se_country_var" />
											</td>
										</tr>
										<tr><td colspan="2">&nbsp;</td></tr>
										<tr>
											<td style="width: 45%;" >
												<label for="se_lastdate_var">Variabile per il filtro temporale:</label>
											</td>
											<td>
												<input type="text" id="se_lastdate_var" name="se_lastdate_var" value="$se_lastdate_var" />
											</td>
										</tr>
										<tr>
											<td style="width: 45%;" >
												<label for="se_lastdate_val">Valore per il filtro temporale:</label>
											</td>
											<td>
												<input type="text" id="se_lastdate_val" name="se_lastdate_val" value="$se_lastdate_val" />
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
							<td valign="top" style="padding-left: 5px;">
								<fieldset>
									<legend class="form">Variabili nel campo di ricerca</legend>
									<table cellspacing="5" cellpadding="0" style="width: 100%;">
										<tr>
											<td>
												<label for="se_quoting_var">Variabile per il filtro su una citazione:</label>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" id="se_quoting_var" name="se_quoting_var" value="$se_quoting_var" />
											</td>
										</tr>
										<tr><td></td></tr>
										<tr>
											<td>
												<label for="se_ADD_var">Variabile per l'aggiunta di parole:</label>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" id="se_ADD_var" name="se_NOT_var" value="+" />
											</td>
										</tr>
										<tr><td></td></tr>
										<tr>
											<td>
												<label for="se_NOT_var">Variabile per l'esclusione di parole:</label>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" id="se_NOT_var" name="se_NOT_var" value="$se_not_var" />
											</td>
										</tr>
										<tr><td></td></tr>
										<tr>
											<td>
												<label for="se_AND_var">Variabile per una ricerca ristretta di parole:</label>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" id="se_AND_var" name="se_AND_var" value="." />
											</td>
										</tr>
										<tr><td></td></tr>
										<tr>
											<td>
												<label for="se_OR_var">Variabile per una <a href="http://it.wikipedia.org/wiki/Ricerca_operativa" target="_blank">ricerca operativa tra parole (OR)</a>:</label>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" id="se_OR_var" name="se_OR_var" value="$se_or_var" />
											</td>
										</tr>
									</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="save_feed_btn" id="save_feed_btn" value="Salva" />
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="contenuto"></div>
SE_form;
?>