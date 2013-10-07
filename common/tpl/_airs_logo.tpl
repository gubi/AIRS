<?php
/**
* Generates AIRS animated logo
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
<script type="text/javascript" src="common/js/jquery.backgroundpos.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#background").delay(600).fadeIn(9000);
	$("#airs_logo").append('<div id="logotype"></div>');
	$("#logotype").prepend("<span></span>").find("span").fadeIn(300);
	$("#logotype").append("<span></span>").find("span").delay(90).fadeIn(300);
	$("#logotype").append("<span></span>").find("span").delay(180).fadeIn(300);
	$("#logotype").append("<span></span>").find("span").delay(270).fadeIn(300);
	var system_version_txt = "",
	system_type = "<?php print trim($config["system"]["type"]); ?>";
	if(system_type.length > 0){
		switch("<?php print $config["system"]["type"]; ?>"){
			case "develop":
				system_version_txt = "<?php print $i18n["system_version_develop"]; ?>";
				break;
			case "beta":
				system_version_txt = "<?php print $i18n["system_version_beta"]; ?>";
				break;
			case "installer":
				system_version_txt = "<?php print $i18n["system_version_installer"]; ?>";
		}
		$("#payoff").addClass("has_type").delay(360).slideDown(600);
		$("#logotype").append('<div id="system_version" class="' + system_type + '">' + system_version_txt + '</div>');
		$("#system_version").delay(450).fadeIn(450).animate({"right": "0"}, 450);
	} else {
		$("#payoff").delay(360).slideDown(600);
	}
});
</script>
<div id="airs_logo"><div id="payoff"></div></div>