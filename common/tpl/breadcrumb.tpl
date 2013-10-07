<?php
/**
* Generates AIRS Wiki breadcrumb
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
?>
<script type="text/javascript">
$(document).ready(function(){ if ($(".home").attr("title") !== "<?php print $i18n["page_name_main"]; ?>"){ $(".home").hover(function(){ if ($(this).find("span").html() == null){ $(this).append("<span style=\"display: none;\"><?php print $i18n["page_name_root"]; ?></span>"); } $(this).find("span").fadeIn(300); }, function(){ $(this).find("span").delay(300).fadeOut(600); }); } });
</script>
<ul>
	<?php
	if (isset($GLOBALS["page"]) && trim($GLOBALS["page"]) !== ""){
		?>
		<li><a class="home" href="" title="<?php print $i18n["come_back_to"] . " " . $i18n["page_name_main"]; ?>"></a></li>
		<?php
	} else {
		?>
		<li><span class="home" title="<?php print $i18n["page_name_main"]; ?>"></span></li>
		<?php
	}
	print $page_a;
	?>
</ul>