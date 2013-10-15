<?php
/**
* Generates form for adding user
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
* @package	AIRS_manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

$content_body = <<<Add_user_form
<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/skins/simple/style.css" />
<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/sets/wiki/style.css" />
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/markitup/jquery.markitup.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	myWikiSettings = {
		nameSpace: "wiki",
		previewParserPath: "~/templates/preview.php?page={PAGE}&user={DECRYPTED_USER}",
		previewAutoRefresh: true,
		markupSet:  [
			{name:'Grassetto', key:'B', openWith:"'''", closeWith:"'''", className:'bold'}, 
			{name:'Corsivo', key:'I', openWith:"''", closeWith:"''", className:'italic'}, 
			{name:'Sottolineato', key:'U', openWith:'__', closeWith:'__', className:'underline'}, 
			{name:'Barrato', key:'S', openWith:'@@--- ', closeWith:' @@', className:'strokethrough'}, 
			{separator:'---------------' },
			{name:'Titolo 1', key:'1', openWith:'= ', closeWith:' =', className:'h1'},
			{name:'Titolo 2', key:'2', openWith:'== ', closeWith:' ==', className:'h2'},
			{name:'Titolo 3', key:'3', openWith:'=== ', closeWith:' ===', className:'h3'},
			{name:'Titolo 4', key:'4', openWith:'==== ', closeWith:' ====', className:'h4'},
			{name:'Titolo 5', key:'5', openWith:'===== ', closeWith:' =====', className:'h5'},
			{separator:'---------------' }, 
			{name:'Elenco puntato', openWith:'(!(* |!|*)!)', className:'ul'}, 
			{name:'Elenco numerato', openWith:'(!(# |!|#)!)', className:'ol'}, 
			{separator:'---------------' },
			{name:'Crea tabella', className:'tablegenerator', placeholder:"Inserisci del testo qui",
				replaceWith:function(h) {
					var cols = prompt("Inserire il numero di colonne"),
						rows = prompt("Inserire il numero di righe"),
						html = "||";
					if (h.altKey) {
						for (var c = 0; c < cols; c++) {
							html += "! [![TH"+(c+1)+" text:]!]";
						}	
					}
					for (var r = 0; r < rows; r++) {
						if (r > 0){
							html += "||\\n||";
						}
						for (var c = 0; c < cols; c++) {
							if (c > 0){
								html += "||";
							}
							html += (h.placeholder||"");
						}
					}
					html += "||";
					return html;
				},
			className:'tablegenerator'},
			{name:'Immagine', key:'P', replaceWith:'[![Url:!:http://]!] [![name]!]', className:'image'},
			{name:'Collegamento libero', openWith:'(([![Collegamento:!:]!]|', closeWith:'))', placeHolder:'Testo del collegamento', className:'internal_link'},
			{name:'Collegamento InterWiki', openWith:'[[![Canale wiki:!:Wikipedia]!]:[![Lingua (sigla ISO_3166-1):!:it]!]:', closeWith:']', placeHolder:'Testo del collegamento', className:'interwiki_link'},
			{name:'Collegamento esterno', openWith:'[[![URI (Uniform Resource Locator):!:http://]!] ', closeWith:']', placeHolder:'Testo del collegamento', className:'external_link'},
			{separator:'---------------' },
			{name:'Citazione', openWith:'(!(> |!|>)!)', className:'quote'},
			{name:'Codice', openWith:'(!(<code type="[![Linguaggio:!:php]!]">\\n|!|<pre>)!)', closeWith:'(!(\\n</code>|!|</pre>)!)', className:'code'}, 
			{separator:'---------------' },
			{name:'Anteprima', call:'preview', className:'preview'}
		]
	};
	$('#user_communication').markItUp(myWikiSettings);
});
</script>
<div id="add_user">
	<form action="" method="post" id="add_user_form" onsubmit="return check_data(); return false;">
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<fieldset>
						<legend>Dati del nuovo utente</legend>
						<br />
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td rowspan="4" style="width: 150px; text-align: center;">
									<img src="common/media/img/user_profile_plus_128_ccc.png" />
								</td>
								<td>
									<input type="text" id="user_name" name="user_name" placeholder="Nome" style="width: 99%;" value="" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="user_lastname" name="user_lastname" placeholder="Cognome" style="width: 99%;" value="" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<input type="email" id="user_email" name="user_email" placeholder="Indirizzo e-mail" style="width: 99%;" value="" />
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend>Utente responsabile</legend>
						<br />
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="text" id="user" name="user" placeholder="Utente responsabile" style="width: 99%;" value="{DECRYPTED_USER}" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<textarea style="width: 98%; height: 150px;" name="user_communication" id="user_communication" placeholder="Eventuali comunicazioni"></textarea>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="save_user_btn" id="save_user_btn" value="Crea" />
				</td>
			</tr>
		</table>
	</form>
Add_user_form;

require_once("common/include/conf/replacing_object_data.php");
?>