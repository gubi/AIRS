<?php
$cproject_start_one = 1;
$cproject_start_no = (count($foaf_current_project) > 0) ? count($foaf_current_project) + 1 : 1;
if(is_array($foaf_current_project) && count($foaf_current_project) > 0) {
	$kno_cp = 1;
	foreach ($foaf_current_project as $currentProject) {
		$kno_cp++;
		if($kno_cp > 2) {
			$cp_remove_btn = '&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo progetto">-</a>';
		}
		$cp_rows .= '<tr class="user_cproject">
						<td>
							<table cellspacing="5" cellpadding="5" style="width: 95%;">
								<tr>
									<td>
										<input type="text" style="width: 50%;" name="user_cproject_title_' . $kno_cp . '" rel="cproject" placeholder="Titolo del progetto" title="Titolo del progetto" value="' . $currentProject->get("dc:title") . '" />
										<br />
										<br />
										<textarea name="user_cproject_desc_' . $kno_cp . '" placeholder="Descrizione del progetto" style="width: 90%; height: 50px;">' . $currentProject->get("dc:description") . '"</textarea>
										<br />
										<br />
										<input type="uri" placeholder="Pagina del progetto (url)" id="user_cproject_page_' . $kno_cp . '" name="user_cproject_page_' . $kno_cp . '" style="width: 50%;" value="' . $currentProject->get("foaf:homepage") . '" />
										<br />
										<br />
										<input id="user_cproject_logo_' . $kno_cp . '" type="uri" placeholder="Logo del progetto (uri)" name="user_cproject_logo_' . $kno_cp . '" style="width: 50%;" value="' . $currentProject->get("foaf:logo") . '" />
									</td>
								</tr>
							</table>
						</td>
						<td class="separator">&nbsp;</td>
						<td align="center" style="width: 100px;">
							<a class="btn add" style="float: none;" href="javascript: void(0);" title="Aggiungi un progetto">+</a>' . $cp_remove_btn . '
						</td>
					</tr>';
		if($kno_cp < (count($foaf_current_project) + 1)) {
			$cp_rows .= '<tr class="user_cproject addedtr_line"><td colspan="3"><hr></td></tr>';
		}
	}
} else {
	$cp_rows = '<tr class="user_cproject">
					<td>
						<table cellspacing="5" cellpadding="5" style="width: 95%;">
							<tr>
								<td>
									<input type="text" style="width: 50%;" name="user_cproject_title_$cproject_start_no" class="cproject" placeholder="Titolo del progetto" title="Titolo del progetto" value="" />
									<br />
									<br />
									<textarea name="user_currentProject_desc_$cproject_start_no" placeholder="Descrizione del progetto" style="width: 90%; height: 50px;"></textarea>
									<br />
									<br />
									<input type="uri" placeholder="Pagina del progetto (url)" id="user_cproject_page_$cproject_start_no" name="user_cproject_page_$cproject_start_no" style="width: 50%;" value="" />
									<br />
									<br />
									<input id="user_cproject_logo_$cproject_start_no" type="uri" placeholder="Logo del progetto (uri)" name="user_cproject_logo_$cproject_start_no" style="width: 50%;" value="" />
								</td>
							</tr>
						</table>
					</td>
					<td class="separator">&nbsp;</td>
					<td align="center" style="width: 100px;">
						<a class="btn add" style="float: none;" href="javascript: void(0);" title="Aggiungi un progetto">+</a>
					</td>
				</tr>';
}
$pproject_start_one = 1;
$pproject_start_no = (count($foaf_past_project) > 0) ? count($foaf_past_project) + 1 : 1;

if(is_array($foaf_past_project) && count($foaf_past_project) > 0) {
	$kno_pp = 1;
	foreach ($foaf_past_project as $pastProject) {
		$kno_pp++;
		if($kno_pp > 2) {
			$pp_remove_btn = '&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo progetto">-</a>';
		}
		$pp_rows .= '<tr class="user_pproject">
						<td>
							<table cellspacing="5" cellpadding="5" style="width: 95%;">
								<tr>
									<td>
										<input type="text" style="width: 50%;" name="user_pproject_title_' . $kno_pp . '" rel="pproject" placeholder="Titolo del progetto" title="Titolo del progetto" value="' . $pastProject->get("dc:title") . '" />
										<br />
										<br />
										<textarea name="user_pproject_desc_' . $kno_pp . '" placeholder="Descrizione del progetto" style="width: 90%; height: 50px;">' . $pastProject->get("dc:description") . '"</textarea>
										<br />
										<br />
										<input type="uri" placeholder="Pagina del progetto (url)" id="user_pproject_page_' . $kno_pp . '" name="user_pproject_page_' . $kno_pp . '" style="width: 50%;" value="' . $pastProject->get("foaf:homepage") . '" />
										<br />
										<br />
										<input id="user_pproject_logo_' . $kno_pp . '" type="uri" placeholder="Logo del progetto (uri)" name="user_pproject_logo_' . $kno_pp . '" style="width: 50%;" value="' . $pastProject->get("foaf:logo") . '" />
									</td>
								</tr>
							</table>
						</td>
						<td class="separator">&nbsp;</td>
						<td align="center" style="width: 100px;">
							<a class="btn add" style="float: none;" href="javascript: void(0);" title="Aggiungi un progetto">+</a>' . $cp_remove_btn . '
						</td>
					</tr>';
		if($kno_pp < (count($foaf_past_project) + 1)) {
			$pp_rows .= '<tr class="user_pproject addedtr_line"><td colspan="3"><hr></td></tr>';
		}
	}
} else {
	$pp_rows .= '<tr class="user_pproject">
					<td>
						<table cellspacing="5" cellpadding="5" style="width: 95%;">
							<tr>
								<td>
									<input type="text" style="width: 50%;" name="user_pproject_title_$pproject_start_no" class="pproject" placeholder="Titolo del progetto" title="Titolo del progetto" value="" />
									<br />
									<br />
									<textarea name="user_currentProject_desc_$pproject_start_no" placeholder="Descrizione del progetto" style="width: 90%; height: 50px;"></textarea>
									<br />
									<br />
									<input type="uri" placeholder="Pagina del progetto (url)" id="user_pproject_page_$pproject_start_no" name="user_pproject_page_$pproject_start_no" style="width: 50%;" value="" />
									<br />
									<br />
									<input id="user_pproject_logo_$pproject_start_no" type="uri" placeholder="Logo del progetto (uri)" name="user_pproject_logo_$pproject_start_no" style="width: 50%;" value="" />
								</td>
							</tr>
						</table>
					</td>
					<td class="separator">&nbsp;</td>
					<td align="center" style="width: 100px;">
						<a class="btn add" style="float: none;" href="javascript: void(0);" title="Aggiungi un progetto">+</a>
					</td>
				</tr>';
}
$projects_data_div = <<<EOF
<div id="projects_data_div" style="display: none;">
	<fieldset>
		<legend>Progetti correnti</legend>
		<input type="hidden" id="cproject_no" value="1" />
		<table cellpadding="10" cellspacing="10" style="width: 100%;" id="user_cproject">
			$cp_rows
		</table>
	</fieldset>
	<br />
	<br />
	<fieldset>
		<legend>Progetti passati</legend>
		<input type="hidden" id="pproject_no" value="1" />
		<table cellpadding="10" cellspacing="10" style="width: 100%;" id="user_pproject">
			$pp_rows
		</table>
	</fieldset>
</div>
EOF;
?>