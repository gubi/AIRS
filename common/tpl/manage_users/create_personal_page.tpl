<?php
/**
* Generates form for create personal page
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

require_once("common/include/lib/gnu_pgp/gnupgp.class.php");
function explode_key($key_Array) {
	for($i = 0; $i < count($key_Array); $i++){
		$tmp = explode(":", $key_Array[$i]);
	}
	return $tmp;
}
function get_key_length($key_Array){
	$tmp = explode_key($key_Array);
	if(strlen($tmp[2]) > 0) {
		return $tmp[2];
	}
}
function get_key_id($key_Array) {
	$tmp = explode_key($key_Array);
	if(strlen($tmp[4]) > 0) {
		return $tmp[4];
	}
}

require_once("EasyRDF/autoload.php");
EasyRdf_Namespace::set("admin", "http://webns.net/mvcb/");
EasyRdf_Namespace::set("bibo", "http://purl.org/ontology/bibo/");
EasyRdf_Namespace::set("cc", "http://web.resource.org/cc/");
EasyRdf_Namespace::set("dc", "http://purl.org/dc/elements/1.1/");
EasyRdf_Namespace::set("dcterms", "http://purl.org/dc/terms/");
EasyRdf_Namespace::set("foaf", "http://xmlns.com/foaf/0.1/");
EasyRdf_Namespace::set("geo", "http://www.w3.org/2003/01/geo/wgs84_pos#");
EasyRdf_Namespace::set("org", "http://www.w3.org/ns/org#");
EasyRdf_Namespace::set("owl", "http://www.w3.org/2002/07/owl#");
EasyRdf_Namespace::set("rdf", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
EasyRdf_Namespace::set("rdfs", "http://www.w3.org/2000/01/rdf-schema#");
EasyRdf_Namespace::set("rsa", "http://www.w3.org/ns/auth/rsa#");
EasyRdf_Namespace::set("wot", "http://xmlns.com/wot/0.1/");
EasyRdf_TypeMapper::set('wot:PubKey', 'Model_PubKey');
EasyRdf_TypeMapper::set('geo:Point', 'Model_GeoPoint');

$foaf_uri = $config["system"]["default_host_uri"] . ucfirst($i18n["user_string"]) . "/" . ucfirst($GLOBALS["page_id"]) . "/foaf.rdf";

if(isset($_POST["personal_data_btn"])) {
	foreach($_POST as $k => $v) {
		if(is_array($v)) {
			//print $k . ": (<br />";
			foreach ($v as $kv => $vv) {
				//print "&emsp;" . $kv . "<br />";
				foreach ($vv as $kvv => $vvv) {
					//print "&emsp;&emsp;" . $kvv . " ~ " . $vvv . "<br />";
				}
			}
			//print ")<br />";
		} else {
			//print $k . " ~ " . $v . "<br />";
		}
	}
	$generatorAgent = $config["system"]["default_host_uri"] . "Sistema/Genera_foaf";
	$errorReportsTo = "mailto:" . $cofig["mail"]["Errors-To"];
	$foaf_name = $user_name . " " . $user_lastname;
	$foaf_givenname = $user_name;
	$foaf_family_name = $user_lastname;
	if($_POST["user_sex"] !== "na") {
		$gender = "<foaf:gender>" . (($_POST["user_sex"] == "m") ? "male" : "female") . "</foaf:gender>";
	}
	$foaf_dateOfBirth = $_POST["user_birthdate"];
	$foaf_nick = ucfirst($decrypted_user);
	$foaf_mbox = "mailto:" . $user_email;
	$foaf_mbox_sha1 = sha1($user_email);
	$foaf_homepage = $_POST["user_homepage"];
	$foaf_weblog = $_POST["user_blog"];
	$foaf_phone = "tel:" . $_POST["user_phone"];
	if(strlen($_POST["pgp_pubkey"]) > 0) {
		$gpg = new gnugpg;
		$gpg->userEmail = $user_email;
		$result = $gpg->import_key($_POST["pgp_pubkey"]);
		if($result){
			$wot_key = $_POST["pgp_pubkey"];
			$wot_fingerprint = $_POST["pgp_fingerprint"];
			$res = $gpg->list_keys();
			if($res) {
				$wot_length = get_key_length($gpg->keyArray);
				$wot_hex_id = get_key_id($gpg->keyArray);
				print "<br />";
			} else {
				$wot_key = "Chiave non valida";
				$wot_length = "";
				$wot_hex_id = "";
			}
		}
	}
	$foaf_workplaceHomepage = $_POST["user_workplace"];
	$foaf_workInfoHomepage = $_POST["user_workplace_info"];
	$foaf_thumb =  $config["system"]["default_host_uri"] . ucfirst($i18n["user_string"]) . "/" . ucfirst($GLOBALS["page_id"]) . "/pictures/" . $_POST["foaf_thumb"];
	$foaf_bio = $_POST["personal_bio"];
	foreach($_POST["user_account"] as $ka => $accounts) {
		$foaf_account .= "<foaf:account>\n";
			$foaf_account .= '	<foaf:OnlineAccount rdf:about="' . $_POST["user_account"][$ka]["uri"] . $_POST["user_account"][$ka]["name"] . '/">' . "\n";
			$foaf_account .= '		<foaf:accountName>' . $_POST["user_account"][$ka]["type"] . '</foaf:accountName>' . "\n";
			$foaf_account .= '		<foaf:accountServiceHomepage rdf:resource="' . $_POST["user_account"][$ka]["uri"] . '"/>' . "\n";
			$foaf_account .= '		<foaf:name>' . $_POST["user_account"][$ka]["name"] . '</foaf:name>' . "\n";
			//$foaf_account .= '		<foaf:thumbnail rdf:resource="http://d3fildg3jlcvty.cloudfront.net/20130318-03/graphics/favicon.ico"/>' . "\n";
			$foaf_account .= '	</foaf:OnlineAccount>' . "\n";
		$foaf_account .= '</foaf:account>' . "\n";
	}
	foreach($_POST["user_knows"] as $kk => $knows) {
		$foaf_knows .= "<foaf:knows>\n";
		$foaf_knows .= "	<foaf:Person>\n";
		$foaf_knows .= '		<foaf:name>' . $_POST["user_knows"][$kk]["name"] . '</foaf:name>' . "\n";
		$foaf_knows .= '		<foaf:mbox rdf:resource="mailto:' . $_POST["user_knows"][$kk]["email"] . '" />' . "\n";
		$foaf_knows .= '		<foaf:mbox_sha1sum>' . sha1($_POST["user_knows"][$kk]["email"]) . '</foaf:mbox_sha1sum>' . "\n";
		$foaf_knows .= '		<rdfs:seeAlso rdf:resource="' . $_POST["user_knows"][$kk]["uri"] . '"/>' . "\n";
		$foaf_knows .= '	</foaf:Person>' . "\n";
		$foaf_knows .= '</foaf:knows>' . "\n";
	}
	foreach($_POST["user_publication"] as $kkp => $kv) {
		$foaf_publication .= '<foaf:publications rdf:resource="' . $_POST["user_publication"][$kkp]["uri"] . '" />' . "\n";
	}
	foreach($_POST["user_interest"] as $ki => $vi) {
		$foaf_interest .= '<foaf:interest rdf:resource="http://it.dbpedia.org/page/' . urlencode(ucfirst(str_replace(" ", "_", $_POST["user_interest"][$ki]["type"]))) . '" />' . "\n";
	}
	foreach($_POST["user_cproject"] as $kcp => $kcpv) {
		$foaf_cproject .= "<foaf:currentProject>\n";
		$foaf_cproject .= "	<foaf:Project>\n";
		$foaf_cproject .= '		<dc:title xml:lang="it">' . $_POST["user_cproject"][$kcp]["title"] . '</dc:title>' . "\n";
		$foaf_cproject .= '		<dc:description xml:lang="it">' . $_POST["user_cproject"][$kcp]["desc"] . '</dc:description>' . "\n";
		$foaf_cproject .= '		<foaf:logo rdf:resource="' . $_POST["user_cproject"][$kcp]["logo"] . '"/>' . "\n";
		$foaf_cproject .= '		<foaf:homepage rdf:resource="' . $_POST["user_cproject"][$kcp]["page"] . '"/>' . "\n";
		$foaf_cproject .= "	</foaf:Project>\n";
		$foaf_cproject .= "</foaf:currentProject>\n";
	}
	foreach($_POST["user_pproject"] as $kpp => $kppv) {
		$foaf_pproject .= "<foaf:pastProject>\n";
		$foaf_pproject .= "	<foaf:Project>\n";
		$foaf_pproject .= '		<dc:title xml:lang="it">' . $_POST["user_pproject"][$kpp]["title"] . '</dc:title>' . "\n";
		$foaf_pproject .= '		<dc:description xml:lang="it">' . $_POST["user_pproject"][$kpp]["desc"] . '</dc:description>' . "\n";
		$foaf_pproject .= '		<foaf:logo rdf:resource="' . $_POST["user_pproject"][$kpp]["logo"] . '"/>' . "\n";
		$foaf_pproject .= '		<foaf:homepage rdf:resource="' . $_POST["user_pproject"][$kpp]["page"] . '"/>' . "\n";
		$foaf_pproject .= "	</foaf:Project>\n";
		$foaf_pproject .= "</foaf:pastProject>\n";
	}
	
	require_once("common/tpl/manage_users/foaf_manager/manual_foaf_generator.php");
	
	$fp = fopen($_SERVER["DOCUMENT_ROOT"] . ucfirst($i18n["user_string"]) . "/" . ucfirst($decrypted_user) . "/foaf.rdf", "w+");
	fwrite($fp, $foaf_file);
	fclose($fp);
}
$select_user = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($decrypted_user) . "'");
if ($select_user->rowCount() > 0){
	while($dato_user = $select_user->fetch()){
		$user_id = $dato_user["id"];
		$user_birthdate = ($dato_user["birth"] == "0000-00-00" ? "" : $dato_user["birth"]);
		$user_username = $dato_user["username"];
		$user_email = $dato_user["email"];
		if(strpos($dato_user["mail_account"], "@")){
			$splitted_mail = explode("@", $dato_user["mail_account"]);
			$mail_account = $splitted_mail[0];
		} else {
			$mail_account = $dato_user["mail_account"];
		}
		$mail_account_passwd = PMA_blowfish_decrypt($dato_user["mail_password"], $_COOKIE["iack"]);
		$user_newsletter_frequency = $dato_user["newsletter_frequency"];
		$user_session_length = $dato_user["session_length"];
			
			$file_headers = @get_headers($foaf_uri);
			if($file_headers[0] !== "HTTP/1.1 404 Not Found") {
				$foaf = EasyRdf_Graph::newAndLoad($foaf_uri);
				$foaf->load();
				if ($foaf->type() == "foaf:PersonalProfileDocument") {
					$person = $foaf->primaryTopic();
				}
				$personal_image = $person->get("foaf:depiction/foaf:thumbnail");
				$group = $foaf->get('foaf:Group', '^rdf:type');
				$org = $foaf->get('foaf:Organization', '^rdf:type');
				
				if (isset($person)) {
					/* Dati personali */
					$personal_description = nl2br(trim($person->get("rdfs:comment")));
					$profile_thumb = '<div id="profile_thumb_upload" style="background-image: url(' . $personal_image . ');"><a id="pickfiles" href="javascript: void(0);" title="Carica un\'altra immagine"></a><div id="upload_status"><span></span><div></div></div>';
					$gender = $person->get("foaf:gender");
					switch($gender) {
						case "male":
							$gender_m = 'checked="checked"';
							$gender_f = "";
							$gender_na = "";
							break;
						case "female":
							$gender_m = "";
							$gender_f = 'checked="checked"';
							$gender_na = "";
							break;
						default:
							$gender_m = "";
							$gender_f = "";
							$gender_na = 'checked="checked"';
							break;
					}
					$user_birthdate = (strlen($user_birthdate) > 0 ? $user_birthdate : $person->get("foaf:dateOfBirth"));
					$user_phone = str_replace("tel:", "", $person->get("foaf:phone"));
					$user_site = $person->get("foaf:homepage");
					$user_blog = $person->get("foaf:weblog");
					/* Informazioni lavorative */
					$user_workplace = (strlen($person->get("foaf:workplaceHomepage")) > 0 ? $person->get("foaf:workplaceHomepage") : $config["company"]["uri"]);
					$user_workplace_info = (strlen($person->get("foaf:workInfoHomepage")) > 0 ? $person->get("foaf:workInfoHomepage") : $config["company"]["uri"]);
					/* Account web */
					$user_common_nickname = (strlen($person->get("foaf:nick")) > 0 ? $person->get("foaf:nick") : $decrypted_user);
					$openid_account = $person->get("foaf:openid");
					$skype_account = $person->get("foaf:skypeID");
					$msn_account = $person->get("foaf:msnChatID");
					$yahoo_account = $person->get("foaf:yahooChatID");
					$aim_account = $person->get("foaf:aimChatID");
					$jabber_account = $person->get("foaf:jabberID");
					$icq_account = $person->get("foaf:icqChatID");
					
					$foaf_account = $person->all("foaf:account");
					/* Amici e conoscenti */
					$foaf_knows = $person->all("foaf:knows");
					/* Progetti */
					$foaf_current_project = $person->all("foaf:currentProject");
					$foaf_past_project = $person->all("foaf:pastProject");
					/* Interessi */
					$foaf_interest = $person->all("foaf:interest");
					/* Pubblicazioni */
					$foaf_publication = $person->all("foaf:publications");
					/* Gruppi di appartenenza */
					$foaf_group = $person->all("foaf:memberOf");
				}
			} else {
				$profile_thumb =  '<div id="profile_thumb_upload" style="background-color: #ccc;"><a id="pickfiles" href="javascript: void(0);" title="Carica un\'altra immagine"></a><div id="upload_status"><span></span><div></div></div>';
			}
	}
	//print_r($foaf_publication);
}
require_once("common/tpl/manage_users/foaf_manager/personal_data_div.tpl");
require_once("common/tpl/manage_users/foaf_manager/work_data_div.tpl");
require_once("common/tpl/manage_users/foaf_manager/accounts_data_div.tpl");
require_once("common/tpl/manage_users/foaf_manager/knows_data_div.tpl");
require_once("common/tpl/manage_users/foaf_manager/publications_data_div.tpl");
require_once("common/tpl/manage_users/foaf_manager/projects_data_div.tpl");
require_once("common/tpl/manage_users/foaf_manager/interests_data_div.tpl");
//grurequire_once("common/tpl/manage_users/foaf_manager/groups_data_div.tpl");

$username = ucfirst($decrypted_user);
$root = $absolute_path . ucfirst($i18n["user_string"]) . "/" . $username;

$content_body = <<<CPP
<!--
Questo modulo aiuter&agrave; nella creazione del proprio file <a href="http://it.wikipedia.org/wiki/FOAF" title="Vai alla pagina di Wikipedia" target="_blank">FoaF</a>, all'interno del quale saranno salvati i propri dati personali.<br />
Per maggiori informazioni riguardo al FoaF consultare la <a href="./Guide/Sistema/Genera_foaf" title="Vai alla guida">guida di riferimento.</a><br />
<b>Tutti i campi sono facoltativi</b><br />
<br />
<br />
-->
<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/skins/simple/style.css" />
<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/sets/html/style.css" />
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/markitup/jquery.markitup.js"></script>
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/plupload/js/plupload.full.js"></script>
<script language="Javascript" src="common/js/GnuPG/sha1.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/base64.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/PGpubkey.js" type="text/javascript"></script>
<script type="text/javascript">
function extractEmails(text) {
	return text.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
}
function getkey() {
	if($("#pgp_pubkey").val().length > 0){ 
		var pu = new getPublicKey($("#pgp_pubkey").val()),
		finger_parts = pu.fp.match(/.{4}/g).join(" ");
		
		$("#pgp_fingerprint").val(finger_parts);
	} else {
		$("#pgp_fingerprint").val("");
	}
}
$("a.btn.add").live("click", function() {
	$(this).closest("tr[class]").find("input").removeClass("form-ui-valid");
	
	if($("tr." + $(this).closest("table[id]").attr("id") + ".addedtr").length == 0) {
		if($(this).closest("tr[class]").find("input").filter(":visible:first").val().length > 0 && $(this).closest("tr[class]").find("input").filter(":visible:first").val().length > 0) {
			var type = $(this).closest("tr[class]").find("input").filter(":visible:first").attr("rel");
			$(this).closest("table[id]").find("tr").each(function(index, value) {
				$(this).removeClass("addedtr");
			});
			switch(type) {
				case "account":
					var row_a_count = $(this).closest("table[id]").find("tr").length;
					$(this).closest("table[id]").append('<tr class="user_' + type + ' addedtr"><td><table cellpadding="5" cellspacing="5" style="width: 100%;"><tr><td><input type="text" style="width: 25%;" rel="' + type + '" name="user_account[' + row_a_count + '][type]" placeholder="Nome del servizio (ad es. Facebook, Twitter, ecc...)" title="Nome del servizio (ad es. Facebook, Twitter, ecc...)" value="" />&emsp;<input type="text" style="width: 25%;" name="user_account[' + row_a_count + '][uri]" placeholder="Pagina del proprio account..." title="Indirizzo della pagina del proprio account" value="" />&emsp;<input type="text" name="user_account[' + type + '][name]" placeholder="Nome account..." title="Nome dell\'account" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un account">+</a>&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo account">-</a></td></tr></table></td></tr>');
					break;
				case "knows":
					var row_k_count = $(this).closest("table[id]").find("tr").length;
					$(this).closest("table[id]").append('<tr class="user_' + type + ' addedtr"><td><input type="text" style="width: 25%;" rel="' + type + '" name="user_knows[' + row_k_count + '][name]" placeholder="Nome del conoscente..." value="" />&emsp;<input type="text" value="" title="Indirizzo e-mail del conoscente" placeholder="Indirizzo e-mail..." name="user_knows[' + row_k_count + '][email]" style="width: 25%;">&emsp;<input type="uri" style="width: 25%;" name="useruser_knows[' + row_k_count + '][uri]" placeholder="Indirizzo del file FoaF..." title="Indirizzo del file FoaF del conoscente" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un conoscente">+</a>&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo conoscente">-</a></td></tr>');
					break;
				case "interest":
					var row_i_count = $(this).closest("table[id]").find("tr.user_interest").length;
					$(this).closest("table[id]").append('<tr class="user_' + type + ' addedtr"><td><input type="text" style="width: 25%;" rel="' + type + '" name="user_interest[' + row_i_count + '][type]" placeholder="Interesse" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un interesse">+</a>&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo interesse">-</a></td></tr>');
					break;
				case "publication":
					var row_p_count = $(this).closest("table[id]").find("tr").length;
					$(this).closest("table[id]").append('<tr class="user_' + type + ' addedtr"><td><input type="text" style="width: 25%;" rel="' + type + '" name="user_publication[' + row_p_count + '][uri]" placeholder="Pubblicazione" value="" />&emsp;<a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un pubblicazione">+</a>&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questa pubblicazione">-</a></td></tr>');
					break;
				case "cproject":
					var row_cp_count = $("#user_cproject tr.user_cproject:not(.addedtr_line)").length;
					$(this).closest("#user_cproject").append('<tr class="user_' + type + ' addedtr_line"><td colspan="3"><hr /></td></tr><tr class="user_' + type + ' addedtr"><td><table cellpadding="10" cellspacing="10" class="row_group"><tr><td><table cellpadding="5" cellspacing="5" style="width: 95%;"><tr><td><input type="text" style="width: 50%;" rel="' + type + '" name="user_pproject[' + row_cp_count + '][title]" placeholder="Titolo del progetto" value="" /><br />\n<br />\n<textarea style="width: 100%; height: 50px;" name="user_pproject[' + row_cp_count + '][desc]" placeholder="Descrizione del progetto"></textarea><br /><br />\n<input type="uri" style="width: 50%;" name="user_pproject[' + row_cp_count + '][page]" placeholder="Pagina del progetto (url)" value="" /><br /><br />\n<input type="uri" style="width: 50%;" name="user_pproject[' + row_cp_count + '][logo]" placeholder="Logo del progetto (uri)" value="" /></td></tr></table></td><td class="separator">&nbsp;</td><td style="width: 100px;" align="center"><a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un progetto">+</a>&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo progetto">-</a></td></tr></table></td></tr><tr class="user_' + type + ' addedtr_line"><td colspan="3">&nbsp;</td></tr>');
				case "pproject":
					var row_pp_count = $("#user_pproject tr.user_pproject:not(.addedtr_line)").length;
					$(this).closest("#user_pproject").append('<tr class="user_' + type + ' addedtr_line"><td colspan="3"><hr /></td></tr><tr class="user_' + type + ' addedtr"><td><table cellpadding="10" cellspacing="10" class="row_group"><tr><td><table cellpadding="5" cellspacing="5" style="width: 95%;"><tr><td><input type="text" style="width: 50%;" rel="' + type + '" name="user_pproject[' + row_pp_count + '][title]" placeholder="Titolo del progetto" value="" /><br />\n<br />\n<textarea style="width: 100%; height: 50px;" name="user_pproject[' + row_pp_count + '][desc]" placeholder="Descrizione del progetto"></textarea><br /><br />\n<input type="uri" style="width: 50%;" name="user_pproject[' + row_pp_count + '][page]" placeholder="Pagina del progetto (url)" value="" /><br /><br />\n<input type="uri" style="width: 50%;" name="user_pproject[' + row_pp_count + '][logo]" placeholder="Logo del progetto (uri)" value="" /></td></tr></table></td><td class="separator">&nbsp;</td><td style="width: 100px;" align="center"><a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un progetto">+</a>&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo progetto">-</a></td></tr></table></td></tr><tr class="user_' + type + ' addedtr_line"><td colspan="3">&nbsp;</td></tr>');
					break;
				case "group":
					var row_g_count = $(this).closest("table[id]").find("tr.user_group").length;
					$(this).closest("table[id]").append('<tr class="user_' + type + ' addedtr_line"><td colspan="3"><hr /></td></tr><tr class="user_' + type + ' addedtr"><td><table cellpadding="10" cellspacing="10" class="row_group"><tr><td><table cellpadding="5" cellspacing="5" style="width: 85%;"><tr><td><input type="text" style="width: 50%;" rel="' + type + '" name="user_group[' + row_g_count + '][name]" placeholder="Nome del gruppo" value="" /><br /><br />\n\n<input type="uri" style="width: 50%;" name="user_group[' + row_g_count + '][logo]" placeholder="Logo del gruppo (uri)" value="" /><br><br /><input type="uri" value="" style="width: 50%;" name="user_group[' + row_g_count + '][page]" placeholder="Pagina del gruppo (url)" /><br /><br /><input type="text" value="" placeholder="Nick" name="user_group[' + row_g_count + '][member]" style="width: 50%;" /></td></tr></table></td><td class="separator">&nbsp;</td><td style="width: 100px;" align="center"><a href="javascript: void(0);" class="btn add" style="float: none;" title="Aggiungi un progetto">+</a>&emsp;<a href="javascript: void(0);" class="btn remove" style="float: none;" title="Rimuovi questo progetto">-</a></td></tr></table></td></tr><tr class="user_' + type + ' addedtr_line"><td colspan="3">&nbsp;</td></tr>');
					break;
			}
			$("tr." + $(this).closest("table[id]").attr("id") + ".addedtr").find("input").filter(":first").focus();
		} else {
			$("tr." + $(this).closest("table[id]").attr("id") + ".addedtr").find("input").filter(":visible:first").focus();
		}
	} else {
		$("tr." + $(this).closest("table[id]").attr("id") + ".addedtr").find("input").filter(":visible:first").focus();
	}
});
$("#pgp_pubkey").on('keyup change', function(e){
	getkey();
});
$("a.btn.remove").live("click", function() {
	if($(this).closest("tr[class]")) {
		$(this).closest("tr[class]").fadeOut(150, function(){ $(this).remove(); });
		if($(this).closest("tr[class]").prev("tr.addedtr_line")) {
			$(this).closest("tr[class]").prev("tr.addedtr_line").fadeOut(150, function(){ $(this).remove(); });
		}
	}
});
function change_tab(subject, is_selected) {
	if(!is_selected) {
		$("#tabs li").each(function(){
			if($(this).hasClass("selected")) {
				$(this).removeClass("selected").addClass("normal");
				
				$("#" + $(this).find("a").attr("id") + "_div").fadeOut(300, function() {
					$("#" + subject + "_div").fadeIn(300);
				});
			}
		});
		$("#" + subject).parent("li").removeClass("normal").addClass("selected");
	}
	$("#selected_tab").val(subject);
}
$(function(){
	loader("Caricamento del documento FoaF...", "show");
	myWikiSettings = {
		nameSpace: "wiki",
		previewParserPath: "~/templates/preview.php?type=html&page={PAGE}&user={DECRYPTED_USER}",
		previewAutoRefresh: true,
		markupSet:  [
			{name:'Grassetto', key:'B', openWith:"<b>", closeWith:"</b>", className:'bold'}, 
			{name:'Corsivo', key:'I', openWith:"<i>", closeWith:"</i>", className:'italic'}, 
			{name:'Sottolineato', key:'U', openWith:'<u>', closeWith:'</u>', className:'underline'}, 
			{name:'Barrato', key:'S', openWith:'<strike>', closeWith:'</strike>', className:'strokethrough'}, 
			{separator:'---------------' },
			{name:'Titolo 1', key:'1', openWith:'<h1>', closeWith:'</h1>', className:'h1'},
			{name:'Titolo 2', key:'2', openWith:'<h2>', closeWith:'</h2>', className:'h2'},
			{name:'Titolo 3', key:'3', openWith:'<h3>', closeWith:'</h3>', className:'h3'},
			{name:'Titolo 4', key:'4', openWith:'<h4>', closeWith:'</h4>', className:'h4'},
			{name:'Titolo 5', key:'5', openWith:'<h5>', closeWith:'</h5>', className:'h5'},
			{separator:'---------------' },
			{name:'Collegamento libero', openWith:'<a href="[![Collegamento:!:]!]|">[![Collegamento:!:]!]|', closeWith:'</a>', placeHolder:'Testo del collegamento', className:'internal_link'},
			{separator:'---------------' },
			{name:'Citazione', openWith:'<blockquote>', closeWith:'</blockquote>', className:'quote'},
			{name:'Codice', openWith:'(!(<code type="[![Linguaggio:!:php]!]">\\n|!|<pre>)!)', closeWith:'(!(\\n</code>|!|</pre>)!)', className:'code'}, 
			{separator:'---------------' },
			{name:'Anteprima', call:'preview', className:'preview'}
		]
	};
	disableWikiSettings = {
		nameSpace: "wiki",
		previewParserPath: "~/templates/preview.php?page={PAGE}&user={DECRYPTED_USER}",
		previewAutoRefresh: true,
		markupSet:  [ ]
	};
	$('#personal_bio').markItUp(myWikiSettings);
	
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
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		autoSize: true,
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional['it']);
	$(".datepicker").datepicker();
	
	var upload_path = "Utente/$username/pictures";
	var uploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		browse_button : 'pickfiles',
		container : 'img_uploader',
		multi_selection: false,
		max_file_size : '10mb',
		chunk_size : '1mb',
		unique_names : false,
		resize : {width : 128, height : 128, quality : 100},
		url : '{ABSOLUTE_PATH}common/js/plupload/upload.php?upload_path=' + upload_path,
		flash_swf_url : '{ABSOLUTE_PATH}common/js/plupload/js/plupload.flash.swf',
		silverlight_xap_url : '{ABSOLUTE_PATH}common/js/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Files immagine", extensions : "jpg,gif,png"}
		]
	});
	uploader.init();
	uploader.bind('FilesAdded', function(up, files) {
		uploader.start();
		up.refresh();
	});
	uploader.bind('UploadProgress', function(up, file) {
		$("#upload_status span").html(file.percent + "%");
		$("#upload_status div").css("width", file.percent + "%");
	});
	uploader.bind('FileUploaded', function(up, file) {
		var filename = file.name = file.name.replace(/[^\w\._-]+/g, "_");
		$("#profile_thumb_upload").css({"background-image": "url($root/pictures/" + filename});
		$("#foaf_thumb").val(filename);
		$("#upload_status span").html("");
		$("#upload_status div").css("width", "0");
		up.refresh();
	});
	
	$("#profile_thumb_upload").hover(function() {
		$("#pickfiles").animate({'opacity': 1}, 300);
	}, function(){
		$("#pickfiles").animate({'opacity': 0}, 600);
	});
	$("#tabs a").click(function() {
		var subject = $(this).attr("id"),
		is_selected = $(this).parent("li").hasClass("selected");
		change_tab(subject, is_selected);
	});
	if($("#selected_tab").val() != "") {
		var current_tab = $("#selected_tab").val();
		is_selected = $("#" + current_tab).parent("li").hasClass("selected");
		change_tab(current_tab, is_selected);
		loader("", "hide");
	} else {
		loader("", "hide");
	}
});
</script>
<style>
#tabs ul {
	padding: 0;
	margin: 0;
}
#tabs li {
	display: inline-block;
	padding: 9px 0;
	position: relative;
	top: 0;
}
#tabs li a, #tabs li a:focus, #tabs li a:visited {
	padding: 10px;
	border: #ccc 1px solid;
	background-color: #f0f0f0;
	box-shadow: 0 0 3px #ddd inset;
	text-decoration: none;
	color: #666 !important;
}
#tabs li:first-child, #tabs li:first-child a {
	border-top-left-radius: 5px;
}
#tabs li:last-child, #tabs li:last-child a {
	border-top-right-radius: 5px;
}
#tabs li.selected a {
	background-color: #fff;
	box-shadow: 0 4px 4.5px #f4f4f4 inset;
	border-right: 0px none;
	border-bottom: 0px none;
	border-left: 0px none;
	padding-top: 11px;
	padding-bottom: 23px;
}
#tabs li.selected:first-child a {
	box-shadow: 7px 7px 6px #f4f4f4 inset;
	border-top-right-radius: 2px;
	border-right: 0px none;
	border-left: #ccc 1px solid;
	padding-top: 11px;
	padding-bottom: 23px;
}
#tabs li.selected:last-child {
	border-right: #ccc 1px solid;
}
#tabs li.selected:last-child a {
}
#tabs li.normal a:hover {
	box-shadow: 0 0 0 transparent;
	background-color: #f6f6f6;
	padding: 11px 10px 10px 10px;
}
#tabbed {
	margin-top: 8px;
	border-radius: 3px;
}
#tabbed form {
	background-color: #fff;
}
#content_wrapper_main_content {
	padding-top: 0;
}
table.row_group {
	width: 100%;
	background-color: #f0f0f0;
	border: #dedede 1px solid;
}
</style>
<p>
	Le informazioni contenute in questa pagina sono personali e appartengono al tuo profilo pubblico.<br />
	&Egrave; molto importante ricordare che una volta premuto il pulsante "salva" saranno disponibilli sul web - come del resto anche le informazioni sui social network.<br />
	<u>Se non si vuole che un'informazione venga conosciuta &egrave; bene non pubblicarla.</u>
</p>
<br />
<br />
<input type="hidden" id="selected_tab" value="" />
<div id="tabs">
	<ul>
		<li class="selected"><a href="javascript: void(0);" id="personal_data">Dati personali</a></li>
		<li class="normal"><a href="javascript: void(0);" id="work_data">Informazioni lavorative</a></li>
		<li class="normal"><a href="javascript: void(0);" id="accounts_data">Account web</a></li>
		<li class="normal"><a href="javascript: void(0);" id="knows_data">Amici e conoscenti</a></li>
		<li class="normal"><a href="javascript: void(0);" id="publications_data">Pubblicazioni</a></li>
		<li class="normal"><a href="javascript: void(0);" id="projects_data">Progetti</a></li>
		<li class="normal"><a href="javascript: void(0);" id="interests_data">Interessi</a></li>
		<!--li class="normal"><a href="javascript: void(0);" id="groups_data">Gruppi di appartenenza</a></li-->
	</ul>
</div>
<form method="post" action="">
	<div id="tabbed">
		$personal_data_div
		$work_data_div
		$accounts_data_div
		$knows_data_div
		$publications_data_div
		$projects_data_div
		$interests_data_div
		$groups_data_div
		
		<hr />
		<div style="display: inline-block; width: 100%;">
			<input name="personal_data_btn" type="submit" value="Salva" />
		</div>
	</div>
</form>
CPP;

require_once("common/include/conf/replacing_object_data.php");
?>