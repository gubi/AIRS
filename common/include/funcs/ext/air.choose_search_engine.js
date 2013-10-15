var auto_txt = "<br /><br />Il sistema ricaverà da sé la chiave da usare per le ricerche selettive.";
var step = new Array();
for (i = 1; i <= 5; i++){
	step[i] = new Array();
}
	step[1]["title"] = "LINGUA DELLE RICERCHE";
	step[1]["description"] = "Fare click sul campo che identifica il parametro per il filtro delle lingue.<br />Se il modulo presenta più di una lingua è sufficiente sceglierne una a caso.";
	step[1]["var"] = "se_lang_var";
	step[2]["title"] = "DOMINIO DI RICERCA";
	step[2]["description"] = "Fare click sul campo che identifica il parametro per il filtro sul dominio di ricerca.";
	step[2]["var"] = "se_site_var";
	step[3]["title"] = "FORMATO DI FILE";
	step[3]["description"] = "Fare click sul campo che identifica il parametro per il filtro su un formato di file.";
	step[3]["var"] = "se_filetype_var";
	step[4]["title"] = "RICERCA GEOGRAFICA";
	step[4]["description"] = "Fare click sul campo che identifica il parametro per il filtro su una località geografica.";
	step[4]["var"] = "se_country_var";
	step[5]["title"] = "FILTRO SU UNA DATA";
	step[5]["var"] = "se_lastdate_var";
	step[5]["description"] = "Fare click sul campo che identifica il parametro per il filtro su una data.";
function set_step(no){
	var current_created = $("#current_created").val();
	
	if (no == undefined || no == null){
		var no = 1;
	}
	if (no >= step.length){
		no = 5;
	}
	if (no > 1 && no <= 5){
		var prev_no = no - 1;
		var next_no = no + 1;
		if (prev_no > 1){
			prev_no = prev_no - 1;
		}
		if (next_no > 5){
			next_no = 5;
		}
		$("#prev_step").fadeIn(300);
	} else {
		var prev_no = no;
	}
	if (current_created == ""){
		$("#prev_step").fadeOut(150);
	} else {
		$("#skip_step").fadeIn(300);
	}
	
	step[5]["var"] = "se_lastdate_var";
	
	$("#step_no_val").val(no);
	//if (no == prev_no || $("#selected_var_" + prev_no).val() != ""){
		$("#step_no").text(no);
		$("#step_no_span").text(no);
		$("#step_title").text(step[no]["title"]);
		$("#step_description").html("<b>" + step[no]["description"] + "</b>" + auto_txt);
	//}
}
function arrow_clicked(action, step_no_val) {
	if (action == "prev"){
		if (step_no_val >= 1){
			var prev_step = step_no_val -= 1;
			if (prev_step < 1){
				prev_step = 1;
			}
			var current_created = $("#current_created").val();
			var current_created_no1 = current_created - 1;
			if (current_created_no1 == 0){
				// Resetta tutta la storia
				current_created_no1 = "";
				for (cc = 0; cc <= 5; cc++){
					$("#selected_var_" + cc).val("");
					
				}
				$("#content").contents().find(":input['type'=text], :input['type'=radio], :input['type'=checkbox], select, label").css({"z-index": "300" });
			}
			var prev_step_id = $("#selected_var_" + current_created + "_id").val();
			
			// Reimposta lo z-index dell'ultima creazione
			$("#content").contents().find("#" + prev_step_id).css({"z-index": "300"});
			$("#content").contents().find("#_for_" + prev_step_id).css({"z-index": "300"});
			$("#content").contents().find("#__" + prev_step_id).css({"z-index": "300"});
			
			if ($("#content").contents().find(':input[type="text"], :input[type="radio"], :input[type="checkbox"], select, label').css("z-index") == "1"){
				$("#content").contents().find(':input[type="text"], :input[type="radio"], :input[type="checkbox"], select, label').css({"z-index": "300"});
				for (h = 1; h <= current_created; h++){
					var prev_step_id = $("#selected_var_" + h + "_id").val();
					$("#content").contents().find("#" + prev_step_id).css({"z-index": "1"});
				}
			}
			
			// Rimuove l'ultima selezione
			$("#selected_var_" + current_created).val("");
			$("#selected_var_" + current_created + "_id").val("");
			$("#current_created").val(current_created_no1);
			
			set_step(prev_step);
		}
	} else {
		if (step_no_val <= 5){
			var next_step = parseInt(step_no_val);
			next_step += 1;
			if (next_step > 5){
				next_step = 5;
			}
			
			$("#selected_var_" + step_no_val).val("-");
			$("#selected_var_" + step_no_val + "_id").val("-");
			$("#current_created").val(step_no_val);
			
			set_step(next_step);
		}
	}
}
$(document).ready(function(){
	$("#step_no_val").val("1");
	$("#current_created").val("");
	$("#selected_var_1").val("");
	$("#selected_var_1_id").val("");
	$("#selected_var_2").val("");
	$("#selected_var_2_id").val("");
	$("#selected_var_3").val("");
	$("#selected_var_3_id").val("");
	$("#selected_var_4").val("");
	$("#selected_var_4_id").val("");
	$("#selected_var_5").val("");
	$("#selected_var_5_id").val("");
	
	var step_no_val = parseInt($("#step_no_val").val());
	set_step(step_no_val);
	$("#lateral_panel").live('mousemove', function(event) {
		$(this).focus();
	});
});