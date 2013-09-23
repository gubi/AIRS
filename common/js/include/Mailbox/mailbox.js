/**
* Generate GUI for Mailbox main page
* 
* Javascript and jQuery
*
* @category	SystemScript
* @package	AIRS_Mailbox
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
<!--
$(function(){
	$.contextMenu({
		selector: '.context-menu', 
		callback: function(key, options) {
			var the_id = "";
			var the_rel = "";
			var the_name = "";
			$.each(options.$trigger, function(item, data){
				the_id += data.id;
				the_rel += data.rel;
				the_name += data.name;
			});
			switch(key){
				case "open":
					show(the_rel);
					break;
				case "new_folder":
					apprise("Nome della directory:", {"input": true, "textOK": "Crea"}, function(r){
						if(r){
							new_dir(the_rel, r);
						}
					});
					break;
				case "search":
					search(the_rel);
					break;
				case "read":
					set_as_read(the_rel);
					break;
			}
		},
		items: {
			"open": {name: "Apri", icon: "open"},
			"sep1": "---------",
			"new_folder": {name: "Nuova cartella...", icon: "new"},
			"search": {name: "Cerca...", icon: "search"},
			"sep2": "---------",
			"read": {name: "Marca la cartella come letta", icon: "read"}
		}
	});
	$.contextMenu({
		selector: '.context-menu-folder',
		callback: function(key, options) {
			var the_id = "";
			var the_rel = "";
			var the_name = "";
			$.each(options.$trigger, function(item, data){
				the_id += data.id;
				the_rel += data.rel;
				the_name += data.name;
			});
			switch(key){
				case "open":
					show(the_rel);
					break;
				case "rename":
					apprise("Nome della directory:", {"input": the_name, "textOK": "Rinomina"}, function(r){
						if(r){
							rename(the_rel, r);
						}
					});
					break;
				case "clear":
					clear(the_rel);
					break;
				case "new_subfolder":
					apprise("Nome della directory:", {"input": true, "textOK": "Crea"}, function(r){
						if(r){
							new_dir(the_rel, r);
						}
					});
					break;
				case "search":
					search(the_rel);
					break;
				case "read":
					set_as_read(the_rel);
					break;
			}
		},
		items: {
			"open": {name: "Apri", icon: "open"},
			"rename": {name: "Rinomina...", icon: "rename"},
			"clear": {name: "Elimina...", icon: "clear"},
			"sep1": "---------",
			"new_subfolder": {name: "Nuova sottocartella...", icon: "new"},
			"search": {name: "Cerca nella cartella...", icon: "search"},
			"sep2": "---------",
			"read": {name: "Marca la cartella come letta", icon: "read"}
		}
	});
	$.contextMenu({
		selector: '.context-menu-junk', 
		callback: function(key, options) {
			var the_id = "";
			var the_rel = "";
			var the_name = "";
			$.each(options.$trigger, function(item, data){
				the_id += data.id;
				the_rel += data.rel;
				the_name += data.name;
			});
			switch(key){
				case "open":
					show(the_rel);
					break;
				case "empty":
					empty(the_rel);
					break;
				case "new_subfolder":
					apprise("Nome della directory:", {"input": true, "textOK": "Crea"}, function(r){
						if(r){
							new_dir(the_rel, r);
						}
					});
					break;
				case "search":
					search(the_rel);
					break;
				case "read":
					set_as_read(the_rel);
					break;
			}
		},
		items: {
			"open": {name: "Apri", icon: "open"},
			"sep1": "---------",
			"new_subfolder": {name: "Nuova sottocartella...", icon: "new"},
			"search": {name: "Cerca...", icon: "search"},
			"sep2": "---------",
			"read": {name: "Marca la cartella come letta", icon: "read"},
			"sep3": "---------",
			"empty": {name: "Svuota posta indesiderata", icon: "empty"}
		}
	});
	$.contextMenu({
		selector: '.context-menu-trash', 
		callback: function(key, options) {
			var the_id = "";
			var the_rel = "";
			var the_name = "";
			$.each(options.$trigger, function(item, data){
				the_id += data.id;
				the_rel += data.rel;
				the_name += data.name;
			});
			switch(key){
				case "open":
					show(the_rel);
					break;
				case "rename":
					apprise("Nome della directory:", {"input": the_name, "textOK": "Rinomina"}, function(r){
						if(r){
							rename(the_rel, r);
						}
					});
					break;
				case "empty_trash":
					empty_trash(the_rel);
					break;
				case "new_subfolder":
					apprise("Nome della directory:", {"input": true, "textOK": "Crea"}, function(r){
						if(r){
							new_dir(the_rel, r);
						}
					});
					break;
				case "search":
					search(the_rel);
					break;
				case "read":
					set_as_read(the_rel);
					break;
			}
		},
		items: {
			"open": {name: "Apri", icon: "open"},
			"sep1": "---------",
			"new_subfolder": {name: "Nuova sottocartella...", icon: "new"},
			"search": {name: "Cerca...", icon: "search"},
			"sep2": "---------",
			"read": {name: "Marca la cartella come letta", icon: "read"},
			"sep3": "---------",
			"empty_trash": {name: "Svuota cestino", icon: "empty"}
		}
	});
});
-->