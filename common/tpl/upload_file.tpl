<?php
/**
* Upload file params template
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
* @SLM_status		ok
*/

$content_title = "File: " . $GLOBALS["page_title"];
$content_last_edit = "";

$absolute_path_common_js = $absolute_path . "common/js";
$path = "share_data/files/" . $GLOBALS["page_m"] . "/";
if (!$dir_handle = @opendir($path)){
	mkdir($path, 0777);
	require_once("common/tpl/form_upload_file.tpl");
} else {
	$file_count = 0;
	while ($file = readdir($dir_handle)) {
		if($file !== "." && $file !== ".."){
			$file_count++;
		}
		if ($file_count > 0){
			if(isset($_POST["edit_rdf_btn"])){
				require_once("common/tpl/form_upload_file.tpl");
			} else {
				$content_subtitle = "Pagina di modifica del file";
				require_once("common/tpl/upload_file_template.tpl");
			}
		} else {
			require_once("common/tpl/form_upload_file.tpl");
		}
	}
}
require_once("common/include/conf/replacing_object_data.php");
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
?>