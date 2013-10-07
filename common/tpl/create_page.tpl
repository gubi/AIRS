<?php
/**
* Generates form for creating page
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

if (isset($_POST["save_page_btn"])){
	$_POST["page_content"] = str_replace("{SIG}", "((" . ucfirst($decrypted_user) . ")) " . date("d/m/Y H:i"), $_POST["page_content"]);
	
	if ($_POST["allow_discussions"] == "on"){
		$_POST["allow_discussions"] = 1;
	}
	if ($_POST["restrict_to_level"] == "on"){
		$_POST["restrict_to_level"] = 1;
	}
	if ($_POST["show_right_panel"] == "on"){
		$_POST["show_right_panel"] = 1;
	}
	if ($_POST["show_right_panel_toc"] == "on"){
		$_POST["show_right_panel_toc"] = 1;
	}
	if ($_POST["show_right_panel_tocs"] == "on"){
		$_POST["show_right_panel_tocs"] = 1;
	}
	$check_page = $pdo->query("select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "'");
	if ($check_page->rowCount() > 0){
		if (strlen($_POST["page_content"]) > 0){
			$edit_content = $pdo->prepare("update `airs_content` set `subname`=?, `sub_subname`=?, `title`=?, `subtitle`=?, `body`=?, `allow_discussions`=?, `restrict_to_level`=?, `show_right_panel`=?, `show_right_panel_toc`=?, `show_right_panel_tocs`=?, `is_modifying`=?, `modifying_by`=?, `is_modifying_standby`=? where `id`=?");
			if ($edit_content->execute(array(addslashes($GLOBALS["page_id"]), 
				addslashes($GLOBALS["page_q"]), 
				addslashes($_POST["page_title"]), 
				addslashes($_POST["page_subtitle"]), 
				addslashes($_POST["page_content"]), 
				addslashes($_POST["allow_discussions"]), 
				addslashes($_POST["restrict_to_level"]), 
				addslashes($_POST["show_right_panel"]), 
				addslashes($_POST["show_right_panel_toc"]), 
				addslashes($_POST["show_right_panel_tocs"]), 
				"0", 
				"", 
				"0", 
				$content_id))){
				$add_chronology = $pdo->prepare("insert into `airs_chronology` (`name`, `subname`, `sub_subname`, `title`, `subtitle`, `body`, `reason`, `user`) values(?, ?, ?, ?, ?, ?, ?, ?)");
					$add_chronology->bindParam(1, addslashes($GLOBALS["page_m"]));
					$add_chronology->bindParam(2, addslashes($GLOBALS["page_id"]));
					$add_chronology->bindParam(3, addslashes($GLOBALS["page_q"]));
					$add_chronology->bindParam(4, addslashes($_POST["page_title"]));
					$add_chronology->bindParam(5, addslashes($_POST["page_subtitle"]));
					$add_chronology->bindParam(6, addslashes($_POST["page_content"]));
					$add_chronology->bindParam(7, addslashes($_POST["reason"]));
					$add_chronology->bindParam(8, addslashes($decrypted_user));
				if ($add_chronology->execute()){
					$chronology_maxid = $pdo->query("select max(`id`) as ch_maxid from `airs_chronology`");
					if ($chronology_maxid->rowCount() > 0){
						while ($dato_chronology_maxid = $chronology_maxid->fetch()){
							$maxid = $dato_chronology_maxid["ch_maxid"];
						}
					}
					$add_chronology_version = $pdo->prepare("update `airs_content` set `chronology_version`= '" . $maxid . "' where `id`= '" . $content_id . "'");
					if ($add_chronology_version->execute()){
						print "<br /><br />Attendere...";
						redirect("./" . $GLOBALS["redirect"]);
					} else {
						print join(", ", $add_chronology_version->errorInfo());
					}
				} else {
					print join(", ", $add_chronology->errorInfo());
				}
			}
		} else {
			$edit_content = $pdo->prepare("delete from `airs_content` where `id`='" . $content_id . "'");
			if ($edit_content->execute()){
				print "<br /><br />Attendere...";
				redirect("./" . $GLOBALS["redirect"]);
			}
		}
	} else {
		$edit_content = $pdo->prepare("insert into `airs_content` (`name`, `subname`, `sub_subname`, `title`, `subtitle`, `body`, `allow_discussions`, `restrict_to_level`, `creation_date`, `show_right_panel`, `show_right_panel_toc`, `show_right_panel_tocs`) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$edit_content->bindParam(1, addslashes($GLOBALS["page_m"]));
			$edit_content->bindParam(2, addslashes($GLOBALS["page_id"]));
			$edit_content->bindParam(3, addslashes($GLOBALS["page_q"]));
			$edit_content->bindParam(4, addslashes($_POST["page_title"]));
			$edit_content->bindParam(5, addslashes($_POST["page_subtitle"]));
			$edit_content->bindParam(6, addslashes($_POST["page_content"]));
			$edit_content->bindParam(7, addslashes($_POST["allow_discussions"]));
			$edit_content->bindParam(8, addslashes($_POST["restrict_to_level"]));
			$edit_content->bindParam(9, date("Y-m-d H:i:s"));
			$edit_content->bindParam(10, addslashes($_POST["show_right_panel"]));
			$edit_content->bindParam(11, addslashes($_POST["show_right_panel_toc"]));
			$edit_content->bindParam(12, addslashes($_POST["show_right_panel_tocs"]));
		if ($edit_content->execute()){
			$add_chronology = $pdo->prepare("insert into `airs_chronology` (`name`, `subname`, `sub_subname`, `title`, `subtitle`, `body`, `reason`, `user`) values(?, ?, ?, ?, ?, ?, ?, ?)");
				$add_chronology->bindParam(1, addslashes($GLOBALS["page_m"]));
				$add_chronology->bindParam(2, addslashes($GLOBALS["page_id"]));
				$add_chronology->bindParam(3, addslashes($GLOBALS["page_q"]));
				$add_chronology->bindParam(4, addslashes($_POST["page_title"]));
				$add_chronology->bindParam(5, addslashes($_POST["page_subtitle"]));
				$add_chronology->bindParam(6, addslashes($_POST["page_content"]));
				$add_chronology->bindParam(7, addslashes($_POST["reason"]));
				$add_chronology->bindParam(8, addslashes($decrypted_user));
			if ($add_chronology->execute()){
				$chronology_maxid = $pdo->query("select max(`id`) as `ch_maxid` from `airs_chronology`");
				if ($chronology_maxid->rowCount() > 0){
					while ($dato_chronology_maxid = $chronology_maxid->fetch()){
						$ch_maxid = $dato_chronology_maxid["ch_maxid"];
					}
				}
				$content_maxid = $pdo->query("select max(`id`) as `ch_maxid` from `airs_chronology`");
				if ($content_maxid->rowCount() > 0){
					while ($dato_content_maxid = $content_maxid->fetch()){
						$co_maxid = $dato_content_maxid["ch_maxid"];
					}
				}
				$add_chronology_version = $pdo->prepare("update `airs_content` set `chronology_version`= '" . $ch_maxid . "' where `id`= '" . $co_maxid . "'");
				if ($add_chronology_version->execute()){
					print "<br /><br />Attendere...";
					redirect("./" . $GLOBALS["redirect"]);
				} else {
					print join(", ", $add_chronology_version->errorInfo());
				}
			} else {
				print join(", ", $add_chronology->errorInfo());
			}
		}
	}
} else {
	if (trim($content_title) == ""){
		$content_title = str_replace("_", " ", $GLOBALS["page"]);
	}
	$check_page_existing = $pdo->query("select `id` from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "'");
	if($check_page_existing->rowCount() > 0){
		$action = "modificato";
		while($dato_page = $check_page_existing->fetch()){
			$page_title = htmlentities($content_title);
			$page_subtitle = $content_subtitle;
		}
	} else {
		$action = "creato";
		$page_title = "{WIKI_PAGE_TITLE}";
		$page_subtitle = "";
	}
	$check_page_modifying = $pdo->query("select `id`, `is_modifying`, `modifying_by`, `is_modifying_standby` from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "' and `is_modifying` = '1' and `modifying_by` != '" . addslashes($decrypted_user) . "'");
	if ($check_page_modifying->rowCount() > 0){
		while ($dato_modifying = $check_page_modifying->fetch()){
			if ($dato_modifying["is_modifying_standby"] == 1){
				$is_modifying_standby = true;
			} else {
				$is_modifying_standby = false;
			}
			$is_modifying = true;
			$is_modifying_by = "<a href=\"Utente/" . ucfirst($dato_modifying["modifying_by"]) . "\">" . ucfirst($dato_modifying["modifying_by"]) . "</a>";
		}
	} else {
		$is_modifying = false;
	}
	if ($is_modifying){
		if ($is_modifying_standby){
			$modifying_msg = "<div id=\"modifyng_info_msg\">ATTENZIONE: " . $is_modifying_by . " stava modificando questa pagina ma ora risulta inattivo: in caso riprenda il lavoro le modifiche che si andranno a effettuare potrebbero essere sovrascritte.<br />Sarà comunque possibile recuperare questa versione dalla cronologia della pagina.</div>";
			$disabled = "";
			$markitup_enabled = "myWikiSettings";
		} else {
			$modifying_msg = "<div id=\"modifyng_alert_msg\">ATTENZIONE: " . $is_modifying_by . " sta attualmente modificando questa pagina e pertanto il salvataggio delle modifiche è stato temporaneamente disattivato.<br /><br />Una richiesta asincrona sta provvedendo a controllare ogni 10 secondi se il suo intervento &egrave; stato effettuato.<br />In ogni caso, è stato impostato un timeout di un'ora al termine del quale il pulsante di salvataggio ritornerà attivo.</div>";
			$disabled = " disabled=\"disabled\"";
			$markitup_enabled = "disableWikiSettings";
		}
	} else {
		$modifying_msg = "";
		$markitup_enabled = "myWikiSettings";
		if (!isset($_SESSION["modifying_by"])){
			// Inserisce la sessione per il timeout
			session_set_cookie_params("3600", "/"); // 1 ora, per tutto il dominio
			session_start();
			$_SESSION["modifying_by"] = $decrypted_user;
			$disabled = "";
		}
		$booking_page = $pdo->prepare("update `airs_content` set `is_modifying`= '1', `modifying_by` = '" . addslashes($decrypted_user) . "' where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "'");
		if (!$booking_page->execute()){
			print join(", ", $booking_page->errorInfo());
			exit();
		}
	}
	$session = $_SESSION["modifying_by"];
	$content_body = <<<New_page
	<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/skins/simple/style.css" />
	<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/sets/wiki/style.css" />
	<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/markitup/jquery.markitup.js"></script>
	<script type="text/javascript">
	function disable_right_panel_options(val){
		if (!val){
			$('#show_right_panel_toc').attr('disabled', 'disabled');
			$('#show_right_panel_tocs').attr('disabled', 'disabled');
			/*$('#right_panel_options').attr('disabled', 'disabled');*/
		} else {
			$('#show_right_panel_toc').removeAttr('disabled');
			$('#show_right_panel_tocs').removeAttr('disabled');
			/*$('#right_panel_options').removeAttr('disabled');*/
		}
	}
	function check_session(){
		var session = '$session';
		if(session.length == 0){
			/*alert(session);*/
		}
		/*$(document).keypress(function(e) {
			if(e.keyCode) {
				$.get('common/include/funcs/_ajax/session_controls.php', {user: '$decrypted_user', action: 'restore'}, function(data){alert(data);});
			}
		});*/
	}
	$(function(){
		myWikiSettings = {
			nameSpace: "wiki",
			previewParserPath: "~/templates/preview.php?page={PAGE}&user={DECRYPTED_USER}",
			previewAutoRefresh: true,
			markupSet:  [
				{name:'Grassetto', key:'B', openWith:"'''", closeWith:"'''", className:'bold'}, 
				{name:'Corsivo', key:'I', openWith:"''", closeWith:"''", className:'italic'}, 
				{name:'Sottolineato', key:'U', openWith:'__', closeWith:'__', className:'underline'}, 
				{name:'Barrato', key:'S', openWith:'@@--- ', closeWith:' @@', className:'strokethrough'}, 
				{separator:'---------------' },
				{name:'Titolo 1', key:'1', openWith:'= ', closeWith:' =', className:'h1'},
				{name:'Titolo 2', key:'2', openWith:'== ', closeWith:' ==', className:'h2'},
				{name:'Titolo 3', key:'3', openWith:'=== ', closeWith:' ===', className:'h3'},
				{name:'Titolo 4', key:'4', openWith:'==== ', closeWith:' ====', className:'h4'},
				{name:'Titolo 5', key:'5', openWith:'===== ', closeWith:' =====', className:'h5'},
				{separator:'---------------' }, 
				{name:'Elenco puntato', openWith:'(!(* |!|*)!)', className:'ul'}, 
				{name:'Elenco numerato', openWith:'(!(# |!|#)!)', className:'ol'}, 
				{separator:'---------------' },
				{name:'Crea tabella', className:'tablegenerator', placeholder:"Inserisci del testo qui",
					replaceWith:function(h) {
						var cols = prompt("Inserire il numero di colonne"),
							rows = prompt("Inserire il numero di righe"),
							html = "||";
						if (h.altKey) {
							for (var c = 0; c < cols; c++) {
								html += "! [![TH"+(c+1)+" text:]!]";
							}	
						}
						for (var r = 0; r < rows; r++) {
							if (r > 0){
								html += "||\\n||";
							}
							for (var c = 0; c < cols; c++) {
								if (c > 0){
									html += "||";
								}
								html += (h.placeholder||"");
							}
						}
						html += "||";
						return html;
					},
				className:'tablegenerator'},
				{name:'Immagine', key:'P', replaceWith:'[![Url:!:http://]!] [![name]!]', className:'image'},
				{name:'Collegamento libero', openWith:'(([![Collegamento:!:]!]|', closeWith:'))', placeHolder:'Testo del collegamento', className:'internal_link'},
				{name:'Collegamento InterWiki', openWith:'[[![Canale wiki:!:Wikipedia]!]:[![Lingua (sigla ISO_3166-1):!:it]!]:', closeWith:']', placeHolder:'Testo del collegamento', className:'interwiki_link'},
				{name:'Collegamento esterno', openWith:'[[![URI (Uniform Resource Locator):!:http://]!] ', closeWith:']', placeHolder:'Testo del collegamento', className:'external_link'},
				{separator:'---------------' },
				{name:'Citazione', openWith:'(!(> |!|>)!)', className:'quote'},
				{name:'Codice', openWith:'(!(<code type="[![Linguaggio:!:php]!]">\\n|!|<pre>)!)', closeWith:'(!(\\n</code>|!|</pre>)!)', className:'code'}, 
				{separator:'---------------' },
				{name:'Reindirizzamento', openWith:'-->[![Indirizzo (nome della pagina o url):!:]!]', className:'redirect'}, 
				{separator:'---------------' },
				{name:'Anteprima', call:'preview', className:'preview'}
			]
		};
		disableWikiSettings = {
			nameSpace: "wiki",
			previewParserPath: "~/templates/preview.php?page={PAGE}&user={DECRYPTED_USER}",
			previewAutoRefresh: false,
			markupSet:  [ ]
		};
		$('#page_content').markItUp($markitup_enabled);
		var is_modifying = '$is_modifying';
		var is_modifying_standby = '$is_modifying_standby';
		if (is_modifying_standby == '1'){
			is_modifying = '0';
		}
		if (is_modifying !== '1'){
			$('#page_title, #page_subtitle, #page_content, #reason').bind('keyup', function(){
				$('#save_page_btn').removeAttr('disabled');
			});
			$('#allow_discussions, #restrict_to_level, #show_right_panel, #show_right_panel_toc, #show_right_panel_tocs').bind('click', function(){
				$('#save_page_btn').removeAttr('disabled');
			});
		}
		/*
		$('#show_right_panel').click(function(){
			disable_right_panel_options($(this).is(':checked'));
		});
		*/
		check_session();
		$(document).ready(function(){
			$.get("common/include/funcs/_ajax/get_content_page.php", {page_id: "$content_id"}, function(data){
				$("#page_content").html(data).text();
			});
		});
	});
	</script>
	$modifying_msg
	<form action="" method="post">
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<input type="text" id="page_title" name="page_title"$disabled autofocus="autofocus" placeholder="Titolo della pagina" style="width: 99%;" value="$page_title" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="text" id="page_subtitle" name="page_subtitle"$disabled placeholder="Sottotitolo della pagina" style="width: 99%;" value="$page_subtitle" />
				</td>
			</tr>
			<tr>
				<td>
					<textarea id="page_content" name="page_content"$disabled>Caricamento dei contenuti...</textarea>
					Per inserire la propria firma (<b>Nome account</b> data e ora) basta scrivere <b><tt>{SIG}</tt></b><br />
					<b>ATTENZIONE:</b> la firma <tt>{SIG}</tt> non sarà visibile nella visualizzazione dell'anteprima, ma solo una volta salvata la pagina.<br /><br />
					Per maggiori informazioni riguardo alla formattazione consultare la <a href="Guide/Sintassi_del_wiki" title="Guida per la sintassi del Wiki" target="_blank">guida per la sintassi del Wiki</a>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" style="width: 100%;">
						<tr>
							<td valign="top">
								<fieldset>
									<legend>Permessi sulla pagina</legend>
									<br />
									<label>
										<input type="checkbox" id="allow_discussions" name="allow_discussions"$disabled {ALLOW_DISCUSSIONS} />
										Permetti le discussioni
									</label>
									<br />
									<label>
										<input type="checkbox" id="restrict_to_level" name="restrict_to_level"$disabled {RESTRICT_TO_LEVEL} />
										Visualizza solo agli utenti registrati
									</label>
									<br />
									<br />
								</fieldset>
							</td>
							<td valign="top">
								<fieldset>
									<legend>Pannello laterale destro</legend>
									<br />
									<label>
										<input type="checkbox" id="show_right_panel" name="show_right_panel"$disabled {RIGHT_PANEL_CHECKED} />
										Visualizza il pannello di destra in questa pagina
									</label>
									<br />
									<label>
										<input type="checkbox" id="show_right_panel_toc" name="show_right_panel_toc"$disabled {RIGHT_PANEL_TOC_CHECKED} />
										Visualizza la tabella dei contenuti (Indice)
									</label>
									<br />
									<br />
									<label>
										<input type="checkbox" id="show_right_panel_tocs" name="show_right_panel_tocs"$disabled {RIGHT_PANEL_TOCS_CHECKED} />
										Visualizza la tabella delle sottopagine
									</label>
									<br />
									<br />
									<!--<input type="text" id="right_panel_options" name="right_panel_options"$disabled placeholder="Funzionalit&agrave; aggiuntive nel pannello di destra (nome_dei_file.tpl separati da virgola &quot;,&quot;)" style="width: 99%;" value="" />-->
								</fieldset>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>
					<input type="text" id="reason" name="reason"$disabled placeholder="Motivo della modifica" style="width: 99%;" value="$decrypted_user ha $action la pagina" />
				</td>
			</tr>
		</table>
		<hr />
		<table cellpadding="0" cellspacing="0" style="width: 100%;">
			<tr>
				<td>
					<input type="submit" name="save_page_btn" id="save_page_btn" disabled="disabled" title="Temporanemente inattivo" value="Salva" />
				</td>
			</tr>
		</table>
	</form>
New_page;
	if ($allow_discussions == 0){
		$discussion_checkbox = "";
	} else {
		$discussion_checkbox = "checked=\"checked\"";
	}
	if ($restrict_to_level == 0){
		$level_checkbox = "";
	} else {
		$level_checkbox = "checked=\"checked\"";
	}
	if ($show_right_panel == 0){
		$right_panel = "";
	} else {
		$right_panel = "checked=\"checked\"";
	}
	if ($show_right_panel_toc == 0){
		$right_panel_toc = "";
	} else {
		$right_panel_toc = "checked=\"checked\"";
	}
	if ($show_right_panel_tocs == 0){
		$right_panel_tocs = "";
	} else {
		$right_panel_tocs = "checked=\"checked\"";
	}
	require_once("common/include/conf/replacing_object_data.php");
}
?>