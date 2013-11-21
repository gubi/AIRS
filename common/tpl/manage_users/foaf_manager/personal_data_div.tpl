<?php
if(file_exists($_SERVER["DOCUMENT_ROOT"] . ucfirst($i18n["user_string"]) . "/" . ucfirst($decrypted_user) . "/pub_key.asc")) {
	$pub_key_asc = str_replace("\n", "&#13;&#10;", file_get_contents($_SERVER["DOCUMENT_ROOT"] . ucfirst($i18n["user_string"]) . "/" . ucfirst($decrypted_user) . "/pub_key.asc"));
} else {
	$pub_key_asc = "";
}
$personal_data_div = <<<EOF
<script language="Javascript" src="common/js/GnuPG/sha1.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/base64.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/PGpubkey.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#pgp_pubkey").on('keyup change', function(e){
			getkey();
		});
		getkey();
	});
</script>
<div id="personal_data_div">
	<table cellpadding="5" cellspacing="5" style="width: 100%;">
		<tr>
			<td valign="top" align="center" style="width: 150px;">
				<b>Immagine personale</b><br />
				<br />
				<div id="img_uploader">
					<input type="hidden" name="foaf_thumb" id="foaf_thumb" value="$personal_image" />
					$profile_thumb
					<div id="file_status"></div>
				</div>
				<br />
				<p style="font-style: italic; font-size: 11px;">Fare click sull'immagine per caricarne un'altra</p>
			</td>
			<td class="separator">&nbsp;</td>
			<td>
				<label for="personal_bio" style="font-weight: bold;">Biografia o descrizione sul proprio conto</label><br />
				<br />
				<textarea id="personal_bio" name="personal_bio" style="height: 150px;">$personal_description</textarea>
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<th align="right"><label for="user_birthdate">Data di nascita:</label></th>
			<td class="separator" rowspan="5">&nbsp;</td>
			<td>
				<input type="datetime" id="user_birthdate" name="user_birthdate" class="datepicker" maxlength="10" style="width: 75px;" placeholder="Data di nascita" value="$user_birthdate" />
			</td>
		</tr>
		<tr>
			<th align="right">Sesso:</th>
			<td>
				<label>
					<input type="radio" name="user_sex" value="m" $gender_m /> Maschio
				</label>
				<br />
				<label>
					<input type="radio" name="user_sex" value="f" $gender_f /> Femmina
				</label>
				<br />
				<label>
					<input type="radio" name="user_sex" value="na" $gender_na /> <i>Non dichiarato</i>
				</label>
			</td>
		</tr>
		<tr>
			<th align="right"><label for="user_phone">Contatto telefonico:</label></th>
			<td>
				<input type="tel" id="user_phone" name="user_phone" placeholder="Numero di telefono" value="$user_phone" />
			</td>
		</tr>
		<tr>
			<th align="right"><label for="user_homepage">Sito personale:</label></th>
			<td>
				<input type="url" style="width: 75%" id="user_homepage" name="user_homepage" placeholder="Indirizzo web del sito personale" value="$user_site" />
			</td>
		</tr>
		<tr>
			<th align="right"><label for="user_blog">Blog personale:</label></th>
			<td>
				<input type="url" style="width: 75%" id="user_blog" name="user_blog" placeholder="Indirizzo web del blog personale" value="$user_blog" />
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<th align="right"><label for="pgp_pubkey">Chiave pubblica PGP</label></th>
			<td class="separator" rowspan="3">&nbsp;</td>
			<td>
				<textarea name="pgp_pubkey" id="pgp_pubkey" rows="5" style="width: 50%; height: 150px;">$pub_key_asc</textarea>
			</td>
		</tr>
		<tr>
			<th><label for="pgp_fingerprint">Fingerprint</label></th>
			<td>
				<input type="text" size="34" maxlength="49" name="pgp_fingerprint" id="pgp_fingerprint" value="" />
			</td>
		</tr>
	</table>
</div>
EOF;
?>