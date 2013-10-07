<?php
/**
* Generates top submenu (at bottom of breadcrumb)
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

$menu_groups = $pdo->query("select * from `airs_menu_groups` where `visible` = '1' and `position` = 'top' and `level` <= '" . $GLOBALS["user_level"] . "' order by `order`");
if ($menu_groups->rowCount() > 0){
	while ($dato_menu_groups = $menu_groups->fetch()){
		print "<ul title=\"" . $dato_menu_groups["name"] . "\">";
			$menu = $pdo->query("select * from `airs_menu` where `visible` = '1' and `position` = 'top' and `group` = '" . addslashes($dato_menu_groups["id"]) . "' and `level` <= '" . $GLOBALS["user_level"] . "' order by `order` asc");
			if ($menu->rowCount() > 0){
				while ($dato_menu = $menu->fetch()){
					if ($dato_menu["has_link"] == 1){
						$link = "" . $dato_menu["link"];
					} else {
						$link = get_link($dato_menu["name"]);
					}
					if ($link == trim($GLOBALS["page"])){
						// Mostra il testo invece del  link
						if ($dato_menu["rel"] !== "nofollow" && $dato_menu["rel"] !== "next"){
							print "<li>" . $dato_menu["name"] . "</li>";
						} else {
							print "<li><a href=\"" . $link . "\" title=\"" . $dato_menu["title"] . "\" rel=\"" . $dato_menu["rel"] . "\">" . $dato_menu["name"] . "</a></li>";
						}
					} else {
						print "<li><a href=\"" . $link . "\" title=\"" . $dato_menu["title"] . "\" rel=\"" . $dato_menu["rel"] . "\">" . $dato_menu["name"] . "</a></li>";
					}
				}
			}
		print "</ul>";
	}
}
?>
<ul>
	<?php
	if ($GLOBALS["user_level"] == 0){
		print '<li><a href="./Speciale:Accedi" title="Accedi al Sistema" rel="nofollow">Accedi</a></li><li><a href="./Registrami" title="Registrati al Sistema" rel="nofollow">Registrati</a></li>';
	} else {
		if(isset($_COOKIE["iac"])){
			print "<li><a href=\"Utente/" . ucwords($GLOBALS["decrypted_user"]) . "\" title=\"Pagina personale\" rel=\"nofollow\">" . ucwords($GLOBALS["decrypted_user"]) . "</a></li>";
		}
		print "<li><a href=\"/Speciale:Esci\" title=\"Esci dal sistema\" rel=\"nofollow\">Esci</a></li>";
	}
	print "<li><a id=\"feedback_btn\" href=\"javascript:void(0);\" title=\"Riporta un feedback\">Feedback</a></li>";
	?>
</ul>