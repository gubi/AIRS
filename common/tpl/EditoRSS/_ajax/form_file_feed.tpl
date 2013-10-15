<?php
/**
* Load form for file feed
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
$content_body = <<<__File_upload
Seleziona un file contenente più di un feed, solitamente un file <acronym title="Comma-Separated Values">CSV</acronym>, <acronym title="eXtensible Markup Language">XML</acronym> o <acronym title="Really Simple Syndication">RSS</acronym>.<br />
Il sistema rileverà automaticamente i feed presenti nel file e ne scansionerà le news afferenti.<br />
&Egrave; possibile tentare di <i>dare in pasto</i> al Sistema anche dei files pdf, ma con poca garanzia sul risultato.
<br />
<br />
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery_file_upload/jquery.fileupload-ui.css">
<script src="{ABSOLUTE_PATH}common/js/jquery_ui_effects/ui/jquery-ui.min.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_tmpl/jquery.tmpl.min.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/jquery.iframe-transport.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/jquery.fileupload.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/jquery.fileupload-ui.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery.getimagedata.min.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/application_ajax.js"></script>
<div id="fileupload_loading"><br /><img src="common/media/img/loader_horizontal.gif" /></div>
<div id="fileupload" style="display: none;">
	<input type="hidden" id="formtype" value="xml_file" />
	<form action="{ABSOLUTE_PATH}common/js/jquery_file_upload/upload_ajax.php" method="POST" enctype="multipart/form-data">
		<div class="fileupload-buttonbar">
			<label class="fileinput-button upload_button">
				<input type="file" name="files[]" id="input_file_upload" accept="text/csv" />
			</label>
		</div>
	</form>
	<div class="fileupload-content no-height">
		<table class="files list"></table>
		<div class="fileupload-progressbar"></div>
	</div>
</div>
<script id="template-upload" type="text/x-jquery-tmpl">
	<tr class="template-upload{{if error}} ui-state-error{{/if}}">
		<td class="preview"></td>
		{{if error}}
			<td class="name"><span class="xml_error">{NAME}</span></td>
			<td class="size">{SIZEF}</td>
			<td colspan="2" style="border-top: #CD0A0A 1px solid !important; white-space: pre;">
				{{if error === 'maxFileSize'}}Il file è troppo grande
				{{else error === 'minFileSize'}}Il file è troppo leggero
				{{else error === 'acceptFileTypes'}}Formato di file non valido
				{{else error === 'maxNumberOfFiles'}}È consentito il caricamento di un solo file alla volta
				{{else}}{ERROR}
				{{/if}}
			</td>
			<td>
		{{else}}
			<td class="name"><span class="xml">{NAME}</span></td>
			<td class="size">{SIZEF}</td>
			<td class="progress"><div></div></td>
			<td>
				<span  class="start"><button>Carica</button></span>
		{{/if}}
				<span class="cancel"><button>Annulla</button></span>
			</td>
	</tr>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
	<tr class="template-download{{if error}} ui-state-error{{/if}}">
		{{if error}}
			{{if thumbnail_url}}
				<td></td>
			{{/if}}
			<td class="name">{NAME}</td>
			<td class="size">{SIZEF}</td>
			<td colspan="2" style="border-top: #CD0A0A 1px solid !important;">
				{{if error === 1}}Il file supera la soglia di upload_max_filesize (direttiva php.ini)
				{{else error === 2}}Il file supera la soglia di MAX_FILE_SIZE (direttiva HTML)
				{{else error === 3}}Il file è stato caricato solo parzialmente
				{{else error === 4}}Nessun file caricato
				{{else error === 5}}Directory di destinazione errrata
				{{else error === 6}}È fallita la scrittura del file sul disco
				{{else error === 7}}Caricamento interrotto da un'estensione
				{{else error === 'maxFileSize'}}Il file è troppo grande
				{{else error === 'minFileSize'}}Il file è troppo leggero
				{{else error === 'acceptFileTypes'}}Formato di file non valido
				{{else error === 'maxNumberOfFiles'}}È consentito il caricamento di un solo file alla volta
				{{else error === 'uploadedBytes'}}I dati caricati superano la dimensione del file
				{{else error === 'emptyResult'}}Risultato del caricamente di un file vuoto
				{{else}}{ERROR}
				{{/if}}
			</td>
		{{else}}
			{{if thumbnail_url}}
				<td class="preview">
					<a href="{URL}" target="_blank"><img src="{THUMBNAIL_URL}"></a>
				</td>
			{{/if}}
			<td class="name">
				<a class="xml" href="{URL}"{{if thumbnail_url}} target="_blank"{{/if}}>{NAME}</a>
			</td>
			<td class="size">{SIZEF}</td>
			<td colspan="2"></td>
		{{/if}}
		<td class="delete">
			<button data-type="{DELETE_TYPE}" data-url="{DELETE_URL}">Delete</button>
		</td>
	</tr>
</script>
__File_upload;

require_once("../../../../include/conf/_ajax/replacing_object_data.php");
$content_body = str_replace(
					array("{NAME}",
						 "{SIZEF}",
						 "{ERROR}",
						 "{URL}",
						 "{THUMBNAIL_URL}",
						 "{DELETE_TYPE}",
						 "{DELETE_URL}"), 
					array("\${name}",
						 "\${sizef}",
						 "\${error}",
						 "\${url}",
						 "\${thumbnail_url}",
						 "\${delete_type}",
						 "\${delete_url}"), 
					$content_body);
print $content_body;
?>