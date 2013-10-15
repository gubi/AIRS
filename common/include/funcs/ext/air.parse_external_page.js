(function($) {
	$.fn.outerHTML = function() {
		return $(this).animate({boxShadow: "0 0 12px #ff0000"}).css("padding", "3px").find(".focus").css({
			"position": "absolute",
			"top": "0",
			"left": "0",
			"width": "100%",
			"height": "100%",
			"z-index": "400"
		});
	}
})(jQuery);

function finished(div){
	$(":input['type'=text], :input['type'=radio], :input['type'=checkbox], select, label").css({"z-index": "1" });
	div.hide();
	parent.parent.zoombox.close();
	parent.parent.apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Variabili importate correttamente</h1><br />Le variabili necessarie alle ricerche approfondite sono state importate correttamente.<br />Ora puoi aggiungere il tuo motore tra le ricerche automatizzate.</td></tr></table>");
}
$(document).ready(function() {
	var $highlit;
	var $the_id = "";
	var $the_for = "";
	var $the_name = "";
	var $the_type = "";
	
	//parent.$("#skip_step").fadeIn(300);
	var $div = $('<div id="highlighter">').animate({boxShadow: "0 0 12px #ff0000"}).css({
		'box-shadow': '0 0 0 transparent',
		'position': 'absolute',
		'z-index': '65535',
		'padding': '5px',
		'cursor': 'pointer'
	}).hide().prependTo('body');
	$(":input[type='text'], :input[type='checkbox'], :input[type='radio'], select, label").css({
		"z-index": "300", 
		"position": "relative"
	}).live('mousemove', function(event) {
		if (this.nodeName === 'HTML' || this.nodeName === 'BODY'){
			$div.hide();
			return false;
		}
		var $this = this.id === 'highligher' ? $highlit : $(this),
			x = event.pageX,
			y = event.pageY,

			width = $this.width(),
			height = $this.height(),
			offset = $this.offset(),

			minX = offset.left,
			minY = offset.top,
			maxX = minX + width,
			maxY = minY + height;
		
		if (this.id === 'highlighter'){
			if (minX <= x && x <= maxX && minY <= y && y <= maxY){
				// nada
			} else {
				$div.hide();
			}
		} else {
			$the_this = $this;
			$the_id = $this.attr("id");
			$the_for = $this.attr("for");
			$the_name = $this.attr("name");
			$the_type = this.nodeName.toLowerCase();
			
			switch (this.nodeName) {
				case "INPUT":
					if (this.type == "radio"){
						minY = minY - 1;
						minX = minX - 1;
						the_width = $this.width() - 8;
						the_height = $this.height() - 8;
					} else {
						minY = minY - 1;
						minX = minX - 1;
						the_width = $this.width() - 2;
						the_height = $this.height();
					}
					break;
				case "SELECT":
					minY = minY - 2;
					minX = minX - 2;
					the_width = $this.width() + 16;
					the_height = $this.height();
					break;
				case "LABEL":
					minY = minY - 5;
					minX = minX - 5;
					the_width = $this.width();
					the_height = $this.height();
					break;
			}
			offset.top = minY;
			offset.left = minX;
			$highlit = $this;
			$div.offset(offset).width(the_width).height(the_height).show();
		}
		return false;
	});
	$("#highlighter").click(function(event){
		var current = parseInt(parent.$("#step_no_val").val());
		if (current < 6){
			var current_more = current + 1;
			if (current_more > 5){
				current_more = 5;
			}
			if ($the_id != "" && $the_id != undefined){
				parent.$("#selected_var_" + current + "_id").val($the_id);
			} else {
				if ($the_for != "" && $the_for != undefined){
					var input_name = $("#" + $the_for).attr("name");
					
					$($the_this).attr("id", "_for_" + $the_for);
					
					parent.$("#selected_var_" + current).val(input_name);
					parent.parent.$("#" + parent.step[current]["var"]).val(input_name);
					parent.$("#selected_var_" + current + "_id").val($the_for);
					
					parent.$("#current_created").val(current);
					parent.set_step(current_more);
					
					console.log(parent.$("#selected_var_" + current_more).val());
					if(parent.$("#selected_var_" + current_more).val() == "") {
						$("#" + $the_for).css({"z-index": "1"}).blur();
						$("#_for_" + $the_for).css({"z-index": "1"}).blur();
						$div.hide();
					} else {
						finished($div);
					}
				} else {
					$($the_this).attr("id", "__" + $the_name);
					
					parent.$("#selected_var_" + current).val($("#" + $the_name).attr("name"));
					parent.parent.$("#" + parent.step[current]["var"]).val($("#" + $the_name).attr("name"));
					parent.$("#selected_var_" + current + "_id").val("__" + $the_name);
					
					parent.$("#current_created").val(current);
					parent.set_step(current_more);
					
					console.log(parent.$("#selected_var_" + current_more).val());
					if(parent.$("#selected_var_" + current_more).val() == "") {
						$("#" + $the_name).css({"z-index": "1"}).blur();
						$("#__" + $the_name).css({"z-index": "1"}).blur();
						$div.hide();
					} else {
						finished($div);
					}
				}
			}
			if ($the_name != "" && $the_name != undefined){
				parent.$("#selected_var_" + current).val($the_name);
				parent.parent.$("#" + parent.step[current]["var"]).val($the_name);
				parent.$("#current_created").val(current);
				parent.set_step(current_more);
				if(parent.$("#selected_var_" + (current_more)).val() == "") {
					$("#" + $the_id).css({"z-index": "1"}).blur();
					$div.hide();
				} else {
					finished($div);
				}
			}
		}
	});
	parent.$("#skip_step").click(function(event){
		var current = parseInt(parent.$("#current_created").val());
		if (current == 5){
			finished($div);
		}
	});
	$("#content").live("mouseout", function(event) {
		parent.$("#lateral_panel").focus();
	});
});