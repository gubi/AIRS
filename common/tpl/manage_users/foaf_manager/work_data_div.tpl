<?php
$work_data_div = <<<EOF
<div id="work_data_div" style="display: none;">
	<table cellpadding="5" cellspacing="5" style="width: 100%;">
		<tr>
			<td><label for="user_workplace"><b>Indirizzo web dell'Azienda/Ente:</b></label></td>
			<td style="width: 75%;">
				<input type="uri" style="width: 75%;" id="user_workplace" name="user_workplace" placeholder="Indirizzo web dell'Azienda/Ente di afferenza" value="$user_workplace" />
			</td>
		</tr>
		<tr>
			<td><label for="user_workplace_info"><b>Pagina di descrizione dell'Azienda/Ente:</b></label></td>
			<td style="width: 75%;">
				<input type="uri" style="width: 75%;" id="user_workplace_info" name="user_workplace_info" placeholder="Pagina di descrizione dell'Azienda/Ente di afferenza" value="$user_workplace_info" />
			</td>
		</tr>
	</table>
</div>
EOF;
?>