<?php
$account_start_one = 1;
$account_start_no = (count($foaf_account) > 0) ? count($foaf_account) + 1 : 1;
if(is_array($foaf_account) && count($foaf_account) > 0) {
	$acc_c = -1;
	foreach ($foaf_account as $accounts) {
		$acc_c++;
		if($acc_c > 1) {
			$acc_remove_btn = '&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo campo">-</a>';
		}
		$account_rows .= '<tr class="user_account"><td><table cellpadding="5" cellspacing="5" style="width: 100%;"><tr><td><input type="text" style="width: 25%;" name="user_account[' . $acc_c . '][type]" rel="account" placeholder="Nome del servizio..." title="Nome del servizio (ad es. Facebook, Twitter, ecc...)" value="' . $accounts->get("foaf:name") . '" />&emsp;<input type="text" style="width: 25%;" name="user_account[' . $acc_c . '][uri]" placeholder="Pagina del proprio account..." title="Indirizzo della pagina del proprio account" value="' . $accounts->get("foaf:accountServiceHomepage") . '" />&emsp;<input type="text" name="user_account[' . $acc_c . '][name]" placeholder="Nome account..." title="Nome dell\'account" value="' . $accounts->get("foaf:accountName") . '" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un altro account">+</a>' . $acc_remove_btn . '</td></tr></table></td></tr>';
	}
} else {
	$account_rows = '<tr class="user_account"><td><table cellpadding="5" cellspacing="5" style="width: 100%;"><tr><td><input type="text" style="width: 25%;" name="user_account[$account_start_no][type]" class="account" placeholder="Nome del servizio..." title="Nome del servizio (ad es. Facebook, Twitter, ecc...)" value="" />&emsp;<input type="text" style="width: 25%;" name="user_account[$account_start_no][uri]" class="account" placeholder="Pagina del proprio account..." title="Indirizzo della pagina del proprio account" value="" />&emsp;<input type="text" name="user_account[$account_start_no][name]" class="account" placeholder="Nome account..." title="Nome dell\'account" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un altro account">+</a></td></tr></table></td></tr>';
}
$accounts_data_div = <<<EOF
<div id="accounts_data_div" style="display: none;">
	<table cellpadding="10" cellspacing="10" style="font-size: 1.5em;">
		<tr>
			<th><label for="user_nickname">Nickname comune:</label></th>
			<td>
				<input type="text"  style="font-size: 1.2em;" id="user_nickname" name="user_nickname" placeholder="Inserire il nickname pi&ugrave; usato" value="$user_common_nickname" />
			</td>
		</tr>
	</table>
	<br />
	<fieldset>
		<legend>Messaggistica istantanea</legend>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 30%;">
					<table cellpadding="5" cellspacing="5" style="width: 100%;">
						<tr>
							<th><label for="user_openid_account"><img src="common/media/img/openid_logo.png" style="vertical-align: -3px;" /> OpenID</label></th>
							<td>
								<input type="text" id="user_openid_account" name="user_openid_account" placeholder="Account OpenID" style="width: 150px;" value="$openid_account" />
							</td>
						</tr>
						<tr>
							<th><label for="user_skype_account"><img src="common/media/img/skype_logo.png" style="vertical-align: -5px;" /> Skype:</label></th>
							<td>
								<input type="text" id="user_skype_account" name="user_skype_account" placeholder="Account Skype" style="width: 150px;" value="$skype_account" />
							</td>
						</tr>
						<tr>
							<th><label for="user_msn_account"><img src="common/media/img/msn_logo.png" style="vertical-align: -5px;" /> MSN:</label></th>
							<td>
								<input type="text" id="user_msn_account" name="user_msn_account" placeholder="Account MSN" style="width: 150px;" value="$msn_account" />
							</td>
						</tr>
						<tr>
							<th><label for="user_yahoo_account"><img src="common/media/img/yahoo_logo.png" style="vertical-align: -5px;" /> Yahoo:</label></th>
							<td>
								<input type="text" id="user_yahoo_account" name="user_yahoo_account" placeholder="Account Yahoo" style="width: 150px;" value="$yahoo_account" />
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" style="width: 50%;">
					<table cellpadding="5" cellspacing="5" style="width: 100%;">
						<tr>
							<th><label for="user_aim_account"><label for="user_aim_account"><img src="common/media/img/aim_logo.png" style="vertical-align: -5px;" /> AIM:</label></th>
							<td>
								<input type="text" id="user_aim_account" name="user_aim_account" placeholder="Account AIM" style="width: 150px;" value="$aim_account" />
							</td>
						</tr>
						<tr>
							<th><label for="user_jabber_account"><label for="user_jabber_account"><img src="common/media/img/jabber_logo.png" style="vertical-align: -5px;" /> Jabber:</label></th>
							<td>
								<input type="text" id="user_jabber_account" name="user_jabber_account" placeholder="Account Jabber" style="width: 150px;" value="$jabber_account" />
							</td>
						</tr>
						<tr>
							<th><label for="user_icq_account"><img src="common/media/img/icq_logo.png" style="vertical-align: -5px;" /> ICQ:</label></th>
							<td>
								<input type="text" id="user_icq_account" name="user_icq_account" placeholder="Account ICQ" style="width: 150px;" value="$icq_account" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</fieldset>
	<br />
	<fieldset>
		<legend>Altri account</legend>
		<table cellpadding="5" cellspacing="5" style="width: 100%;" id="user_account">
			<tr>
				<td>
					<table cellpadding="5" cellspacing="5" style="width: 100%;">
						<tr class="user_account">
							<td>
								<input type="text" style="width: 25%;" name="user_account_type_$account_start_one" class="account" placeholder="Nome del servizio (ad es. Facebook, Twitter, ecc...)" value="AIRS - Automatic Intelligent Research System" disabled="disabled" />&emsp;<input type="text" style="width: 25%;" name="user_account_uri_$account_start_one" class="account" placeholder="Indirizzo della pagina del proprio account" value="https:/airs.inran.it/Utente/$username" disabled="disabled" />&emsp;<input type="text" name="user_account_name_$account_start_one" class="account" placeholder="Nome dell'account" value="$username" disabled="disabled" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			$account_rows
		</table>
	</fieldset>
</div>
EOF;
?>