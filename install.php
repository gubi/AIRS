<?php
/**
* This is a main index file for AIRS wiki
* 
*  ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~
*		Automatic Intelligent Research System											
*		This is an "Heuristically programmed Algorithmic calculator"						
*  ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~
*	"Nessun calcolatore 9000 ha mai commesso un errore o alterato un'informazione.			
*		Noi siamo, senza possibili eccezioni di sorta, a prova di errore, e incapaci di sbagliare."	
*  ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~
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
if (isset($_POST["form_submit"])) {
	header("Content-type: text/plain");
	print_r($_POST);
	exit();
}
require_once("common/include/classes/class.html_element.php");
require_once("common/include/funcs/email_verify_source.php");

$lang = "it";
require_once("common/include/i18n/" . $lang . "/i18n.install.php");
require_once("common/include/conf/install_data.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html
	version="HTML+RDFa 1.1"
	lang="<?php print $config["language"]["default_language"]; ?>"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
	xmlns:cc="http://creativecommons.org/ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:foaf="http://xmlns.com/foaf/0.1/">
	<head profile="http://www.w3.org/1999/xhtml/vocab">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta property="dc:date dc:created" content="<?php print date("Y-m-dTH:i:s"); ?>" />
		<meta rel="dc:creator" content="creator.rdf" />
		<meta rel="dc:subject" href="http://airs.inran.it/index.rdf" />

		<title property="dc:title">AIRS - Automatic Intelligent Research System | Installer</title>
		<link rel="stylesheet" href="common/css/main.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="common/css/install.css" type="text/css" media="screen" />
		
		<?php require_once("common/tpl/_javascripts/install.tpl"); ?>
		<script type="text/javascript">
		function loader(message, action){
			$("#loader_message").html(message);
			if (action == "show"){
				$("#loader").fadeIn();
			} else {
				$("#loader").fadeOut();
			}
		}
		function check() {
			var okay = false;
			$.each($("input[require]"), function(item, value) {
				if ($(this).hasClass("error")) {
					$(this).parent("td").css("borderLeft", "#cecece 3px solid").next(".row_help").css("borderLeft", "#cecece 3px solid");
					$("#" + $(this).attr("id")).removeClass("error").next().remove();
				}
				if($(this).val() == "") {
					if($("#" + $(this).attr("id") + " + span.error").length == 0){
						$(this).parent("td").css("borderLeft", "#cc0000 3px solid").next(".row_help").css("borderLeft", "#cc0000 3px solid");
						$("#" + $(this).attr("id")).addClass("error").after('<span class="error"><span>' + $(this).attr("error_txt") + '</span></span>');
					}
					if($("#" + $(this).attr("id")).val() == "") {
						$(this).focus();
						okay = false;
						return false;
					}
				} else {
					okay = true;
				}
			});
			if(okay == true) {
				// Controlla parametri mail
				loader("Controllo i parametri di connessione al database...", "show");
				var mail_check = $.check_mail("", function(arg) {
					if(arg) {
						loader("", "hide");
						var data = arg.split("::");
						if($("#mail_pop_host").val() != arg) {
							$("#mail_pop_host").attr("placeholder", arg).addClass("error").after('<span class="error">' + data[1] + "</span>").focus();
						} else {
							$("#mail_pop_host").attr("placeholder", "").removeClass("error");
							$("#mail_pop_host + span.error").remove();
						}
						if($("#mail_smtp_host").val() != arg) {
							$("#mail_smtp_host").attr("placeholder", arg).addClass("error").after('<span class="error">' + data[1] + "</span>").focus();
						} else {
							$("#mail_smtp_host").attr("placeholder", "").removeClass("error");
							$("#mail_smtp_host + span.error").remove();
						}
					}
				});
				// Controlla parametri mail
				loader("Controllo i parametri di connessione al database...", "show");
				var db_check = $.check_db("", function(arg) {
					if(arg == "ok") {
						loader("Installazione del Sistema", "show");
						$.post("http://<?php print $_SERVER["HTTP_HOST"]; ?>/common/include/funcs/_ajax/install_system.php", $("#forms form").serialize(), function(resp) {
							if(resp) {
								apprise("<h1>Il Sistema &egrave; stato installato con successo!</h1><br /><p>Attendere l'aggiornamento della pagina...</p>");
								location.reload();
							}
						});
					} else {
						var data = arg.split("::");
						apprise(data[1], {"textOk": "Ok"});
					}
				});
			}
		}
		$.extend({
			check_mail: function(options, callback) {
				loader("Controllo i parametri di connessione alla posta...", "show");
				$.post("http://<?php print $_SERVER["HTTP_HOST"]; ?>/common/include/funcs/_ajax/check_mail_settings.php", $("#Mail").serialize(), function(mail_data) {
					if (typeof callback == "function") {
						callback.call(this, mail_data);
					}
				});
			},
			check_db: function(options, callback) {
				$.post("http://<?php print $_SERVER["HTTP_HOST"]; ?>/common/include/funcs/_ajax/check_db_connection.php", $("#Database").serialize(), function(mail_data) {
					if (typeof callback == "function") {
						callback.call(this, mail_data);
					}
				});
			}
		});
		$(document).ready(function() {
			$("select").chosen({
				no_results_text: "<?php print $i18n["error_no_results_for"]; ?>"
			});
			$(".chzn-select-deselect").chosen({
				allow_single_deselect: true
			});
			$(".key").iphonePassword({mask: '•', duration: 1000});
			myWikiSettings = {
				nameSpace: "html",
				previewParserPath: "~/templates/preview.php?type=html",
				previewAutoRefresh: true,
				markupSet:  [
					{name:'<?php print $i18n["text_format"]["bold"]; ?>', key:'B', openWith:"<b>", closeWith:"</b>", className:'bold'}, 
					{name:'<?php print $i18n["text_format"]["italic"]; ?>', key:'I', openWith:"<i>", closeWith:"</i>", className:'italic'}, 
					{name:'<?php print $i18n["text_format"]["underline"]; ?>', key:'U', openWith:'<u>', closeWith:'</u>', className:'underline'}, 
					{name:'<?php print $i18n["text_format"]["strike"]; ?>', key:'S', openWith:'<strike>', closeWith:'</strike>', className:'strokethrough'}, 
					{separator:'---------------' },
					{name:'<?php print $i18n["text_format"]["heading"]; ?> 1', key:'1', openWith:'<h1>', closeWith:'</h1>', className:'h1'},
					{name:'<?php print $i18n["text_format"]["heading"]; ?> 2', key:'2', openWith:'<h2>', closeWith:'</h2>', className:'h2'},
					{name:'<?php print $i18n["text_format"]["heading"]; ?> 3', key:'3', openWith:'<h3>', closeWith:'</h3>', className:'h3'},
					{name:'<?php print $i18n["text_format"]["heading"]; ?> 4', key:'4', openWith:'<h4>', closeWith:'</h4>', className:'h4'},
					{name:'<?php print $i18n["text_format"]["heading"]; ?> 5', key:'5', openWith:'<h5>', closeWith:'</h5>', className:'h5'},
					{separator:'---------------' },
					{name:'<?php print $i18n["text_format"]["link"]; ?>', openWith:'<a href="[![<?php print $i18n["text_format"]["link"]; ?>:!:http://]!]">', closeWith:'</a>', placeHolder:'<?php print $i18n["text_format"]["link_placeholder"]; ?>', className:'external_link'},
					{separator:'---------------' },
					{name:'<?php print $i18n["text_format"]["preview"]; ?>', call:'preview', className:'preview'}
				]
			};
			$('.html').markItUp(myWikiSettings);
			$("input[type=checkbox]").switchbutton({
				checkedLabel: '<?php print $i18n["yes"]; ?>',
				uncheckedLabel: '<?php print $i18n["no"]; ?>',
				classes: "ui-switchbutton-thin"
			}).change(function(){
				// Trasformazione automatica http:// in https:// in base a checkbox "system_need_ssl"
				if($(this).attr("id") == "system_need_ssl") {
					var replace_from = (!$(this).prop("checked") ? "https://" : "http://"),
					replace_to = (!$(this).prop("checked") ? "http://" : "https://");
					$.each($("input[type=url], textarea"), function(e, item) {
						$(this).val($(this).val().replace(new RegExp(replace_from, "g"), replace_to));
					});
				}
			});
			$("#mail_pop_username").keyup(function() {
				$("#mail_smtp_username").val($(this).val());
			});
		});
		$(document).keyup(function(e) {
			if (e.keyCode == 27) { loader("", "hide"); } /* ESC */
		});
		</script>
	</head>
	<body>
		<div id="loader"><div id="loader_message"></div></div>
		<div id="header">
			<h1><?php print $i18n["form_install_title"]; ?></h1>
			<?php require_once("common/tpl/_airs_logo.tpl"); ?>
		</div>
		<!--div id="right_guide"></div-->
		<div id="container">
			<?php
			if (!isset($_POST["form_submit"])) {
				?>
				<div id="forms">
					<?php
					$br = new Element("br");
					$link = new Element("a");
					$span = new Element("span", array("class" => "left"));
					$span2 = new Element("span");
					$small = new Element("small");
					$form = new Element("form", array("action" => "", "method" => "post", "onsubmit" => "check(); return false;"));
					$submit = new Element("button", array("type" => "submit", "class" => "btn", "name" => "proceed", "text" => "Installa &rsaquo;"));
					
					foreach($fieldset_arr as $i => $element){
						$fieldset[$i] = new Element("fieldset");
						$fieldset[$i]->set("id", $element["legend"]);
							$anchor[$i] = new Element("a", array("text" => $element["legend"], "href" => "#" . $element["legend"]));
							$span->clear()->set("class", "left");
							$span->nest($anchor[$i]);
						$legend[$i] = new Element("legend");
						$table[$i] = new Element("table", array("cellpadding" => "0", "cellspacing" => "0"));
						
						for($ftr = 0; $ftr < $element["table"]["tr"]; $ftr++) {
							$tr[$ftr] = new Element("tr");
							$th[$i][$ftr] = new Element("th", array("valign" => "top", "text" => $element["table"]["th"][$ftr]));
							$td[$i][$ftr] = new Element("td");
							$td2[$i][$ftr] = new Element("td", array("class" => "row_help", "text" => $element["table"]["help"][$ftr]));
							foreach($element["table"]["td"] as $k => $v) {
								foreach($v as $kk => $vv) {
									if($kk == "element"){
										$el_type[$i][$k] = new Element($vv);
									} else {
										if(!is_array($vv)){
											$el_type[$i][$k]->set($kk, $vv);
										} else {
											foreach($vv as $kkk => $vvv) {
												foreach($vvv as $kkkk => $vvvv) {
													$el_type[$i][$k]->nest(new Element($kkk, $vvvv));
												}
											}
										}
									}
								}
							}
							$td[$i][$ftr]->nest($el_type[$i][$ftr]);
							
							$tr[$ftr]->nest($th[$i][$ftr]);
							$tr[$ftr]->nest($td[$i][$ftr]);
							$tr[$ftr]->nest($td2[$i][$ftr]);
							$table[$i]->nest($tr[$ftr]);
						}
						$legend[$i]->nest($span);
						$fieldset[$i]->nest($legend[$i]);
						$fieldset[$i]->nest($table[$i]);
						$form->nest($fieldset[$i]);
					}
					print $form;
					?>
				</div>
				<?php
			} else {
				?>
				<div id="loader"></div>
				<?php
			}
			?>
		</div>
	</body>
</html>