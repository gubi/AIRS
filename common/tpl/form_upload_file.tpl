<?php
/**
* Generates template for upload file
* 
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

if (!isset($_POST["save_rdf_btn"])){
	if (isset($_POST["delete_rdf_btn"])){
		$delete_rdf_data = $pdo->exec("delete from `airs_rdf_files` where id = '" . addslashes($_POST["file_id"]) . "'");
		if($delete_rdf_data === FALSE){
			$content_subtitle = "Errore di eliminazione dei dati";
			$content_body = "Si è verificato un errore durante l'eliminazione dei dati: " . join(", ", $add_rdf_data->errorInfo()); 
		} else {
			$path = "share_data/files/" . $GLOBALS["page_m"] . "/";
			if (is_dir($path)) {
				rmdir($path);
			}
			header("Location: " . $absolute_path . "File:" . $GLOBALS["page_m"]);
		}
	} else {
		$user_data = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($decrypted_user) . "'");
		while($dato_user = $user_data->fetch()){
			$file_rdf_author = ucwords($dato_user["name"] . " " . $dato_user["lastname"]);
			$file_rdf_author_uri = "https://" . $_SERVER["HTTP_HOST"] . "/User/" . ucfirst($decrypted_user);
		}
		
		$check_rdf = $pdo->query("select * from `airs_rdf_files` where `file` = '" . addslashes($GLOBALS["page"]) . "'");
		if ($check_rdf->rowCount() > 0){
			$content_subtitle = "Pagina di modifica dei metadati RDF";
			$delete_btn = "<input type=\"submit\" class=\"delete\" name=\"delete_rdf_btn\" id=\"delete_rdf_btn\" value=\"Rimuovi questo file\" />";
			
			while ($dato_check_rdf = $check_rdf->fetch()){
				$file_rdf_id = $dato_check_rdf["id"];
				$file_rdf_title = $dato_check_rdf["title"];
				$file_rdf_description = $dato_check_rdf["description"];
				$file_rdf_tags = $dato_check_rdf["tag"];
					$tags = explode(",", $dato_check_rdf["tag"]);
					foreach($tags as $tag){
						$file_rdf_li_tags .= "<li>" . $tag . "</li>";
					}
				$file_rdf_origin = $dato_check_rdf["origins"];
				$file_rdf_origin_uri = $dato_check_rdf["origins_uri"];
				$file_rdf_author = $dato_check_rdf["author"];
				$file_rdf_author_uri = $dato_check_rdf["author_uri"];
				$file_rdf_entity = $dato_check_rdf["author_entity"];
				$file_rdf_entity_uri = $dato_check_rdf["author_entity_uri"];
				
				$file_rdf_license_type = $dato_check_rdf["license"];
				$file_rdf_license_uri = $dato_check_rdf["license_uri"];
			}
		} else {
			$content_subtitle = "Pagina di creazione dei metadati RDF";
			$delete_btn = "";
		}
		
		// Select delle license
		$select_licenses_group = $pdo->query("select distinct `group` from `airs_licenses`");
		
		if ($select_licenses_group->rowCount() > 0){
			$licenses_select = "<select name=\"license_type\" id=\"license_type\" onchange=\"$('#license_uri').val(this.value); if(this.options[this.selectedIndex].text != 'Seleziona un tipo'){ $('#license').val(this.options[this.selectedIndex].text); } else { $('#license').val(''); }\" style=\"width: 450px;\">";
			$licenses_select .= "<option value=\"\">Seleziona un tipo</option>";
			
			while ($dato_select_group = $select_licenses_group->fetch()){
				$licenses_select .= "<optgroup label=\"" . utf8_encode($dato_select_group["group"]) . "\">";
					
					$select_licenses = $pdo->query("select * from `airs_licenses` where `group` = '" . addslashes($dato_select_group["group"]) . "'");
					if ($select_licenses->rowCount() > 0){
						while ($dato_select_license = $select_licenses->fetch()){
							if ($dato_select_license["license"] == $file_rdf_license_type){
								$selected = "selected=\"selected\" ";
							} else {
								$selected = "";
							}
							$licenses_select .= "<option " . $selected . "value=\"" . $dato_select_license["uri"] . "\" title=\"" . strip_tags($dato_select_license["description"]) . "\">" . $dato_select_license["license"] . "</option>";
						}
					}
				$licenses_select .= "</optgroup>";
			}
			$licenses_select .= "</select>";
		}
		$content_body = <<<__File_upload
<script src="{ABSOLUTE_PATH}common/js/jquery_tag-it/tag-it.js" type="text/javascript" charset="utf-8"></script>
<link href="{ABSOLUTE_PATH}common/js/jquery_tag-it/jquery.tagit.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
$(document).ready(function() {
	$("#tag").tagit({
		fieldName: "tags",
		singleField: true,
		singleFieldNode: $('#inputTag'),
		removeConfirmation: true,
		allowSpaces: true,
		tagSource: function(search, showChoices) {
			var that = this;
			$.ajax({
				url: "common/include/funcs/_ajax/get_existing_tags.php",
				data: search,
				dataType: "json",
				success: function(choices) {
					showChoices(that._subtract_Array(choices, that.assignedTags()));
				}
			});
		}
	}).click(function(){
		$(this).css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	}).find("input").blur(function(){
		$("#tag").css({
			"border": "#ccc 1px solid",
			"box-shadow": "none"
		});
	}).focus(function(){
		$("#tag").css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	});
	$('legend').click(function(){
		$(this).next("table").slideToggle("slow", function() {
		});
	});
	
	$("#inputTag").val("$file_rdf_tags");
});
</script>

Il file che si vuole visualizzare non esiste.<br />
&Egrave; possibile caricarne uno nel modulo a seguire. Una volta creati i suoi metadati, per uniformarlo alle nuove specifiche riguardo alla semantica, si potr&agrave; passare al successivo modulo di caricamento.
<br />
Per maggiori informazioni riguardo all'utilit&agrave; dei metadati consultare le pagine relative al <a href="http://it.wikipedia.org/wiki/Web_semantico" title="Wikipedia: Wen Semantico" target="_blank">Web Semantico (o Web 3.0)</a> e allo strumento <a href="http://it.wikipedia.org/wiki/Resource_Description_Framework" title="Wikipedia: RDF" target="_blank">RDF</a>
<br />
<br />
<form method="post" action="" id="file_form">
	<fieldset>
		<legend><b>Modulo di creazione dei metadati RDF</b></legend>
		<table cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 10px;">
			<tr>
				<td valign="top" style="width: 128px;">
					<img src="common/media/img/rdf_document_128_ccc.png" />
				</td>
				<td valign="top">
					<fieldset>
						<legend class="edit">File</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="hidden" id="file_id" name="file_id" value="$file_rdf_id" />
									<input type="text" id="title" name="title" placeholder="Titolo" style="width: 72%;" value="$file_rdf_title" />
								</td>
							</tr>
							<tr>
								<td>
									<textarea style="width: 99%;" name="description" id="description" placeholder="Descrizione">$file_rdf_description</textarea>
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend class="label">Tag</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="hidden" id="inputTag" name="tag" value="$file_rdf_tags" />
									<ul id="tag">$file_rdf_li_tags</ul>
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<br />
					<fieldset>
						<legend class="origin" title="Espandi/contrai">Origine</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%; display: none;" id="origin_table">
							<tr>
								<td>
									<input type="text" id="file_origin" name="file_origin" placeholder="Origine del file" style="width: 99%;" value="$file_rdf_origin" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="uri" id="origins_uri" name="origins_uri" placeholder="URI dell'origine" style="width: 72%;" value="$file_rdf_origin_uri" />
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend class="author" title="Espandi/contrai">Autore</legend>
						<table cellspacing="10" cellpadding="5" style="width: 100%; display: none;">
							<tr>
								<td>
									<input type="text" id="file_author" name="file_author" placeholder="Autore del file" style="width: 99%;" value="$file_rdf_author" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="uri" id="author_uri" name="author_uri" placeholder="URI relativo all'autore (Pagina web, sito, blog, ecc...)" style="width: 72%;" value="$file_rdf_author_uri" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<input type="text" id="file_author_entity" name="file_author_entity" placeholder="Entit&agrave; afferente all'autore o all'opera (Ente, Istituzione, Azienda, ecc...)" style="width: 99%;" value="$file_rdf_entity" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="uri" id="author_entity_uri" name="author_entity_uri" placeholder="URI relativo ell'entit&agrave; afferente all'autore o all'opera (Pagina web, sito, blog, ecc...)" style="width: 72%;" value="$file_rdf_entity_uri" />
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend class="license" title="Espandi/contrai">Licenza</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%; display: none;">
							<tr>
								<td>
									$licenses_select
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="license" name="license" placeholder="Tipo di licenza" style="width: 99%;" value="$file_rdf_license_type" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="uri" id="license_uri" name="license_uri" placeholder="URI della licenza" style="width: 72%;" value="$file_rdf_license_uri" />
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
				</td>
			</tr>
		</table>
	</fieldset>
	<table cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 10px;">
		<tr>
			<td>
				$delete_btn
				<input type="submit" name="save_rdf_btn" id="save_rdf_btn" value="Avanti »" />
			</td>
		</tr>
	</table>
</form>
__File_upload;
	}
} else {
	if ($_POST["file_id"] == ""){
		$add_rdf_data = $pdo->prepare("insert into `airs_rdf_files` (`file`, `title`, `description`, `tag`, `origins`, `origins_uri`, `author`, `author_uri`, `author_entity`, `author_entity_uri`, `license`, `license_uri`) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$add_rdf_data->bindParam(1, addslashes($GLOBALS["page"]));
		$add_rdf_data->bindParam(2, addslashes($_POST["title"]));
		$add_rdf_data->bindParam(3, addslashes($_POST["description"]));
		$add_rdf_data->bindParam(4, addslashes($_POST["tag"]));
		$add_rdf_data->bindParam(5, addslashes($_POST["file_origin"]));
		$add_rdf_data->bindParam(6, addslashes($_POST["origins_uri"]));
		$add_rdf_data->bindParam(7, addslashes($_POST["file_author"]));
		$add_rdf_data->bindParam(8, addslashes($_POST["author_uri"]));
		$add_rdf_data->bindParam(9, addslashes($_POST["file_author_entity"]));
		$add_rdf_data->bindParam(10, addslashes($_POST["author_entity_uri"]));
		$add_rdf_data->bindParam(11, addslashes($_POST["license"]));
		$add_rdf_data->bindParam(12, addslashes($_POST["license_uri"]));
		if ($add_rdf_data->execute()){
			$content_subtitle = "Pagina di caricamento del file";
			require_once("common/tpl/upload_file_template.tpl");
		} else {
			$content_subtitle = "Errore di generazione dei dati";
			$content_body = "Si è verificato un errore durante il salvataggio dei dati: " . join(", ", $add_rdf_data->errorInfo());
		}
		$get_id = $pdo->query("select max(`id`) as maxid from airs_rdf_files");
		if ($get_id->rowCount() > 0){
			while ($dato_get_id = $get_id->fetch()){
				$the_id = $dato_get_id["maxid"];
			}
		}
	} else {
		$the_id = $_POST["file_id"];
		$edit_rdf_data = $pdo->prepare("update airs_rdf_files set `title`=?, `description`=?, `tag`=?, `origins`=?, `origins_uri`=?, `author`=?, `author_uri`=?, `author_entity`=?, `author_entity_uri`=?, `license`=?, `license_uri`=? where `id`=?");
		if ($edit_rdf_data->execute(array(addslashes($_POST["title"]), addslashes($_POST["description"]), addslashes($_POST["tag"]), addslashes($_POST["file_origin"]), addslashes($_POST["origins_uri"]), addslashes($_POST["file_author"]), addslashes($_POST["author_uri"]), addslashes($_POST["file_author_entity"]), addslashes($_POST["author_entity_uri"]), addslashes($_POST["license"]), addslashes($_POST["license_uri"]), $the_id))){
			$content_subtitle = "Pagina di caricamento del file";
			require_once("common/tpl/upload_file_template.tpl");
		} else {
			$content_subtitle = "Errore di generazione dei dati";
			$content_body = "Si è verificato un errore durante il salvataggio dei dati: " . join(", ", $add_rdf_data->errorInfo());
		}
	}
}
?>