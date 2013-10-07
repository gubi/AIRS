<?php
/**
* Main manage user page
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
* @package	AIRS_Manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$pdo = db_connect("");

$et = 0;
$user_management_type_col = $pdo->query("select distinct(`col`), `col_name` from `airs_users_management_menu`");
if ($user_management_type_col->rowCount() > 0){
	while ($dato_user_management_type_col = $user_management_type_col->fetch()){
		$ec = 0;
		$et++;
		$user_managements_table .= '<fieldset><legend>' . $dato_user_management_type_col["col_name"] . '</legend><table cellpadding="10" cellspacing="10" class="menu"><tr>';
		
		$user_management_type = $pdo->query("select * from `airs_users_management_menu` where `col` = '" . $dato_user_management_type_col["col"] . "'");
		if ($user_management_type->rowCount() > 0){
			while ($dato_user_management_type = $user_management_type->fetch()){
				$ec++;
				$user_managements_table .= "<td id=\"" . $dato_user_management_type["id"] . "\" onclick=\"window.location='./" . $dato_user_management_type["link"] . "'\"><img src=\"" . $dato_user_management_type["img"] . "\" /><span>" . strtoupper($dato_user_management_type["name"]) . "</span></td>";
				if ($ec < $user_management_type->rowCount()){
					$user_managements_table .= "<td class=\"separator\"></td>";
				}
			}
		}
		
		$user_managements_table .= '</tr></table></fieldset><br />';
	}
}
$content_body = $user_managements_table;

require_once("common/include/conf/replacing_object_data.php");
?>
