<?php
$knows_start_one = 1;
$knows_start_no = (count($foaf_knows) > 0) ? count($foaf_knows) + 0 : 0;
if(is_array($foaf_knows) && count($foaf_knows) > 0) {
	$kno_c = -1;
	foreach ($foaf_knows as $knows) {
		$kno_c++;
		if($kno_c > 0) {
			$kno_remove_btn = '&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo campo">-</a>';
		}
		$knows_rows .= '<tr class="user_knows"><td><input type="text" style="width: 25%;" name="user_knows[' . $kno_c . '][name]" rel="knows" placeholder="Nome del conoscente" title="Nome del conoscente" value="' . $knows->get("foaf:name") . '" />&emsp;<input type="text" style="width: 25%;" name="user_knows[' . $kno_c . '][email]" placeholder="Indirizzo e-mail..." title="Indirizzo e-mail del conoscente" value="' . str_replace("mailto:", "", $knows->get("foaf:mbox")) . '" />&emsp;<input type="text" style="width: 25%;" name="user_knows[' . $kno_c . '][uri]" placeholder="Indirizzo del file FoaF..." title="Indirizzo del file FoaF del conoscente" value="' . $knows->get("rdfs:seeAlso") . '" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un conoscente">+</a>' . $kno_remove_btn . '</td></tr>';
	}
} else {
	$knows_rows = '<tr class="user_knows"><td><input type="text" style="width: 25%;" name="user_knows[' . $knows_start_no . '][name]" class="knows" placeholder="Nome del conoscente" title="Nome del conoscente" value="" />&emsp;&emsp;<input type="text" style="width: 25%;" name="user_knows[' . $knows_start_no . '][email]" placeholder="Indirizzo e-mail..." title="Indirizzo e-mail del conoscente" value="" /><input type="text" style="width: 25%;" name="user_knows[' . $knows_start_no . '][uri]" placeholder="Indirizzo del file FoaF..." title="Indirizzo del file FoaF del conoscente" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un conoscente">+</a></td></tr>';
}
$knows_data_div = <<<EOF
<div id="knows_data_div" style="display: none;">
	<input type="hidden" id="knows_no" value="1" />
	<table cellpadding="5" cellspacing="5" style="width: 100%;" id="user_knows">
		$knows_rows
	</table>
</div>
EOF;
?>