<?php
/**
* Load form for page of feed
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
$content_body = <<<Feed_form
<script type="text/javascript">
$(document).ready(function() {
	$("#rss_uri").blur(function(){
		check_if_theres_rss("check_page");
	});
});
</script>
<input type="hidden" id="results_index" value="" />
<div id="add_feed_form">
	<form action="" method="post" id="add_feeds_page" onsubmit="return check_if_theres_rss('check_page', this); return false;">
		<input type="hidden" name="decrypted_user" value="{DECRYPTED_USER}" />
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<fieldset>
						<legend class="edit">Pagina di Feeds</legend>
						Inserisci l'Indirizzo della pagina contenente un elenco di feeds
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="url" id="rss_uri" name="rss_uri" autofocus="autofocus" style="width: 99%;" value="$rss_uri" />
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="contenuto"></div>
Feed_form;

require_once("../../../../include/conf/_ajax/replacing_object_data.php");
print $content_body;
?>