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

$content_body = <<<__File_upload
&Egrave; possibile trascinare il file da caricare direttamente nell'area sottostante. Il sistema dovrebbe accettare anche trascinamenti di immagini provenienti da altri siti.<br />
<br />
Per modificare i metadati del file &egrave; necessario rimuoverlo, quindi attendere il successivo caricamento della pagina.<br />
<u>Si consiglia di salvare una copia del file prima di rimuoverlo definitivamente.</u>
<br />
<br />
<div id="fileupload_loading"><br /><img src="common/media/img/loader_horizontal.gif" /></div>
<div id="fileupload" style="display: none;">
	<input type="hidden" id="maxid" name="maxid" value="$the_id" />
	<form action="common/js/jquery_file_upload/upload.php?page={PAGE}" method="POST" enctype="multipart/form-data">
		<div class="fileupload-buttonbar">
			<label class="fileinput-button">
			<!--<label class="fileinput-button" style="background: url(http://www.madtel.info/inran/common/media/img/accept_16.png) 3px 3px no-repeat;">-->
				<span>Sfoglia...</span>
				<input type="file" name="files[]">
			</label>
			<button type="submit" class="start">Inizia caricamento</button>
			<button type="reset" class="cancel">Annulla caricamento</button>
			<button type="button" class="delete">Elimina il file</button>
		</div>
	</form>
	<div class="fileupload-content">
		<table class="files"></table>
		<!--<div class="fileupload-progressbar"></div>-->
	</div>
</div>
<script id="template-upload" type="text/x-jquery-tmpl">
	<tr class="template-upload{{if error}} ui-state-error{{/if}}">
		<td class="preview"></td>
		<td class="name">{NAME}</td>
		<td class="size">{SIZEF}</td>
		{{if error}}
			<td colspan="2" style="border-top: #CD0A0A 1px solid !important;">
				{{if error === 'maxFileSize'}}Il file &egrave; troppo grande
				{{else error === 'minFileSize'}}Il file &egrave; troppo leggero
				{{else error === 'acceptFileTypes'}}Formato di file non valido
				{{else error === 'maxNumberOfFiles'}}È consentito il caricamento di un solo file alla volta
				{{else}}{ERROR}
				{{/if}}
			</td>
		{{else}}
			<td class="progress"><div></div></td>
			<td class="start"><button>Carica</button></td>
		{{/if}}
		<td class="cancel"><button>Annulla</button></td>
	</tr>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
	<tr class="template-download{{if error}} ui-state-error{{/if}}">
		{{if error}}
			<td></td>
			<td class="name">{NAME}</td>
			<td class="size">{SIZEF}</td>
			<td colspan="2" style="border-top: #CD0A0A 1px solid !important;">
				{{if error === 1}}Il file supera la soglia di upload_max_filesize (direttiva php.ini)
				{{else error === 2}}Il file supera la soglia di MAX_FILE_SIZE (direttiva HTML)
				{{else error === 3}}Il file &egrave; stato caricato solo parzialmente
				{{else error === 4}}Nessun file caricato
				{{else error === 5}}Directory di destinazione errrata
				{{else error === 6}}&Egrave; la scrittura del file sul disco
				{{else error === 7}}Caricamento interrotto da un'estensione
				{{else error === 'maxFileSize'}}Il file &egrave; troppo grande
				{{else error === 'minFileSize'}}Il file &egrave; troppo leggero
				{{else error === 'acceptFileTypes'}}Formato di file non valido
				{{else error === 'maxNumberOfFiles'}}&Egrave; consentito il caricamento di un solo file alla volta
				{{else error === 'uploadedBytes'}}I dati caricati superano la dimensione del file
				{{else error === 'emptyResult'}}Risultato del caricamente di un file vuoto
				{{else}}{ERROR}
				{{/if}}
			</td>
		{{else}}
			<td class="preview">
				{{if thumbnail_url}}
					<a href="{URL}" target="_blank"><img src="{THUMBNAIL_URL}"></a>
				{{/if}}
			</td>
			<td class="name">
				<a href="{URL}"{{if thumbnail_url}} target="_blank"{{/if}}>{NAME}</a>
			</td>
			<td class="size">{SIZEF}</td>
			<td colspan="2"></td>
		{{/if}}
		<td class="delete">
			<button data-type="{DELETE_TYPE}" data-url="{DELETE_URL}">Delete</button>
		</td>
	</tr>
</script>
<script src="{ABSOLUTE_PATH}common/js/jquery_ui_effects/ui/jquery-ui.min.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_tmpl/jquery.tmpl.min.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/jquery.iframe-transport.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/jquery.fileupload.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/jquery.fileupload-ui.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery.getimagedata.min.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_file_upload/application.js"></script>
__File_upload;
require_once("common/include/conf/replacing_object_data.php");
?>