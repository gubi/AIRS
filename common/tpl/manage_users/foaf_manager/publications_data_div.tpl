<?php
$pub_start_one = 1;
$pub_start_no = (count($foaf_publication) > 0) ? count($foaf_publication) + 1 : 1;
if(is_array($foaf_publication) && count($foaf_publication) > 0) {
	$pub_c = 1;
	foreach ($foaf_publication as $publication) {
		$pub_c++;
		if($pub_c > 2) {
			$pub_remove_btn = '&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo campo">-</a>';
		}
		$pub_rows .= '<tr class="user_publication"><td><input type="text" style="width: 25%;" name="user_publication_type_' . $pub_c . '" rel="publication" placeholder="pubblicazione" title="pubblicazione" value="' . str_replace(array("http://it.dbpedia.org/page/", "_"), array("", " "), urldecode($publication)) . '" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un pubblicazione">+</a>' . $pub_remove_btn . '</td></tr>';
	}
} else {
	$pub_rows = '<tr class="user_publication"><td><td><input type="text" style="width: 25%;" name="user_publication_type_$pub_start_no" class="publication" placeholder="pubblicazione" title="pubblicazione" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un pubblicazione">+</a></td></tr>';
}

$publications_data_div = <<<EOF
<div id="publications_data_div" style="display: none;">
	<form action="" method="post">
		<input type="hidden" id="publication_no" value="1" />
		<table cellpadding="5" cellspacing="5" style="width: 100%;" id="user_publication">
			<tr class="user_publications">
				$pub_rows
			</tr>
		</table>
		<table cellpadding="5" cellspacing="5" style="width: 100%;">
			<tr>
				<td colspan="5"><hr /><input name="account_data_btn" type="submit" value="Salva" /></td>
			</tr>
		</table>
	</form>
</div>
EOF;
?>