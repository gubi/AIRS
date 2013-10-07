<?php
$show_right_panel = 1;
$show_right_panel_toc = 0;
$show_right_panel_tocs = 0;
$modules = "manage_users/user_content_module.tpl";

$users_list = $pdo->query("select * from `airs_users` where `username` = '" . addslashes(strtolower($GLOBALS["page_id"])) . "'");
if ($users_list->rowCount() > 0){
	while ($dato_users = $users_list->fetch()){
		$content_title = ucwords($dato_users["name"] . " " . $dato_users["lastname"]);
		
		$users_level = $pdo->query("select * from `airs_levels` where level = '" . addslashes($dato_users["level"]) . "'");
		while ($dato_level = $users_level->fetch()){
			$content_subtitle = $dato_level["text"];
		}
		if($dato_users["personal_page_visible_for"] <= $GLOBALS["user_level"]) {
			require_once("EasyRDF/autoload.php");
			
			$foaf_uri = $config["system"]["default_host_uri"] . $i18n["user_string"] . "/" . ucfirst(strtolower($GLOBALS["page_id"])) . "/foaf.rdf";
			
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
			
			$foaf = new EasyRdf_Graph($foaf_uri);
			$foaf->load();
			if ($foaf->type() == "foaf:PersonalProfileDocument") {
				$person = $foaf->primaryTopic();
			} else if ($foaf->type() == "foaf:Person") {
				$person = $foaf->resource($foaf_uri);
			}
			if (isset($person)) {
				$content_title = ucwords(strtolower($person->get("foaf:givenname") . " " . $person->get("foaf:family_name")));
				
				$profile_thumb = '<div id="profile_thumb">';
					//$profile_thumb .= '<a rel="zoombox" href="' . $person->get("foaf:depiction") . '" title="' . $person->get("foaf:name") . '">';
						$profile_thumb .= '<img src="' . $person->get("foaf:depiction/foaf:thumbnail") . '" />';
					//$profile_thumb .= '</a>';
				$profile_thumb .= '</div>';
				
				$content_body = '<div id="user_page">' . $profile_thumb . '<p>' . nl2br(trim($person->get("rdfs:comment"))) . '</p></div><br />';
				
				foreach ($person->all("foaf:publications") as $publication) {
					$publications[] = urlencode($publication);
				}
				$all_publications = implode(",", $publications);
				$publications_txt = $i18n["publications_txt"];
				
				foreach ($person->all("foaf:interest") as $interest) {
					$int_info = pathinfo(urldecode($interest));
					$int_list .= '<li><a href="' . $interest . '" title="' . $i18n["go_to"] . " " . urldecode($interest) . '">' . $int_info["basename"] . '</a></li>';
				}
				$content_body .= (strlen($int_list) > 0) ? "<h2>" . $i18n["interest_txt"] . "</h2><ul>" . $int_list . "</ul><br />" : "";
				
				foreach ($person->all("foaf:currentProject") as $currentProject) {
					$cp_img = ($currentProject->get("foaf:logo")) ? '<td style="text-align: left;"><img src="' . $currentProject->get("foaf:logo") . '" style="width: 100px;" /></td>' : "<td></td>";
					$cp_link = (strlen($currentProject->get("foaf:homepage")) > 0) ? '<td><a href="' . $currentProject->get("foaf:homepage") . '" title="' . $i18n["go_to_project_main_page"] . '">' . $currentProject->get("dc:title") . '</a></td>' : '<td>' . $currentProject->get("dc:title") . '</td>';
					$cp_desc = ($currentProject->get("dc:description")) ? '<td style="text-align: left;">' . $currentProject->get("dc:description") . '</td>' : "<td></td>";
					$cp_list .= '<tr>' . $cp_img . $cp_link . $cp_desc . '</tr>';
				}
				$content_body .= (strlen($cp_list) > 0) ? "<h2>" . $i18n["current_projects_txt"] . '</h2><table cellspacing="5" cellpadding="5">' . $cp_list . "</table><br />" : "";
				
				foreach ($person->all("foaf:pastProject") as $pastProject) {
					$p_img = ($pastProject->get("foaf:logo")) ? '<td style="text-align: left;"><img src="' . $pastProject->get("foaf:logo") . '" style="width: 100px;" /></td>' : "<td></td>";
					$p_link = (strlen($pastProject->get("foaf:homepage")) > 0) ? '<td><a href="' . $pastProject->get("foaf:homepage") . '" title="' . $i18n["go_to_project_main_page"] . '">' . $pastProject->get("dc:title") . '</a></td>' : '<td>' . $pastProject->get("dc:title") . '</td>';
					$p_desc = ($pastProject->get("dc:description")) ? '<td style="text-align: left;">' . $pastProject->get("dc:description") . '</td>' : "<td></td>";
					$p_list .= '<tr>' . $p_img . $p_link . $p_desc . '</tr>';
				}
				$content_body .= (strlen($p_list) > 0) ? "<h2>" . $i18n["past_projects_txt"] . '</h2><table cellspacing="5" cellpadding="5">' . $p_list . "</table><br />" : "";
			}
			$content_body .= '<br />';
			
			$content_body .= <<<User_content
			<script type="text/javascript">
			$(document).ready(function(){
				$("#user_publications").html("Ricerca pubblicazioni...");
				$.get("{ABSOLUTE_PATH}common/include/funcs/_ajax/rdf2json_array.php", {resources: '$all_publications'}, function(data){
					if(data) {
						$("#user_publications").html('<h2>$publications_txt</h2>');
						
						var authors = [], 
						autori,
						periodic;
						
						$("#user_publications").append("<ul></ul>");
						$.each(data, function(index, item){
							$.each(item.authors, function(k, v){
								if(item[k].givenname != undefined) {
									authors.push(item[k].givenname.substr(0, 1) + ". " + item[k].surname);
								}
							});
							autori = authors.join(", ");
							dates = item.date.split("-");
							periodic = item.isPartOf.title + " (" + item.isPartOf.alternative + ")  " + dates[0] + ", v. " + item.isPartOf.volume + " nr. " + item.isPartOf.number + " - " + item.identifier;
							
							$("#user_publications ul").append("<li>" + autori + ", <i>" + item.shortTitle + "</i>, " + periodic + "</li>");
						});
					} else {
						$("#user_publications").html("");
					}
				}, "json");
			});
			</script>
			<div id="user_publications"></div>
User_content;
			require_once("common/include/conf/replacing_object_data.php");
		} else {
			$content_body = $message->generate_error(401);
		}
	}
}
?>