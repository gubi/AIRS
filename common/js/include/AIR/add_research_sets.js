/**
* Generate part of GUI for adding research
* 
* Javascript and jQuery
*
* @category	SystemScript
* @package	AIRS_AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
$(document).ready(function(){
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
	$(".timepicker").timepicker({
		timeFormat: "hh:mm",
		stepHour: 1,
		stepMinute: 1,
		hourGrid: 5,
		minuteGrid: 10
	});
	$("#se_tags_ul").tagit({
		fieldName: "se_tags_input",
		singleField: true,
		singleFieldNode: $("#se_tags"),
		removeConfirmation: true,
		allowSpaces: true,
		tagSource: function(search, showChoices) {
			var that = this;
			$.ajax({
				url: "common/include/funcs/_ajax/EditoRSS/get_existing_feeds_groups.php",
				data: search,
				dataType: "json",
				success: function(choices) {
					showChoices(that._subtractArray(choices, that.assignedTags()));
				}
			});
		}
	}).click(function(){ $(this).css({"border": "#999 1px solid", "box-shadow": "0 0 9px #ccc"}); }).find("input").blur(function(){ $("#tag").css({"border": "#ccc 1px solid", "box-shadow": "none"}); }).focus(function(){ $("#tag").css({"border": "#999 1px solid", "box-shadow": "0 0 9px #ccc"}); });
	$("#se_lang_ul").tagit({
		fieldName: "se_lang_input",
		singleField: true,
		singleFieldNode: $("#se_lang"),
		removeConfirmation: true,
		allowSpaces: true,
		tagSource: function(search, showChoices) {
			var that = this;
			$.ajax({
				url: "common/include/funcs/_ajax/get_language.php",
				data: search,
				dataType: "json",
				success: function(choices) {
					showChoices(that._subtractArray(choices, that.assignedTags()));
				}
			});
		}
	}).click(function(){ $(this).css({"border": "#999 1px solid", "box-shadow": "0 0 9px #ccc"}); }).find("input").blur(function(){ $("#tag").css({"border": "#ccc 1px solid", "box-shadow": "none"}); }).focus(function(){ $("#tag").css({"border": "#999 1px solid", "box-shadow": "0 0 9px #ccc"}); });
	$("#se_country_ul").tagit({
		fieldName: "se_country_input",
		singleField: true,
		singleFieldNode: $("#se_country"),
		removeConfirmation: true,
		allowSpaces: true,
		tagSource: function(search, showChoices) {
			var that = this;
			$.ajax({
				url: "common/include/funcs/_ajax/get_country_name.php",
				data: search,
				dataType: "json",
				success: function(choices) {
					showChoices(that._subtractArray(choices, that.assignedTags()));
				}
			});
		}
	}).click(function(){ $(this).css({"border": "#999 1px solid", "box-shadow": "0 0 9px #ccc"}); }).find("input").blur(function(){ $("#tag").css({"border": "#ccc 1px solid", "box-shadow": "none"}); }).focus(function(){ $("#tag").css({"border": "#999 1px solid", "box-shadow": "0 0 9px #ccc"}); });
});