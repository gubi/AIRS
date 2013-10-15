<?php
require_once("FirePHPCore/FirePHP.class.php");
$firephp = FirePHP::getInstance(true);
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	require_once("../../../lib/php-readability-master/Readability.php");
	
	$absolute_path = str_replace(array("/web/htdocs/", "index.php", "/home", "/common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php"), array("http://", "", "", ""), realpath(__FILE__));
	function is_all_text($text, $uri){
		$html = @file_get_contents($uri);
		
		if (function_exists('tidy_parse_string')) {
			$tidy = tidy_parse_string($html, array('indent'=>true), 'UTF8');
			$tidy->cleanRepair();
			$html = $tidy->value;
		}
		$readability = new Readability($html, $url);
		$readability->debug = false;
		$readability->convertLinksToFootnotes = false;
		$result = $readability->init();
		if ($result) {
			$content = $readability->getContent()->innerHTML;
			// if we've got Tidy, let's clean it up for output
			if (function_exists('tidy_parse_string')) {
				$tidy = tidy_parse_string($content, array('indent'=>true, 'show-body-only' => true), 'UTF8');
				$tidy->cleanRepair();
				$content = $tidy->value;
			}
		}
		if (strlen($text) < strlen(strip_tags($content))){
			return "<p style=\"margin-top: 5px;\"><u>Nota:</u> spesso nei feed RSS non &egrave; presente il testo completo di un articolo.<br /><b>&Egrave; possibile estendere tale funzionalit&agrave; abilitando un'acquisizione automatica del testo nella pagina di questo articolo.</b></p><br />";
		}
	}
	if (@fopen($_GET["uri"], 'r')){
		$doc = new DOMDocument();
		if (@$doc->load($_GET["uri"])){
			$arrFeeds = array();
			foreach ($doc->getElementsByTagName('item') as $node) {
				$itemRSS = array ( 
					'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
					'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
					'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
					'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue);
				array_push($arrFeeds, $itemRSS);
			}
			if (isset($_GET["type"]) && trim($_GET["type"]) !== ""){
				$show = $_GET["type"];
				$error = false;
			}
		} else {
			print "invalid uri";
			$error = true;
		}
	} else {
		print "invalid uri";
		$error = true;
	}
	
	if ($error == false){
		switch($show){
			case "save":
				$pdo = db_connect("editorss");
				require_once("../../_parse_content.php");
				require_once("../../_converti_data.php");
				
				foreach ($arrFeeds as $k => $v){
					$add_feed = $pdo->prepare("insert into editorss_feeds (`title`, `description`, `date`, `link`, `parent_id`, `tag`) values(?, ?, ?, ?, ?, ?)");
					$add_feed->bindParam(1, addslashes($v["title"]));
					$add_feed->bindParam(2, parse_content(strip_tags(utf8_decode(addslashes(trim($v["desc"]))))));
					$add_feed->bindParam(3, converti_data(date("D, d M Y", strtotime($v["date"]))));
					$add_feed->bindParam(4, addslashes(trim($v["link"])));
					$add_feed->bindParam(5, addslashes($_GET["id"]));
					$add_feed->bindParam(6, addslashes($v["tags"]));
					if ($add_feed->execute()){
						$added = true;
					} else {
						//print mysql_error();
						print "Si &egrave; verificato un errore durante il salvataggio:<br />" . mysql_error();
						break;
						$added = false;
					}
				}
				if ($added == true){
					print "added";
				}
				break;
			case "show":
				require_once("../../_create_link.php");
				?>
				<link rel="stylesheet" type="text/css" href="common/js/markitup/skins/simple/style.css" />
				<link rel="stylesheet" type="text/css" href="common/js/markitup/sets/wiki/style.css" />
				<script type="text/javascript" src="common/js/markitup/jquery.markitup.js"></script>
				<script type="text/javascript" src="common/js/markitup/sets/html/set.js"></script>
				<script>
				function save_data(){
					loader("salvataggio dei dati", "show");
					$.post("common/include/funcs/_ajax/EditoRSS/add_feeds.php", $("#feedsform").serialize(),
					function(data){
						var response = data.split(":");
						if (response[0] == "error"){
							apprise(response[1], {'animate' : true}, function(r){
								if (r){
									var this_location = location.href;
									var goto_feed = parseInt(response[2]);
									var last = parseInt($("#goto_feed_last").val());
									
									if (goto_feed > last){
										last_no = last;
									} else {
										last_no = response[2];
									}
									this_location = this_location.replace(/\#\d+/, "");
									$("#goto_feed").val(last_no);
									this_location = this_location + "#" + last_no;
									this_location = this_location.replace(/\#\#/, "#");
									location.href = this_location;
									loader("", "hide");
									$("#" + response[3]).focus();
								}
							});
						} else {
							if (response[0] == "added"){
								loader("", "show");
								apprise("Feeds aggiunti con successo.", {'animate' : true}, function(r){
									if (r){
										loader("", "hide");
									}
								});
							} else {
								alert(data);
								loader("", "hide");
							}
						}
					});
					return false;
				}
				$(function() {
					loader("caricamento dei componenti", "show-");
					$.datepicker.regional['it'] = {
						closeText: 'Chiudi',
						prevText: '« Prec',
						nextText: 'Succ »',
						currentText: 'Oggi',
						monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
						monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'],
						dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
						dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
						dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
						dateFormat: 'D, dd M yy',
						firstDay: 1,
						autoSize: true,
						isRTL: false
					};
					$.datepicker.setDefaults($.datepicker.regional['it']);
					$(".datepicker").datepicker();
					
					
					myWikiSettings = {
						nameSpace: "wiki", // Useful to prevent multi-instances CSS conflict
						onShiftEnter:	{keepDefault:false, replaceWith:'<br />\n'},
						onCtrlEnter:	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
						onTab:		{keepDefault:false, openWith:'     '},
						previewParserPath: "~/common/js/markitup/templates/preview.php?type=html",
						previewAutoRefresh: true,
						markupSet:  [
							{name:'Paragrafo', openWith:'<p(!( class="[![Class]!]")!)>', closeWith:'</p>', className:'paragraph'},
							{separator:'---------------' },
							{name:'Grassetto', key:'B', openWith:'<b>', closeWith:'</b>', className:'bold'}, 
							{name:'Corsivo', key:'I', openWith:'<i>', closeWith:'</i>', className:'italic'}, 
							{name:'Sottolineato', key:'U', openWith:'<u>', closeWith:'</u>', className:'underline'}, 
							{name:'Barrato', key:'S', openWith:'<strike>', closeWith:'</strike>', className:'strokethrough'}, 
							{separator:'---------------' },
							{name:'Titolo 1', key:'1', openWith:'<h1(!( class="[![Class]!]")!)>', closeWith:'</h1>', className:'h1'},
							{name:'Titolo 2', key:'2', openWith:'<h2(!( class="[![Class]!]")!)>', closeWith:'</h2>', className:'h2'},
							{name:'Titolo 3', key:'3', openWith:'<h3(!( class="[![Class]!]")!)>', closeWith:'</h3>', className:'h3'},
							{name:'Titolo 4', key:'4', openWith:'<h4(!( class="[![Class]!]")!)>', closeWith:'</h4>', className:'h4'},
							{name:'Titolo 5', key:'5', openWith:'<h5(!( class="[![Class]!]")!)>', closeWith:'</h5>', className:'h5'},
							{name:'Titolo 5', key:'5', openWith:'<h6(!( class="[![Class]!]")!)>', closeWith:'</h6>', className:'h5'},
							{separator:'---------------' }, 
							{name:'Elenco puntato', openWith:'<ul><li>\n', closeWith:'</li></ul>\n', className:'ul'}, 
							{name:'Elenco numerato', openWith:'<ol><li>\n', closeWith:'</li></ol>\n', className:'ol'},
							{separator:'---------------' }, 
							{name:'Immagine', key:'P', replaceWith:'<img src="[![Source:!:http://]!]" alt="[![Testo alternativo]!]" />', className:'image'},
							{name:'Collegamento interno', openWith:'<a href="[![Link:!:<?php print $absolute_path; ?>/]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Testo del collegamento', className:'internal_link'},
							{name:'Collegamento esterno', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!) target="_blank">', closeWith:'</a>', placeHolder:'Testo del collegamento', className:'external_link'},
							{separator:'---------------' },
							{name:'Citazione', openWith:'<pre>', closeWith:'</pre>', className:'quote'},
							{separator:'---------------' },
							{name:'Anteprima', call:'preview', className:'preview'}
						]
					};
					$('.description').markItUp(myWikiSettings);
					
					for(i = 0; i <= $("#results_index").val(); i++){
						$("#tags_" + i + "_ul").tagit({
							fieldName: "tags",
							singleField: true,
							singleFieldNode: $("#tags_" + i),
							removeConfirmation: true,
							allowSpaces: true,
							tagSource: function(search, showChoices) {
								var that = this;
								$.ajax({
									url: "common/include/funcs/_ajax/get_existing_tags.php",
									data: search,
									dataType: "json",
									success: function(choices) {
										showChoices(that._subtractArray(choices, that.assignedTags()));
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
					}
					loader("", "hide");
				});
				</script>
				
				<form action="" method="post" id="feedsform" onsubmit="return save_data(); return false;">
					<input type="hidden" name="counter" value="<?php print count($arrFeeds); ?>" />
					<input type="hidden" name="parent_id" value="<?php print $_GET["id"]; ?>" />
					<table style="width: 100%;" cellpadding="0" cellspacing="0">
						<tr>
							<td align="right" valign="bottom" style="border-bottom: #dcdcdc padding-bottom: 25px">
								<input class="noWarn" type="submit" name="add_feed_data_submit2" id="add_feed_data_submit2" value="Salva" style="margin-top: 15px;" />
							</td>
						</tr>
					</table>
					<table cellspacing="0" cellpadding="0" style="width: 100%">
						<?php
						foreach ($arrFeeds as $k => $v){
							if (($k % 2) == 0){
								$table_background = "#fafafa";
							} else {
								$table_background = "#fefefe";
							}
							$k1 = $k+1;
							?>
							<tr>
								<td>
									<a name="<?php print $k1; ?>"></a>
									<table cellspacing="0" cellpadding="5" class="frm" style="background-color: <?php print $table_background; ?>; padding: 10px; margin-bottom: 10px; -moz-border-radius: 6px;">
										<tr>
											<td rowspan="6" style="font-size: 9em; width: 81px; font-style: italic; font-family: Georgia; color: #999; text-align: right;"><?php print $k1; ?></td>
										</tr>
										<?php
										foreach ($v as $kk => $vv){
											?>
											<tr>
												<?php
												switch($kk){
													case "title":
														print "<th>Titolo</th>";
														print "<td><input type=\"text\" name=\"title_" . $k1 . "\" id=\"title_" . $k1 . "\" style=\"width: 100%;\" value=\"" . ucfirst(trim($vv)) . "\" /></td>";
														break;
													case "link":
														print "<th>Link</th>";
														print "<td><input type=\"url\" name=\"link_" . $k1 . "\" id=\"link_" . $k1 . "\" style=\"width: 80%;\" value=\"" . strtolower(trim($vv)) . "\" />&nbsp;&nbsp;&nbsp;<a href=\"" . $vv . "\" target=\"_blank\" title=\"link esterno\">vedi</a></td>";
														break;
													case "desc":
														print "<th>Descrizione</th>";
														print "<td class=\"description_td\"><textarea name=\"desc_" . $k1 . "\" id=\"desc_" . $k1 . "\" class=\"description no_focus\" style=\"width: 100%;\">" . trim(strip_tags(create_link(str_replace("<br />", "\n", str_replace(array("<p>", "</p>"), "\n", $vv))))) . "</textarea>";
														//print "<a href=\"javascript:void(0);\" onclick=\"compare_contents('" . $v["link"] . "')\" class=\"acquire\">Acquisisci dall'articolo</a></td>";
														print is_all_text(strip_tags($vv), $v["link"]) . "</td>";
														break;
													case "date":
														require_once("../../_converti_data.php");;
														print "<th>Data</th>";
														print "<td><input type=\"date\" class=\"datepicker\" name=\"date_" . $k1 . "\" id=\"date_" . $k1 . "\" style=\"width: 50%;\" value=\"" . converti_data(date("D, d M Y", strtotime($vv))) . "\" /></td>";
														break;
												}
												?>
											</tr>
											<?php
										}
										?>
										<tr>
											<th>Tags</th>
											<td>
												<input type="hidden" id="tags_<?php print $k1; ?>" name="tags_<?php print $k1; ?>" value="<?php print $row_array["tags"]; ?>" />
												<ul id="tags_<?php print $k1; ?>_ul"></ul>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
					<table style="width: 100%; margin-top: -10px;" cellpadding="0" cellspacing="0">
						<tr>
							<td align="right" valign="bottom" style="border-top: #ccc 1px solid; padding-top: 20px;">
								<input class="noWarn" type="submit" name="add_feed_data_submit" id="add_feed_data_submit" value="Salva" />
							</td>
						</tr>
					</table>
				</form>
				<?php
				break;
			case "check":
				print count($arrFeeds) . "";
				break;
		}
	}
} else {
	print "no get";
}
?>