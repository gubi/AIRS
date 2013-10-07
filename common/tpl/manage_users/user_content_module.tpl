<?php
if(!class_exists(EasyRdf_Graph)) {
	require_once("EasyRDF/autoload.php");
}


$user_foaf_uri = $config["system"]["default_host_uri"] . $i18n["user_string"] . "/" . ucfirst(strtolower($GLOBALS["page_id"])) . "/foaf.rdf";
/*
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
$foaf = EasyRdf_Graph::newAndLoad($foaf_file);
$person = $foaf->primaryTopic();

$name = ucwords(strtolower($person->get("foaf:givenname") . " " . $person->get("foaf:family_name")));
$nick = ($person->get("foaf:nick") ? $person->get("foaf:nick") : "");
$email = ($person->get("foaf:mbox") ? '<a href="' . $person->get("foaf:mbox") . '">' . str_replace("mailto:", "", $person->get("foaf:mbox")) . '</a>' : "");
$homepage = ($person->get("foaf:homepage") ? '<a href="' . $person->get("foaf:homepage") . '" target="_blank">' . $person->get("foaf:homepage"). '</a>' : "");
$weblog = ($person->get("foaf:weblog") ? '<a href="' . $person->get("foaf:weblog") . '" target="_blank">' . $person->get("foaf:weblog"). '</a>' : "");
$workplaceHomepage = ($person->get("foaf:workplaceHomepage") ? '<a href="' . $person->get("foaf:workplaceHomepage") . '" target="_blank">' . $person->get("foaf:workplaceHomepage"). '</a>' : "");
$workInfoHomepage = ($person->get("foaf:workInfoHomepage") ? '<a href="' . $person->get("foaf:workInfoHomepage") . '" target="_blank">' . $person->get("foaf:workInfoHomepage"). '</a>' : "");
$pgpKey = ((strlen($person->get("wot:hasKey")) > 0) ? $person->get("wot:PubKey/wot:hex_id") : "");

$person->add('wot:PubKey', $foaf_file);

print $person->get("wot:PubKey/wot:hex_id");
if (isset($person)) {
	$table = '<div id="toc">';
		$table .= '<h1><img src="common/media/img/avatar_16_999.png" />&nbsp;&nbsp;' . strtoupper($nick) . '</h1>';
		$table .= '<div class="toc_content"><table cellspacing="2" cellpadding="2" id="user_content_module">';
			foreach ($person->all("foaf:account") as $account) {
				$acc_link = '<a href="' . $account->get("foaf:page") . '" title="' . $i18n["go_to"] . " " . $account->get("foaf:page") . '">' . $account->get("foaf:name") . '</a>';
				$table .= tr("user_profile_16_999.png", $acc_link);
			}
			($person->all("foaf:skypeID") ? $table .= tr("skype_16_999.png", $person->get("foaf:skypeID")) : "");
			($person->all("foaf:jabberID") ? $table .= tr("jabber_16_999.png", $person->get("foaf:jabberID")) : ""); // Da trovare l'icona
			($person->all("foaf:msnID") ? $table .= tr("msn_16_999.png", $person->get("foaf:msnID")) : ""); // Da trovare l'icona
			
			$table .= tr("");
		$table .= tr("mail_16_999.png", $email);
		$table .= tr("website_16_999.png", $homepage);
		$table .= tr("imprint_16_999.png", $weblog);
		$table .= tr("");
		$table .= tr("industry_16_999.png", $workplaceHomepage);
		$table .= tr("industry_info_16_999.png", $workInfoHomepage);
		$table .= tr("");
		$table .= tr("security_closed_16_999.png.png", $pgpKey);
		$table .= '</table></div>';
	$table .= '</div>';
	print $table;
	
}
*/
?>
<script type="text/javascript">
function tr(img, content) {
	if(img == undefined || img.length == 0 || content == undefined || content.length == 0) {
		return "<tr><td>&nbsp;</td></tr>";
	} else if (content.length == 0) {
		return "";
	} else {
		return '<tr><th><img src="common/media/img/' + img + '" /></th><td>' + content + '</td></tr>';
	}
}
function ucwords(str) {
	return (str + "").replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
		return $1.toUpperCase();
	});
}
$(document).ready(function(){
	var name,
	acc_link,
	table,
	pgp,
	mail,
	fingerprint,
	username = "",
	knows;
	$.get("common/include/funcs/_ajax/rdf2json_array.php", {resources: "<?php print $user_foaf_uri; ?>"}, function(data){
		$.each(data, function(index, item){
			$("#toc").html('<h1><img src="common/media/img/avatar_16_999.png" />&nbsp;&nbsp;' + item.primaryTopic.nick.toUpperCase() + '</h1><div class="toc_content"><table cellspacing="2" cellpadding="2" id="user_content_module"></table></div>');
			name = ucwords(item.primaryTopic.givenname.toLowerCase() + " " + item.primaryTopic.family_name.toLowerCase());
			if(item.primaryTopic.account != undefined){
				$.each(item.primaryTopic.account, function(a_index, a_item) {
					table += tr("user_profile_16_999.png", '<a href="' + a_item.page + '" title="<?php print $i18n["go_to"]; ?> ' + a_item.page + '">' + a_item.name + '</a>');
				});
			}
			table += ((item.primaryTopic.skypeID != undefined) ? tr("skype_16_999.png", item.primaryTopic.skypeID) : "");
			table += ((item.primaryTopic.jabberID != undefined) ? tr("jabber_16_999.png", item.primaryTopic.jabberID) : "");
			table += ((item.primaryTopic.msnID != undefined) ? tr("msn_16_999.png", item.primaryTopic.msnID) : "");
			
			table += tr();
				mail = item.primaryTopic.mbox.replace("mailto:", "");
			table += ((item.primaryTopic.mbox != undefined) ? tr("mail_16_999.png", '<a href="' + item.primaryTopic.mbox + '">' + mail + '</a>') : "");
			table += ((item.primaryTopic.homepage != undefined) ? tr("website_16_999.png", '<a href="' + item.primaryTopic.homepage + '" title="<?php print $i18n["go_to"]; ?> ' + item.primaryTopic.homepage + '" target="_blank">' + (item.primaryTopic.homepage_text != undefined ? item.primaryTopic.homepage_text : item.primaryTopic.homepage) + '</a>') : "");
			table += ((item.primaryTopic.weblog != undefined) ? tr("imprint_16_999.png", '<a href="' + item.primaryTopic.weblog + '" title="<?php print $i18n["go_to"]; ?> ' + item.primaryTopic.weblog + '" target="_blank">' + (item.primaryTopic.weblog_text != undefined ? item.primaryTopic.weblog_text : item.primaryTopic.weblog) + '</a>') : "");
			table += tr();
			table += ((item.primaryTopic.workplaceHomepage != undefined) ? tr("industry_16_999.png", '<a href="' + item.primaryTopic.workplaceHomepage + '" title="<?php print $i18n["go_to"]; ?> ' + item.primaryTopic.workplaceHomepage + '" target="_blank">' + (item.primaryTopic.workplaceHomepage_text != undefined ? item.primaryTopic.workplaceHomepage_text : item.primaryTopic.workplaceHomepage) + '</a>') : "");
			table += ((item.primaryTopic.workInfoHomepage != undefined) ? tr("industry_info_16_999.png", '<a href="' + item.primaryTopic.workInfoHomepage + '" title="<?php print $i18n["go_to"]; ?> ' + item.primaryTopic.workInfoHomepage + '" target="_blank">' + (item.primaryTopic.workplaceHomepage_text != undefined ? item.primaryTopic.workInfoHomepage_text : item.primaryTopic.workInfoHomepage) + '</a>') : "");
			
			if(item.primaryTopic.hasKey != undefined) {
				$("#toc").append('<h1><acronym title="Pretty Good Privacy">PGP</acronym></h1><div class="toc_content"><table cellspacing="2" cellpadding="2" id="user_content_module_pgp"></table></div>');
				pgp += ((item.primaryTopic.hasKey.hex_id != undefined) ? tr("security_closed_16_999.png", "0x" + item.primaryTopic.hasKey.hex_id) : "");
				pgp += ((item.primaryTopic.hasKey.fingerprint != undefined) ? tr("barcode_16_999.png", item.primaryTopic.hasKey.fingerprint) : "");
				pgp += ((item.primaryTopic.hasKey.length != undefined) ? tr("weight_16_999.png", item.primaryTopic.hasKey.length) : "");
			}
			if(item.knows != undefined) {
				$("#toc").append('<h1><?php print $i18n["linked_peoples"]; ?></h1><div class="toc_content"><table cellspacing="2" cellpadding="2" id="user_content_module_knows"></table></div>');
				$.each(item.knows, function(k_index, k_item){
					if(k_item.seeAlso != undefined) {
						$.ajax({
							url: "common/include/funcs/_ajax/rdf2json_array.php",
							data: {resources: k_item.seeAlso},
							dataType: "json",
							success: function(u_data){
								$.each(u_data, function(u_index, u_item){
									username = u_item.primaryTopicnick;
									if (k_item.homepage != undefined) {
										home = u_item.primaryTopic.homepage;
									} else {
										home = u_item.primaryTopic.weblog;
									}
									$("#user_content_module_knows").append((home != undefined) ? tr("user_close_16_999.png", '<a href="' + home + '" target="_blank" title="<?php print $i18n["go_to"]; ?> ' + home + '">' + u_item.primaryTopic.nick + '</a>') : tr("user_close_16_999.png", u_item.primaryTopic.nick));
								});
							},
							error: function() {
								$("#user_content_module_knows").append((k_item.name != undefined) ? tr("user_close_16_999.png", k_item.name) : "");
							}
						});
					} else {
						$("#user_content_module_knows").append((k_item.name != undefined) ? tr("user_close_16_999.png", k_item.name) : "");
					}
				});
			}
			/*
			// Creare gruppi di appartenenza (aggiustare il file 'common/include/funcs/_ajax/rdf2json_array.php')
			if(item.primaryTopic.hasKey != undefined) {
				$("#toc").append('<h1><?php print $i18n["linked_groups"]; ?></h1><div class="toc_content"><table cellspacing="2" cellpadding="2" id="user_content_module_groups"></table></div>');
				pgp += ((item.primaryTopic.hasKey.hex_id != undefined) ? tr("security_closed_16_999.png", "0x" + item.primaryTopic.hasKey.hex_id) : "");
				pgp += ((item.primaryTopic.hasKey.fingerprint != undefined) ? tr("barcode_16_999.png", item.primaryTopic.hasKey.fingerprint) : "");
				pgp += ((item.primaryTopic.hasKey.length != undefined) ? tr("weight_16_999.png", item.primaryTopic.hasKey.length) : "");
			}
			*/
		});
		$("#user_content_module").html(table);
		$("#user_content_module_pgp").html(pgp);
		$("#user_content_module_groups").html(knows);
	}, "json");
});
</script>
<div id="toc"></div>