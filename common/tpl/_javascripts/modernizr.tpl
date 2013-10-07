<!-- Modernizr -->
<script src="<?php print $absolute_path; ?>common/js/modernizr-yepnope-custom.js"></script>
<?php
if(!$js_loaded["jquery"]) {
	require_once("common/tpl/_javascripts/jquery.tpl");
}
?>
<script src="<?php print $absolute_path; ?>common/js/webshim/js-webshim/minified/extras/modernizr-custom.js"></script> 
<script src="<?php print $absolute_path; ?>common/js/webshim/js-webshim/minified/polyfiller.js"></script>
<script type="text/javascript">
function UpdateType(e){
	var events  = (isIE)?event:e; 
	var obj = (isIE)?events.srcElement:e.target; 
	var frm = $("#form1"); 
	if(changeType && obj.nodeName == "INPUT" && obj.type == "text"){ 
		if(isIE){ 
			var nextNode = obj.nextSibling; 
			obj.removeNode(true); 
			var obj = document.createElement("INPUT"); 
			obj.setAttribute("type","password"); 
			obj.setAttribute("id","passwordField");
			frm.insertBefore(obj,nextNode); 
		} else { 
			obj.setAttribute("type","password");
			obj.setAttribute("id","passwordField");
			frm.innerHTML = frm.innerHTML; 
		}
		$("#passwordField").focus(); 
	}
	return;
}
$.webshims.setOptions("forms", {
	overrideMessages: true,
	replaceValidationUI: true,
	customMessages: true
});
$.webshims.polyfill("forms forms-ext");
$(function(){
	$.webshims.activeLang("it");
	$("input[required]").setCustomValidity("");
	$.webshims.validityMessages["it"] = {
		typeMismatch: {
			email: "\"{%value}\" <?php print $i18n["error_invalid_email"]; ?>",
			url: "\"{%value}\" <?php print $i18n["error_invalid_url"]; ?>",
			number: "\"{%value}\" <?php print $i18n["error_is_not_number"]; ?>",
			date: "\"{%value}\" <?php print $i18n["error_is_not_date"]; ?>",
			time: "\"{%value}\" <?php print $i18n["error_is_not_time_data"]; ?>",
			range: "\"{%value}\" <?php print $i18n["error_is_not_number"]; ?>",
			"datetime-local": "\"{%value}\" <?php print $i18n["error_is_not_datetime"]; ?>"
		},
		rangeUnderflow: "\"{%value}\" <?php print $i18n["error_too_short_value"]; ?>.\n<?php print $i18n["error_minimal_value_is"]; ?> {%min}",
		rangeOverflow: "\"{%value}\" <?php print $i18n["error_too_long_value"]; ?>.\n<?php print $i18n["error_maximum_value_is"]; ?> {%max}",
		stepMismatch: "\"{%value}\" <?php print $i18n["error_is_not_possible_value"]; ?>.{%title}",
		tooLong: "<?php print $i18n["error_inserted_text_is_too_long"]; ?>! <?php print $i18n["was_used_string_m"]; ?> {%valueLen} <?php print $i18n["characters_string"]; ?> <?php print $i18n["and_string"]; ?> {%maxlength} <?php print $i18n["error_is_allowed_limit"]; ?>",
		patternMismatch: "\"{%value}\" <?php print $i18n["error_is_not_required_format"]; ?>. <?php print $i18n["error_insert_this_format"]; ?>: {%title}",
		valueMissing: "Compilare questo campo"
	};
	$("form").bind("invalid", function(e){
		e.preventDefault();
	}).bind("firstinvalid", function(e){
		$.webshims.validityAlert.showFor(e.target, $.attr(e.target, "customValidationMessage"));
		return false;
	}).bind("focusout invalid", function(e){
		var $elem = $(e.target);
		if($elem.attr("willValidate") && $elem.attr("type") !== "submit"){
			if(e.type !== "invalid" && $elem.is(":valid-element")){
				$elem.closest(".form-element, fieldset").addClass("valid").removeClass("invalid");
			} else {
				$elem.closest(".form-element, fieldset").addClass("invalid").removeClass("valid");
			}
		}
	});
});
</script>