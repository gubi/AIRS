<?php
$int_start_one = 1;
$int_start_no = (count($foaf_interest) > 0) ? count($foaf_interest) + 1 : 1;
if(is_array($foaf_interest) && count($foaf_interest) > 0) {
	$int_c = -1;
	foreach ($foaf_interest as $interest) {
		$int_c++;
		if($int_c > 0) {
			$int_remove_btn = '&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo campo">-</a>';
		}
		$int_rows .= '<tr class="user_interest"><td><input type="text" style="width: 25%;" name="user_interest[' . $int_c . '][type]" rel="interest" placeholder="Interesse" title="Interesse" value="' . str_replace(array("http://it.dbpedia.org/page/", "_"), array("", " "), urldecode($interest)) . '" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un interesse">+</a>' . $int_remove_btn . '</td></tr>';
	}
} else {
	$int_rows = '<tr class="user_interest"><td><td><input type="text" style="width: 25%;" name="user_interest[$int_start_no][type]" class="interest" placeholder="Interesse" title="Interesse" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un interesse">+</a></td></tr>';
}

$interests_data_div = <<<EOF
<div id="interests_data_div" style="display: none;">
	<input type="hidden" id="interest_no" value="1" />
	<table cellpadding="5" cellspacing="5" style="width: 100%;" id="user_interest">
		<tr class="user_interests">
			$int_rows
		</tr>
	</table>
</div>
EOF;
?>