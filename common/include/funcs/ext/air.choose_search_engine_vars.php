<?php
header('Content-Type: text/html; charset=utf-8');

$uri = $_GET["uri"];
$src = "air.parse_external_page.php?uri=" . $uri . "&search=" . urlencode("<input") . "&replace=" . urlencode("<input style=\"position: relative; z-index: 300;\"");
$content_body = <<<Search_engine_vars
<script type="text/javascript" src="../../../js/jquery-1.6.min.js"></script>
<script type="text/javascript" src="../../../js/jquery_ui_effects/ui/jquery-ui.min.js"></script>
<link type="text/css" href="../../../css/ext.css" rel="stylesheet" />
<script type="text/javascript" src="air.choose_search_engine.js"></script>
<div id="lateral_panel">
	<table id="steps" cellspacing="0">
		<tr>
			<td id="step_no"></td>
			<td id="step_title"></td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="step_description"></div>
				<table id="arrows">
					<tr>
						<td>
							<a id="prev_step" href="javascript: void(0);" onclick="arrow_clicked('prev', $('#step_no_val').val())"></a>
						</td>
						<td>
							<a id="skip_step" href="javascript: void(0);" onclick="arrow_clicked('skip', $('#step_no_val').val())"></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div id="steps_no">Passo <span id="step_no_span">#</span> di 5</div>
</div>
<input type="hidden" id="step_no_val" value="1" />
<input type="hidden" id="current_created" value="" />
<input type="hidden" id="selected_var_1" value="" />
<input type="hidden" id="selected_var_1_id" value="" />
<input type="hidden" id="selected_var_2" value="" />
<input type="hidden" id="selected_var_2_id" value="" />
<input type="hidden" id="selected_var_3" value="" />
<input type="hidden" id="selected_var_3_id" value="" />
<input type="hidden" id="selected_var_4" value="" />
<input type="hidden" id="selected_var_4_id" value="" />
<input type="hidden" id="selected_var_5" value="" />
<input type="hidden" id="selected_var_5_id" value="" />

<iframe id="content" src="$src"></iframe>
Search_engine_vars;

$origin = "ext";
require_once("../../conf/replacing_object_data.php");

print $content_body;
?>