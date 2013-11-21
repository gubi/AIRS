<?php
$pub_start_one = 0;
$pub_start_no = (count($foaf_publication) > 0) ? count($foaf_publication) + 1 : 1;
if(is_array($foaf_publication) && count($foaf_publication) > 0) {
	$pub_c = 0;
	foreach ($foaf_publication as $publication) {
		$pub_c++;
		if($pub_c > 1) {
			$pub_remove_btn = '&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo campo">-</a>';
		}
		$pub_rows .= '<tr class="user_publication"><td><input type="text" style="width: 25%;" name="user_publication[' . $pub_c . '][uri]" rel="publication" placeholder="Pubblicazione" title="Pubblicazione" value="' . str_replace("http://it.dbpedia.org/page/", "", urldecode($publication)) . '" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un pubblicazione">+</a>' . $pub_remove_btn . '</td></tr>';
	}
} else {
	$pub_rows = '<tr class="user_publication"><td><td><input type="text" style="width: 25%;" name="user_publication[' . $pub_start_no . '][uri]" class="publication" placeholder="Pubblicazione" title="Pubblicazione" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un pubblicazione">+</a></td></tr>';
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
	</form>
</div>
EOF;
?>