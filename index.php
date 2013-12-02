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
$config_file = "common/include/conf/airs.conf";

try {
	if (($config = @parse_ini_file($config_file, true)) == false) {
		require_once("install.php");
		exit();
	}
}
catch (Exception $e) {
	die($e->getMessage());
}
ob_start("compress_html");
foreach($config as $configk => $configv){foreach($configv as $configkk => $configvv){ $config[$configk][$configkk] = utf8_encode($configvv); }}

// Generate RSA key
if(!file_exists("common/include/conf/rsa_2048_priv.pem")) {
	shell_exec('openssl genrsa -out common/include/conf/rsa_2048_priv.pem 2048');
	if(!file_exists("common/include/conf/rsa_2048_pub.pem")) {
		shell_exec('openssl rsa -pubout -in common/include/conf/rsa_2048_priv.pem -out common/include/conf/rsa_2048_pub.pem');
	}
}
setlocale(LC_ALL, $config["language"]["default_locale"]);

$load_time = microtime(true);
ob_start("compress_html");
$load_time = microtime(true);

require_once("common/include/conf/Wiki/all_pages/globals_definitions.php");
require_once("common/include/.mysql_connect.inc.php");
require_once("common/include/classes/class.load_i18n.php");
require_once("common/include/classes/class.rsa.php");
require_once("common/include/funcs/get_link.php");
require_once("common/include/funcs/translate.php");
require_once("common/include/funcs/_converti_data.php");

$rsa = new rsa();
$fb = fopen("common/include/conf/rsa_2048_pub.pem", "r");
$Skey = fread($fb, 8192);
fclose($fb);
$Stoken = $rsa->get_token($Skey);
$rsa_encrypted = $rsa->simple_private_encrypt($Stoken);

if (isset($_GET["q"]) && trim($_GET["q"]) !== ""){
	$this_page = urldecode($_GET["q"]);
	$GLOBALS["check_table"] = "sub_subname";
} else {
	if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
		$this_page = urldecode($_GET["id"]);
			$GLOBALS["check_table"] = "subname";
	} else {
		if (isset($_GET["m"]) && trim($_GET["m"]) !== ""){
			$this_page = urldecode($_GET["m"]);
			$GLOBALS["check_table"] = "name";
		}
	}
}
if (strpos($this_page, ":") == true){
	$GLOBALS["symbol"] = ":";
	$part = 1;
	$function_part = 0;
	$GLOBALS["is_functioned"] = true;
} else if (strpos($this_page, "~") == true){
	$GLOBALS["symbol"] = "~";
	$part = 0;
	$function_part = 0;
	$GLOBALS["function_part"] = "";
	$GLOBALS["is_functioned"] = true;
} else {
	$GLOBALS["is_functioned"] = false;
}
if ($GLOBALS["is_functioned"] == true){
	$dati = explode($GLOBALS["symbol"], $this_page);
	$GLOBALS["page"] = $dati[$part];
	$GLOBALS["function"] = $dati[$function_part];
	$GLOBALS["function_part"] = $dati[$function_part];
	$GLOBALS["function_page"] = $GLOBALS["function"] . $GLOBALS["symbol"] . $GLOBALS["page"];
} else {
	$GLOBALS["page"] = $this_page;
	$GLOBALS["function_page"] = $GLOBALS["page"];
}
$GLOBALS["page_m"] = str_replace(array($GLOBALS["function_part"], $GLOBALS["symbol"]), "", $_GET["m"]);
$GLOBALS["page_id"] = str_replace(array($GLOBALS["function_part"], $GLOBALS["symbol"]), "", $_GET["id"]);
if ($GLOBALS["page_m"] == $i18n["user_string"] && isset($_GET["id"]) || $GLOBALS["page_m"] == "Tags"){
	//$GLOBALS["page_id"] = "{1}";
}
$GLOBALS["page_q"] = str_replace(array($GLOBALS["function_part"], $GLOBALS["symbol"]), "", $_GET["q"]);
if ($GLOBALS["page_q"] == ""){
	if ($GLOBALS["page_id"] == ""){
		$GLOBALS["page_level"] = "name";
		$GLOBALS["page_level_key"] = "m";
		$GLOBALS["page_last_level"] = "";
		$GLOBALS["page_last_level_type"] = "";
		$GLOBALS["page_next_level"] = "subname";
		$GLOBALS["page_next_level_type"] = "page_id";
	} else {
		$GLOBALS["page_level"] = "subname";
		$GLOBALS["page_level_key"] = "id";
		$GLOBALS["page_last_level"] = "name";
		$GLOBALS["page_last_level_type"] = "page_m";
		$GLOBALS["page_next_level"] = "sub_subname";
		$GLOBALS["page_next_level_type"] = "page_q";
	}
} else {
	$GLOBALS["page_level"] = "sub_subname";
	$GLOBALS["page_level_key"] = "q";
	$GLOBALS["page_last_level"] = "subname";
	$GLOBALS["page_last_level_type"] = "page_id";
	$GLOBALS["page_next_level"] = "";
}
if (isset($_GET["m"]) && trim($_GET["m"]) !== ""){
	$GLOBALS["redirect"] = $GLOBALS["page_m"];
	if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
		$GLOBALS["redirect"] .= "/" . $GLOBALS["page_id"];
		if (isset($_GET["q"]) && trim($_GET["q"]) !== ""){
			$GLOBALS["redirect"] .= "/" . $GLOBALS["page_q"];
		}
	}
}
if (trim($GLOBALS["page"]) == ""){
	if ($GLOBALS["is_functioned"] == true && $GLOBALS["function"] == $i18n["search_string"]){
		$GLOBALS["page"] = $GLOBALS["page_html_title"] = $i18n["page_title_search"];
	} else {
		$GLOBALS["page"] = $i18n["page_name_main"];
		$GLOBALS["page_html_title"] = $i18n["page_title_main"];
	}
} else {
	if ($GLOBALS["is_functioned"] == true && $GLOBALS["function"] == $i18n["search_string"]){
		$GLOBALS["page_html_title"] = $i18n["research_string"] . ": \"" . str_replace("_", " ", $GLOBALS["page"]) . "\"";
	} else {
		if (is_numeric($GLOBALS["page"])){
			if ($GLOBALS["is_functioned"] == true){
				$GLOBALS["page_html_title"] = $i18n["page_title_edit"] . " " . str_replace("_", " ",  $GLOBALS[$GLOBALS["page_last_level_type"]]);
			} else {
				$GLOBALS["page_html_title"] = $i18n["page_title_edit"] . " " . str_replace("_", " ",  $GLOBALS[$GLOBALS["page_last_level_type"]]);
			}
		} else {
			$GLOBALS["page_html_title"] = str_replace("_", " ", $GLOBALS["page"]);
		}
	}
}
$GLOBALS["page_title"] = str_replace("_", " ", $GLOBALS["page"]);
	
	// Referer page
	$GLOBALS["referer_page"] = str_replace($absolute_path, "", $_SERVER["HTTP_REFERER"]);
	
	$no_referer_pages = array($i18n["page_name_special_login"], $i18n["page_name_special_logout"]);
	if (in_array($GLOBALS["referer_page"], $no_referer_pages)){
		$GLOBALS["referer_page"] = $GLOBALS["function_page"];
	}
	$GLOBALS["referer_page_title"] = str_replace("_", "_", $GLOBALS["referer_page"]);

$pdo = db_connect("");
$GLOBALS["pdo"] = $pdo;
require_once("common/include/funcs/login.php");

if (trim($GLOBALS["user_level"]) == ""){
	$GLOBALS["user_level"] = 0;
}

$link = $http . $page_uri;
$referringPage = parse_url($link);
$parsed_query = $referringPage["path"];
//parse_str($parsed_query, $queryVars); // Solo in caso di .htaccess non funzionante
$queryVars = explode("/", $parsed_query);
	if (in_array($_SERVER["HTTP_HOST"], $queryVars)){
		unset($queryVars[array_search($_SERVER["HTTP_HOST"], $queryVars)]);
	}
	unset($queryVars[array_search("", $queryVars)]);
	if (trim($queryVars[0]) == ""){
		array_shift($queryVars);
	}
//print_r($queryVars);
//print trim($queryVars[count($queryVars)-1]);
$q_counter = 0;
foreach ($queryVars as $k => $v){
	if(strlen(trim($v)) > 0){
		$q_counter++;
	}
	if($q_counter < count($queryVars) + 1){
		$page_link .= $v . "/";
	}
	$v_functioned = $v;
	if ($GLOBALS["function"] !== $i18n["search_string"]){
		$v = str_replace($GLOBALS["function_part"] . ":", "", $v);
	} else {
		if (strlen(str_replace($GLOBALS["function_part"] . ":", "", $v)) > 0){
			$v = str_replace($i18n["search_string"] . ":", $i18n["research_string"] . ": \"", $v) . "\"";
		} else {
			$v = $i18n["page_title_search"];
		}
	}
	if ($q_counter > 0 && $q_counter < count($queryVars) - 1){
		if (!is_numeric($v)){
			$page_a .= "<li><a href=\"" . substr($page_link, 0, -1) . "\" title=\"" . $i18n["go_to_page"] . " &quot;" . unget_link($v) . "&quot;\">" . unget_link($v) . "</a></li>";
		}
	} else {
		if($q_counter >= 1 && $q_counter < count($queryVars) && trim($v) !== ""){
			$page_a .= "<li>" . unget_link(urldecode($v)) . "</li>";
		} else {
			$page_a .= "";
		}
	}
	$GLOBALS["current_pos"] .= $v;
	$GLOBALS["current_pos_functioned"] .= $v_functioned;
	
	if ($q_counter < count($queryVars)){
		$GLOBALS["current_pos"] .= "/";
		$GLOBALS["current_pos_functioned"] .= "/";
	}
}
require_once("common/include/funcs/redirects.php");
/*
if (isset($_POST["username"]) && trim($_POST["username"]) !== "" && !isset($_COOKIE["iac"])){
	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
	header("Refresh: 1;URL=" . $GLOBALS["page_uri"]);
	print "Redirezionamento in corso.<br />Attendere...";
	exit();
}
*/
?>
<!DOCTYPE html>
<html version="HTML+RDFa 1.1" lang="<?php print $config["language"]["default_language"]; ?>"
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
	<meta rel="dc:creator" content="<?php print $absolute_path . "creator.rdf"; ?>" />
	<meta rel="dc:subject" href="http://en.wikipedia.org/wiki/Semantic_web" />

	<title property="dc:title"><?php print $GLOBALS["page_html_title"]; ?> - AIRS - Automatic Intelligent Research System</title>
	
	<base href="<?php print $absolute_path; ?>" />
	<meta name="Author" content="Alessandro Gubitosi" />
	<meta name="Description" content="<?php print $config["company"]["name"]; ?> - AIRS (Automatic Intelligent Research System)" />
	<!--<meta name="Copyright" content="&copy; <?php print date("Y"); ?> INRAN" />-->
	<meta http-equiv="Expires" content="0">
	<meta name="Robots" content="#" />
	<meta name="Generator" content="#" />
	<meta name="Keywords" content="#" />
	<!--<meta http-equiv="expires" content="0" />-->
	
	<link rel="shortcut icon" href="<?php print $absolute_path; ?>common/media/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php print $absolute_path; ?>common/media/favicon.png" type="image/png" />

	<link rel="stylesheet" href="<?php print $absolute_path; ?>common/css/main.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php print $absolute_path; ?>common/css/print.css" type="text/css" media="print" />
	
	<link rel="search" type="application/opensearchdescription+xml" title="<?php print $config["company"]["name"]; ?> - AIRS" href="<?php print $absolute_path; ?>common/media/osd.xml" />
	<!--[if IE]>
	<link rel="stylesheet" href="/css/main_win.css" type="text/css" media="screen, projection" />
	<![endif]-->
	
	<!-- jquery-->
	<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery_ui_effects/ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery_ui_effects/ui/jquery.effects.core.js"></script>
	
		<!-- Jquery ScrollTo -->
		<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery.scrollTo-1.4.2.js"></script>
		<!-- Jquery copy -->
		<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery_copy/jquery.copy.js"></script>
		<!-- iPhone-password -->
		<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/iphone-password/jquery.iphone.password.js" charset="utf-8"></script>
		<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/iphone-password/caret.js" charset="utf-8"></script>
		<!-- Apprise -->
		<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/apprise/apprise-1.5_edited.js" charset="utf-8"></script>
		<link rel="stylesheet" href="<?php print $absolute_path; ?>common/js/apprise/apprise.min.css" type="text/css" media="screen" />
		<?php
		if (strtolower($GLOBALS["function_part"]) == "file" && $GLOBALS["user_level"] > 0){
			?>
			<link rel="stylesheet" href="<?php print $absolute_path; ?>common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
			<link rel="stylesheet" href="<?php print $absolute_path; ?>common/js/jquery_file_upload/jquery.fileupload-ui.css">
			<?php
		}
		?>
		<!-- qTip -->
		<link type="text/css" rel="stylesheet" href="<?php print $absolute_path; ?>common/js/qTip/jquery.qtip.css" />
		<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/qTip/jquery.qtip.js"></script>
		<!-- Zoombox -->
		<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/zoombox/zoombox.js"></script>
		<link href="<?php print $absolute_path; ?>common/js/zoombox/zoombox.css" rel="stylesheet" type="text/css" media="screen" />
		<!-- Chosen -->
		<link href="<?php print $absolute_path; ?>common/js/chosen/chosen.css" rel="stylesheet" type="text/css">
		<script src="<?php print $absolute_path; ?>common/js/chosen/chosen.jquery.js" type="text/javascript" charset="utf-8"></script>
		
		<!-- jQuery notify -->
		<link href="<?php print $absolute_path; ?>common/js/Gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css">
		<script src="<?php print $absolute_path; ?>common/js/Gritter/js/jquery.gritter.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
	/*
	/*
	# Sostituzione degli alerts con Apprise
	window.__oldAlert__ = window.alert;
	window.alert = function () {
		apprise.apply(this, arguments);
	};
	*/
	$.extend({
		request_passphrase: function(v, error, callback) {
			if (v == undefined){
				v = 0;
			}
			v++;
			
			if (v > 3){
				location.reload();
			} else {
				if (v > 1 || error == 1){
					var error_msg = "<br /><span class=\"error\"><?php print $i18n["error_inserted_key_is_wrong"]; ?></span><br />";
				} else {
					var error_msg = "";
				}
				console.warn((3-v) + " attempts");
				apprise("<h1>Ibernazione del sistema</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/document_sans_security_128_ccc.png\" /></td><td valign=\"top\" class=\"appriseInnerContent\"><?php print $i18n["error_session_expired_system_hibernate"]; ?>.<br /><?php print $i18n["session_expired_message_description"]; ?>.<br /><br /><?php print $i18n["error_session_expired_insert_passphrase"]; ?><br /><a href=\"<?php print $i18n["page_name_safety"]; ?>/<?php print $i18n["page_name_encryption_key"]; ?>\" target=\"_blank\"><?php print $i18n["more_info_about_encryption_key"]; ?></a><br />" + error_msg + "</td></tr></table><br /><br />", {"key": true}, function(r){
					$("*").attr("readonly", "readonly");
					$(".markItUpButton").css({visibility: "visible"});
					if(r){
						$("*").removeAttr('readonly');
					} else {
						check_login_expiry(username);
						setTimeout(function(){
							$.request_passphrase(v, 1);
						}, 250);
					}
					if(typeof callback == 'function'){
						callback.call(this, r);
					}

				});
			}
		}
	});
	<?php if (isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== ""){ ?>
	time_count = 0;
	function check_login_expiry(username){
		if (username.length > 0){
			var x = setTimeout(function(){
				time_count += 1;
				$.get("common/include/funcs/_ajax/check_login_expiry.php", {user: username, page: '<?php print $GLOBALS["page_m"]; ?>', subpage: '<?php print $GLOBALS["page_id"]; ?>', sub_subpage: '<?php print $GLOBALS["page_q"]; ?>'}, function(data){
					if (data == "expired"){
						console.log("Session time expired\nSystem is in hibernation");
						$.request_passphrase(0, 0, function(r){
							clearTimeout(x);
							if(r){
								$.get("common/include/funcs/_ajax/re_login.php", {u: '<?php print $decrypted_user; ?>', k1: $("#ukey").val(), k2: r, page: '<?php print $GLOBALS["page_m"]; ?>', subpage: '<?php print $GLOBALS["page_id"]; ?>', sub_subpage: '<?php print $GLOBALS["page_q"]; ?>'}, function(data){
									if (data !== "allowed"){
										console.warn("Wrong passphrase");
										check_login_expiry(username);
									} else {
										console.warn("Session resumed by user input");
										check_login_expiry(username);
									}
								});
							} else {
								check_login_expiry(username);
							}
						});
					} else {
						$(".aOverlay").fadeOut(300);
						$(".appriseOuter").fadeOut(300);
						check_login_expiry(username);
					}
				});
				if($(".flexigrid_autorefresh").length > 0){
					$(".flexigrid_autorefresh").flexReload();
				}
				if($(".flexigrid_long_autorefresh").length > 0){
					/* 300 = 5 minuti */
					if((time_count%300) == 0){
						$(".flexigrid_long_autorefresh").flexReload();
					}
				}
			}, 10000);
		}
	}
	<?php } ?>
	function loader(message, action){
		$("#loader_message").html(message);
		if (action == "show"){
			$("#loader").fadeIn();
		} else {
			$("#loader").fadeOut();
		}
	}
	function show_cloud(type, notype){
		if (notype == undefined){
			var notype = "";
		}
		var content = $("#" + type).html();
		$("." + type).hover(function(){
			var timeout = $(this).data("timeout");
			if(timeout) { clearTimeout(timeout); }
			
			if ($("#" + type + "_cloud").length == 0){
				$("#content_right_panel").append("<div class=\"cloud\" id=\"" + type + "_cloud\" style=\"display: none;\">" + content + "</div>");
				if ($("#" + notype).length > 0){
					$(".cloud").css({marginTop: "+76px"});
				}
				$(".cloud").fadeIn(900).find("#" + type + "_content").css({opacity: "1", display: "none"}).slideDown(300).hover(function(){
					if(timeout) { clearTimeout(timeout); }
				});
			}
		}, function(){
			$(this).data("timeout", setTimeout($.proxy(function(){
				$(".cloud").find("#" + type + "_content").slideUp(600, function(){
					$(this).parent().fadeOut(600, function(){ $(this).remove(); });
				});
			}, this), 1200));
		});
	}
	function show_hide_lateral_panel(){
		if ($("#panel_btn").hasClass("horizontal")){ /* Chiude il pannello*/
			$("#panel_btn").switchClass("horizontal", "vertical", 150);
			$("#toggle_btn").switchClass("close", "open", 150);
			$("#content_right_panel").switchClass("opened", "closed", 150, function(){
				if ($("#content_right_panel > #tocs").length > 0){
					$("#toc_cloud").hide();
					if ($("#tocs_btn").length == 0){
						var tocs_content = $("#tocs").html();
						$("<li><a id=\"tocs_btn\" href=\"javascript: void(0);\" class=\"tocs\" title=\"<?php print $i18n["subpages_string"]; ?>\"></a></li>").insertAfter(".sep");
					}
				}
				if ($("#content_right_panel > #toc").length > 0){
					$("#tocs_cloud").hide();
					if ($("#toc_btn").length == 0){
						var toc_content = $("#toc").html();
						$("<li><a id=\"toc_btn\" href=\"javascript: void(0);\" class=\"toc\" title=\"<?php print $i18n["index_string"]; ?>\"></a></li>").insertAfter(".sep");
					}
				}
				$(".pdf").parent().before("<li class=\"sep\"></li>");
			});
			$("#content_right_panel div").fadeOut(300);
		} else {
			$.scrollTo(0, 600);
			$("ul#panel_btn.vertical").delay(300).css({top: "194px"});
			$("#panel_btn").switchClass("vertical", "horizontal", 150);
			$("#toggle_btn").switchClass("open", "close", 150);
			$("#content_right_panel").switchClass("closed", "opened", 150);
			$("#content_right_panel div").fadeIn(300);
			
			if ($("#toc").length > 0){
				if ($("#toc_btn").length > 0){
					$("#toc_btn").parent().remove();
				}
			}
			if ($("#tocs").length > 0){
				if ($("#tocs_btn").length > 0){
					$("#tocs_btn").parent().remove();
				}
			}
			$(".pdf").parent().prev(".sep").remove();
		}
	}
	function dynamic_content(dynamic){
		var title = $("#content_wrapper_title").find("h1 > span.title").text();
		$("#content_wrapper_main_content").slideUp(1200, function(){
			$(this).prepend(title);
		});
		/*$("#content_wrapper_dynamic_content").html(dynamic).slideDown(1200);*/
	}
	$(function(){
		$.getScript("common/js/html5test.js", function(){
			var obj = jQuery.parseJSON(html5test());
			if(obj.supportInputAutofocus){
				/*
				if (window.location != "https://85.18.206.117/Modifica:401"){
					location.href ="401";
				}
				alert(window.location);
				*/
			}
		});
		<?php if (isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== ""){ ?>
		check_login_expiry('<?php print $decrypted_user; ?>');
		<?php } ?>
		$(".key").iphonePassword({mask: 'â€¢', duration: 1000});
		$("#key").iphonePassword({mask: 'â€¢', duration: 1000});
		/* Da fixare
		dynamic_content("ok")*/
	});
	$(document).ready(function() {
		show_cloud("toc");
		show_cloud("tocs", "toc");
		$(window).scroll(function(e){
			var yscroll = 194 - $(window).scrollTop();
			if (yscroll <= 10){
				yscroll = 10;
				if ($("#panel_btn").hasClass("horizontal")){
					/*
					$("#panel_btn").switchClass("horizontal", "vertical", 150);
					$("#toggle_btn").switchClass("close", "open", 150);
					$("#content_right_panel").switchClass("opened", "closed", 150, function(){
						$("#panel_btn.vertical").css({top: "10px"});
						if ($("#content_right_panel > #tocs").length > 0){
							$("#toc_cloud").hide();
							if ($("#tocs_btn").length == 0){
								var tocs_content = $("#tocs").html();
								$("<li><a id=\"tocs_btn\" href=\"javascript: void(0);\" class=\"tocs\" title=\"<?php print $i18n["subpages_string"]; ?>\"></a></li>").insertAfter(".sep");
							}
						}
						if ($("#content_right_panel > #toc").length > 0){
							$("#tocs_cloud").hide();
							if ($("#toc_btn").length == 0){
								var toc_content = $("#toc").html();
								$("<li><a id=\"toc_btn\" href=\"javascript: void(0);\" class=\"toc\" title=\"<?php print $i18n["index_string"]; ?>\"></a></li>").insertAfter(".sep");
							}
						}
						$(".pdf").parent().before("<li class=\"sep\"></li>");
					});
					$("#content_right_panel div").fadeOut(300);
					*/
				} else {
					$("ul#panel_btn.vertical").css({top: "10px"});
				}
			}
			$("ul#panel_btn.vertical").css({top: yscroll + "px"});
		});
		/* qTip integration */
		$("a[title], div[title], span[title], td[title], acronym[title], img[alt]").qtip({
			style: {
				classes: "ui-tooltip-dark"
			},
			position: {
				my: "bottom center",
				at: "top center",
				target: false,
				container: false,
				viewport: $(window),
				adjust: {
					method: "flip",
					x: parseInt(0, 10) || 0,
					y: parseInt(0, 10) || 0
				},
				effect: true
			}
		});
		$("select").chosen({
			no_results_text: "<?php print $i18n["error_no_results_for"]; ?>"
		});
		$(".chzn-select-deselect").chosen({
			allow_single_deselect: true
		});
		$("#feedback_btn").click(function(){
			var page_uri = "<?php print $GLOBALS["page_uri"]; ?>";
			apprise("<h1><?php print $i18n["feedback_send_us_feedback"]; ?>!</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/bug_beetle_art.png\" /></td><td valign=\"top\" class=\"appriseInnerContent\"><?php print $i18n["feedback_send_us_feedback_message"]; ?></td></tr></table>", {"textarea": true, "textOk": "Invia"}, function(r){
				if(r){
					$.get("common/include/funcs/_ajax/send_feedback.php", {user: "<?php print $decrypted_user; ?>", comment: encodeURI(r), page: encodeURI(page_uri)}, function(data){
						if(data == "OK"){
							apprise("<h1><?php print $i18n["thanks_string"]; ?>!</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/smile_128_ccc.png\" /></td><td valign=\"top\" class=\"appriseInnerContent\"><?php print $i18n["feedback_message_sent"]; ?></td></tr></table>", {"noButton": false});
						}
					});
				}
			});
		});
		$('input[type="password"]').keypress(function(e) {
			var s = String.fromCharCode(e.which);
			if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
				var unique_id = $.gritter.add({
					title: "<?php print $i18n["digits_warnig_string"]; ?>",
					text: "<?php print $i18n["digits_warnig_blockmaiusc_string"]; ?>",
					image: "common/media/img/caps.png",
					sticky: false,
					time: "6000",
					class_name: "my-sticky-class"
				});
			}
		});
	});
	/* Listener pressione dei tasti */
	$(document).keyup(function(e) {
		if (e.keyCode == 27) { loader("", "hide"); } /* ESC */
	});
	var PAGE = "<?php print $GLOBALS["page"]; ?>";
	var PAGE_M = "<?php print $GLOBALS["page_m"]; ?>";
	var PAGE_ID = "<?php print $GLOBALS["page_id"]; ?>";
	var PAGE_Q = "<?php print $GLOBALS["page_q"]; ?>";
	var USER = "<?php print $decrypted_user; ?>";
	</script>
</head>
<body>
	<div id="background"></div>
	<div id="loader"><div id="loader_message"></div></div>
	<?php if (isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== ""){ ?>
		<input type="hidden" id="ukey" value="<?php print urlencode($crypted_user_key); ?>" />
		<input type="hidden" id="focus_backup" value="" />
	<?php } ?>
	<div id="container">
		<div id="top_menu">
			<?php require_once("common/tpl/top_menu.tpl"); ?>
		</div>
		<div id="header">
			<?php require_once("common/tpl/_airs_logo.tpl"); ?>
		</div>
		<div id="menu">
			<?php $menu_position = "left"; require("common/tpl/menu.tpl"); ?>
		</div>
		<div id="content">
			<table id="function_menu" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php require_once("common/tpl/page_menu.tpl"); ?>
					</td>
					<td id="search" valign="bottom">
						<form id="search_form" action="" method="post" name="search">
							<input type="search" name="cerca" id="inputfield" accesskey="s" placeholder="Cerca nel sito..." />
							<input type="submit" style="display: none;" value="Cerca" />
						</form>
					</td>
				</tr>
			</table>
			<div id="main_content">
				<table id="breadcrumb" cellpadding="0" cellspacing="0">
					<tr>
						<td><?php require_once("common/tpl/breadcrumb.tpl"); ?></td>
						<td></td>
					</tr>
				</table>
				<div id="content_body">
					<?php
					require_once("common/include/get_content.php");
					// Rinnova la variabile $pdo (sovrariscritta in alcuni script)
					$pdo = db_connect("");
					?>
				</div>
				<div id="footer">
					<img src="<?php print $absolute_path; ?>common/media/img/logo_81_fff.png" style="float: right; margin: 10px 10px 0 0; width: 75px;" />
					<?php $menu_position = "footer"; require("common/tpl/menu.tpl"); ?>
					<div id="data">
						<?php print $config["company"]["name"] . " :: " . $config["company"]["address"] . " :: " . $config["company"]["contacts"]; ?>
						<br />
						<a href="<?php print $config["company"]["uri"]; ?>"><?php print $config["company"]["url"]; ?></a>
						<br />
						<p style="padding: 5px 7.5px 5px 0; border-right: #a8c9cf 2px solid; margin-right: 2.5px;">
							<?php print $config["company"]["license_html"]; ?>
						</p>
						<br />
						<i><?php print str_replace("{1}", round(microtime(true) - $load_time, 2), $i18n["page_generated_string"]); ?></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<?php
ob_end_flush();
function compress_html($compress){
	$i = array('/\>\n+/', '/\t+/', '/\r+/', '/\n+/');
	$ii = array('>', '', '', ' ');
	$caratteri_a = array("Ã ", "Ã¨", "Ã©", "Ã¬", "Ã²", "Ã¹", "Ã€", "Ãˆ", "Ã‰", "ÃŒ", "Ã’", "Ã™");
	//$caratteri_b = array("a`", "e`", "e`", "i`", "o`", "u`", "A`", "E`", "I`", "O`", "U`");
	$caratteri_c = array("à", "è", "é", "ì", "ò", "ù", "À", "È", "É", "Ì", "Ò", "Ù");
	$charset = array("&agrave;", "&egrave;", "&eacute;", "&igrave;", "&ograve;", "&ugrave;", "&Agrave;", "&Egrave;", "&Eacute;", "&Igrave;", "&Ograve;", "&Ugrave;");
	return str_replace($caratteri_a, $charset, str_replace($caratteri_b, $charset, str_replace($caratteri_c, $charset, preg_replace($i, $ii, $compress))));
}
?>