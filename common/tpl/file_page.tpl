<?php
/**
* Generates file description page
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
* @author		Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

function file_size($file, $setup = null){
	$FZ = ($file && @is_file($file)) ? filesize($file) : NULL;
	$FS = array("bytes","Kb","Mb","Gb","Tb","Pb","Eb","Zb","Yb");

	if(!$setup && $setup !== 0){
		return number_format($FZ/pow(1024, $I=floor(log($FZ, 1024))), ($i >= 1) ? 2 : 0) . ' ' . $FS[$I];
	} elseif ($setup == 'INT') {
		return number_format($FZ);
	} else {
		return number_format($FZ/pow(1024, $setup), ($setup >= 1) ? 2 : 0 ). ' ' . $FS[$setup];
	}
}

$content_subtitle = "Visualizzazione del file";
// Acquisisce i metadati dal database
$check_rdf = $pdo->query("select * from `airs_rdf_files` where `file` = '" . addslashes($GLOBALS["page"]) . "'");
if ($check_rdf->rowCount() > 0){
	$file_rdf_a_tags = "";
	while ($dato_check_rdf = $check_rdf->fetch()){
		$file_rdf_id = $dato_check_rdf["id"];
		$file_rdf_title = $dato_check_rdf["title"];
		$file_rdf_description = $dato_check_rdf["description"];
		$file_rdf_tags = str_replace(",", ", ", $dato_check_rdf["tag"]);
			$tags = explode(",", $dato_check_rdf["tag"]);
			foreach($tags as $tag){
				$file_rdf_a_tags .= "<a class=\"tag\" href=\"" . $absolute_path . "Tag/" . str_replace(" ", "_", $tag) . "\">" . $tag . "</a> ";
			}
		$file_rdf_origin = $dato_check_rdf["origins"];
		$file_rdf_origin_uri = $dato_check_rdf["origins_uri"];
		$file_rdf_author = $dato_check_rdf["author"];
		$file_rdf_author_uri = $dato_check_rdf["author_uri"];
		$file_rdf_entity = $dato_check_rdf["author_entity"];
		$file_rdf_entity_uri = $dato_check_rdf["author_entity_uri"];
		
		$file_rdf_license_type = $dato_check_rdf["license"];
			$check_license = $pdo->query("select * from airs_licenses where `uri` = '" . addslashes($dato_check_rdf["license_uri"]) . "'");
			if ($check_license->rowCount() > 0){
				while ($dato_check_license = $check_license->fetch()){
					$file_rdf_license_description = str_replace("\n", "<br />", $dato_check_license["description"]);
				}
			}
		$file_rdf_license_uri = $dato_check_rdf["license_uri"];
		
		if (strlen($file_rdf_title) == 0){
			$file_rdf_title = "<i>nessun titolo</i>";
		}
		if (strlen($file_rdf_description) == 0){
			$file_rdf_description = "<i>nessuna descrizione</i>";
		}
		if (strlen($file_rdf_tags) == 0){
			$file_rdf_tags = "<i>nessun tag - <u>il file non &egrave; indicizzato</u></i>";
		}
		
		// Visualizzazione del titolo
		$rdf_metadata_txt = "<tr><th valign=\"top\">Titolo:</th><td>" . $file_rdf_title . "</td></tr>";
		// Visualizzazione della descrizione
		$rdf_metadata_txt .= "<tr><th valign=\"top\">Descrizione:</th><td>" . $file_rdf_description . "</td></tr>";
		// Visualizzazione della descrizione
		$rdf_metadata_txt .= "<tr><th valign=\"top\">Tag:</th><td>" . $file_rdf_a_tags . "</td></tr>";
		$rdf_metadata_txt .= "<tr><td colspan=\"2\"></td></tr>";
			
			// Visualizzazione dell'origine del file, se impostata
			if (strlen($file_rdf_origin) > 0){
				$rdf_metadata_txt .= "<tr><th valign=\"top\">Origine:</th><td>" . $file_rdf_origin;
				if (strlen($file_rdf_origin_uri) > 0){
					$rdf_metadata_txt .= "<br /><a href=\"" . $file_rdf_origin_uri . "\" target=\"_blank\">" . $file_rdf_origin_uri . "</a></td></tr>";
				} else {
					$rdf_metadata_txt .= "</td></tr>";
				}
			}
			// Visualizzazione dell'autore del file, se impostato
			if (strlen($file_rdf_author) > 0 || strlen($file_rdf_origin_uri) > 0){
				$rdf_metadata_txt .= "<tr><th valign=\"top\">Autore:</th><td>" . $file_rdf_author;
				if (strlen($file_rdf_author_uri) > 0){
					$rdf_metadata_txt .= "<br /><a href=\"" . $file_rdf_author_uri . "\" target=\"_blank\">" . $file_rdf_author_uri . "</a></td></tr>";
				} else {
					$rdf_metadata_txt .= "</td></tr>";
				}
				
				// Visualizzazione dell'entità afferente all'autore del file, se impostato
				if (strlen($file_rdf_entity) > 0){
					$rdf_metadata_txt .= "<tr><th valign=\"top\">Entità di afferenza:</th><td>" . $file_rdf_entity;
					if (strlen($file_rdf_entity_uri) > 0){
						$rdf_metadata_txt .= "<br /><a href=\"" . $file_rdf_entity_uri . "\" target=\"_blank\">" . $file_rdf_entity_uri . "</a></td></tr>";
					} else {
						$rdf_metadata_txt .= "</td></tr>";
					}
				}
				$rdf_metadata_txt .= "<tr><td colspan=\"2\"></td></tr>";
			}
			// Visualizzazione della licenza del file, se impostata
			if (strlen($file_rdf_license_type) > 0 || strlen($file_rdf_license_uri) > 0){
				$rdf_metadata_txt .= "<tr><th valign=\"top\">Licenza:</th><td><a href=\"" . $file_rdf_license_uri . "\" target=\"_blank\">" . $file_rdf_license_type . "</a><div id=\"license_info\">" . $file_rdf_license_description . "</div></td></tr>";
			}
	}
}
$path = $_SERVER["DOCUMENT_ROOT"] . "share_data/files/" . $GLOBALS["page_m"] . "/";
if (is_dir($path)){
	$filepath = "share_data/files/" . $GLOBALS["page_m"] . "/";
	$previewpath = "share_data/thumbnails/" . $GLOBALS["page_m"] . "/";
	
	$dir_handle = opendir($path);
	while ($file = readdir($dir_handle)) {
		if($file !== "." && $file !== ".."){
			$content_title = $file;
			$filepath_file = $filepath . $file;
			$previewpath_file = $previewpath . $file;
			$content_subtitle = "Visualizzazione del file";
			
			// Peso del file
			$file_weight = file_size($_SERVER["DOCUMENT_ROOT"] . $filepath_file);
			$file_data = pathinfo($_SERVER["DOCUMENT_ROOT"] . $filepath_file);
			$file_ext = $file_data["extension"];
			
			if (is_file($_SERVER["DOCUMENT_ROOT"] . $filepath_file)){
				$the_file_mime = mime_content_type($_SERVER["DOCUMENT_ROOT"] . $filepath_file);
				$the_file_last_access = date("d M Y H:i:s.", filemtime($_SERVER["DOCUMENT_ROOT"] . $filepath_file));
				
				switch($file_ext) {
					case "gif":
					case "jpeg":
					case "jpg":
					case "png":
					case "bmp":
					case "tiff":
					case "tif":
					case "ico":
							// Dimensioni dell'immagine
							list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"] . $filepath_file);
							$image_dimension = $width . "x" . $height;
						$show = "<div><a href=\"" . $absolute_path . $filepath_file . "\" class=\"zoombox\" title=\"" . $file_rdf_title . " (" . $image_dimension . ")\"><img src=\"" . $filepath_file . "\" /><span>" . $file_rdf_title . " (" . $image_dimension . ")</span></a></div>";
						$download_preview = '<li><a href="javascript: void(0);" onclick="download_preview()" class="download" title="Salva l\'anteprima del file su disco">Download anteprima</a></li>';
						break;
					case "pdf":
						$show = '<iframe src="' . $absolute_path . 'common/js/pdf-js/web/viewer.php?file=' . $absolute_path . $filepath_file . '" style="width: 100%; height: 350px; background-color: #121212; border: #ccc 1px solid;"></iframe>';
						$download_preview = "";
						break;
					default:
						if($exif = @exif_read_data($filepath_file, 0, true)) {
							foreach ($exif as $key => $section) {
								foreach ($section as $name => $val) {
									$exif_data .= "<tr><th>" . $name . ":</th><td>" . $val . "</td></tr>";
								}
							}
						}
						break;	
				}
			}
		} else {
			//require_once("common/tpl/__404_login.tpl");
		}
	}
	$content_sub_subtitle = $file_rdf_author;
	
	if ($GLOBALS["user_level"] > 0){
		$edit_frm = <<<Edit_frm
		<tr>
			<td colspan="2">
				<form method="post" style="border: 0px none; box-shadow: 0 0 0;">
					<input type="submit" name="edit_file_btn" value="Carica un file differente" />
					<input type="submit" name="edit_rdf_btn" value="Modifica i dati rdf" style="margin-right: 15px;" />
				</form>
			</td>
		</tr>
Edit_frm;
	} else {
		$edit_frm = "";
	}
	$content_body = <<<File_page
	<script type="text/javascript">
	function download() {
		window.location = "$absolute_path/common/include/funcs/_force_download.php?file=$filepath_file";
	}
	function download_preview() {
		window.location = "$absolute_path/common/include/funcs/_force_download.php?file=$previewpath_file";
	}
	function download_rdf(id) {
		window.location = "$absolute_path/common/include/funcs/generate_rdf.php?id=" + id;
	}
	</script>
	<table cellspacing="0" cellpadding="0" style="width: 100%;">
		<tr>
			<td valign="top" style="text-align: center;" id="show">
				<table cellspacing="5" cellpadding="0" style="width: 100%;">
					<tr>
						<td valign="top">$show</td>
						<td style="width: 200px; text-align: right; padding-left: 10px;" valign="top">
							<div id="show_options">
								<ul>
									<li><a href="javascript: void(0);" onclick="download_rdf('$file_rdf_id')" class="rdf" title="Scarica il file RDF di questo file">Metadati RDF</a></li>
								</ul>
								<hr />
								<ul>
									<li><a href="javascript: void(0);" onclick="download()" class="download" title="Salva il file su disco">Download</a></li>
									$download_preview
								</ul>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<br />
				<fieldset>
					<legend class="metadata">METADATI DEL FILE</legend>
					<table cellspacing="2" cellpadding="2" style="width: 100%;">
						<tr>
							<td valign="top">
								<table cellspacing="2" cellpadding="2" style="width: 100%;">
									<tr>
										<th>File:</th>
										<td>$file</td>
									</tr>
									<tr>
										<th><acronym title="Multipurpose Internet Mail Extensions">MIME</acronym>:</th>
										<td>$the_file_mime</td>
									</tr>
									<tr>
										<th>Ultima modifica:</th>
										<td>$the_file_last_access</td>
									</tr>
									<tr>
										<th>Peso:</th>
										<td>$file_weight</td>
									</tr>
									<tr>
										<th>Dimensioni:</th>
										<td>$image_dimension</td>
									</tr>
								</table>
							</td>
							<td>
								<fieldset>
									<legend>DATI <acronym title="Exchangeable image file format">EXIF</acronym></legend>
									<table cellspacing="0" cellpadding="2" style="width: 100%;">
										$exif_data
									</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</fieldset>
				<br />
				<fieldset>
					<legend class="rdf">METADATI RDF</legend>
					<table cellspacing="3" cellpadding="4" style="width: 100%;">
						$rdf_metadata_txt
					</table>
				</fieldset>
			</td>
		</tr>
		$edit_frm
	</table>
File_page;
	require_once("common/include/conf/replacing_object_data.php");
} else {
	require_once("common/tpl/form_upload_file.tpl");
}
?>