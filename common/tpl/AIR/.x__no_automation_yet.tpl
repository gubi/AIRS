<?php
$content_title = "Rimani qui...";
$content_subtitle = "Non vi sono ancora risultati per questa pagina, ma non per molto...";
$content_last_edit = "";
$content_body = <<<__No_res
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	setInterval(function() {
		$.get("common/include/funcs/_ajax/AIR/research_results_list.php", function(data){
			if(data != null){
				location.reload();
			}
		});
	}, 10000);
});
</script>
<table cellspacing="10" cellpadding="10" style="width: 100%;">
	<tr>
		<td style="width: 128px">
			<img src="common/media/img/recent_changes_128_ccc.png" />
		</td>
		<td style="font-size: 1.1em;">
			Il contenuto che si vuole visualizzare non contiene ancora risultati elencabili ma probabilmente è in fase di scansione.<br />
			&Egrave; facile che non siano ancora state fatte automazioni al riguardo perch&eacute; il lavoro di scansione sia stato programmato in un tempo posticipato.<br />
			<br />
			In ogni caso, ogni 10 secondi questa pagina controlla che ci siano nuovi dati, quindi se si &egrave; certi che potrebbero esserci a breve basta non cambiare pagina.
		</td>
	</tr>
</table>
__No_res;
$content_body = str_replace("{REFERER_PAGE}", $GLOBALS["referer_page"], $content_body);
?>