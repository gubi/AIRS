<?php
/**
* Generates login page
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

$content_title = "Accedi";
$content_subtitle = "Accesso al sistema";
$content_body = <<<'LOGIN'
Per ragioni di sicurezza &egrave; stato disabilitato l'autocompletamento, pertanto il browser non richieder&agrave; di poter salvare i dati di accesso.<br />
In caso di smarrimento dei dati, sono disponibili i moduli di ripristino, grazie ai quali si potr&agrave; riprendere possesso del proprio username o resettare la propria password.<br />
<br />
<form action="" method="post">
	<fieldset>
		<legend>Dati per l'accesso</legend>
		<table cellspacing="15" cellpadding="15" class="frm">
			<tr>
				<td>
					<table cellspacing="5" cellpadding="5" class="frm">
						<tr>
							<th><label for="username">Username</label></th>
							<td>
								<input type="text" name="username" id="username" value="" autofocus="autofocus" required="required" />
								<input type="hidden" name="ref" id="ref" value="{REFERER_PAGE}" />
							</td>
						</tr>
						<tr>
							<th><label for="password">Password</label></th>
							<td>
								<input type="password" name="password" id="password" value="" required="required" />
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<th><img src="common/media/img/document_sans_security_64_ccc.png" /></th>
							<td>
								<input type="password" size="36" name="key" id="key" value="" placeholder="Chiave di cifratura" required="required" autocomplete="off" /><br/>
								<br />
								<a style="margin: 10px 0;" href="Sicurezza/Chiave_di_cifratura" target="_blank">Perch&eacute; la chiave di cifratura</a>
							</td>
						</tr>
						<tr>
							<td colspan="2">				
								<input type="submit" name="login_btn" value="Accedi" />
							</td>
						</tr>
					</table>
				</td>
				<td class="separator"></td>
				<td>
					<ul>
						<li><a href="./Sistema/Username_dimenticata">Username dimenticata</a></li>
						<li><a href="./Sistema/Password_dimenticata">Password dimenticata</a></li>
					</ul>
					<ul>
						<li><a href="./Registrami">Richiedi invito alla registrazione</a></li>
					</ul>
				</td>
			</tr>
		</table>
	</fieldset>
</form>
LOGIN;
$content_body = str_replace("{REFERER_PAGE}", $GLOBALS["referer_page"], utf8_decode($content_body));
?>
