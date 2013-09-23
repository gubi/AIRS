// Apprise 1.5 by Daniel Raftery
// http://thrivingkings.com/apprise
//
// Button text added by Adam Bezulski
//
// Dependancies: iphonePassword for key feature
//

function apprise(string, args, callback){
	var default_args =
		{
		'confirm'		:	false, 		// Ok and Cancel buttons
		'verify'		:	false,		// Yes and No buttons
		'input'		:	false, 		// Text input (can be true or string for default text)
		'key'			:	false, 		// Text input encryption key (can be true or string for default text) ~ need jQuery iphone-password: http://code.google.com/p/iphone-password/
		'password'	:	false, 		// Password input (can be true or string for default text)
		'animate'		:	false,		// Groovy animation (can true or number, default is 400)
		'noButton'	:	false,
		'textarea'		:	false,
		'textOk'		:	'Ok',		// Ok button default text
		'textCancel'	:	'Annulla',	// Cancel button default text
		'textYes'		:	'Si',		// Yes button default text
		'textNo'		:	'No'		// No button default text
		}
	
	if(args) {
		close_apprise();
		for(var index in default_args) {
			if(typeof args[index] == "undefined") {
				args[index] = default_args[index];
			}
		}
	}
	
	var aHeight = $(document).height();
	var aWidth = $(document).width();
	$('body').append('<div class="appriseOverlay" id="aOverlay"></div>');
	$('.appriseOverlay').css('height', aHeight).css('width', aWidth).fadeIn(100);
	$('body').append('<div class="appriseOuter"></div>');
	$('.appriseOuter').append('<div class="appriseInner"></div>');
	$('.appriseInner').append(string);
	$('.appriseOuter').css("left", ( $(window).width() - $('.appriseOuter').width() ) / 2+$(window).scrollLeft() + "px");
    
	if(args){
		if(args['animate']){
			var aniSpeed = args['animate'];
			if(isNaN(aniSpeed)) { aniSpeed = 350; }
			$('.appriseOuter').css('top', '-200px').fadeIn(aniSpeed);
			$('.appriseOuter').css({"marginTop": "-12.5%"}).animate({top:"50%"}, aniSpeed);
		} else {
			$('.appriseOuter').css({"marginTop": "-12.5%", "top": "50%"}).fadeIn(600);
		}
	} else {
		$('.appriseOuter').css({"marginTop": "-" + $('.appriseOuter').height()/2 + "px", "top": "50%"}).fadeIn(600);
	}
	if(args) {
		if(args['input']) {
			if ($('.appriseInnerContent').length == 0){
				var inner = 'Inner';
			} else {
				var inner = 'InnerContent';
			}
			if(typeof(args['input'])=='string') {
				$('.apprise' + inner).append('<div class="aInput"><input type="text" class="aTextbox" t="aTextbox" value="'+args['input']+'" /></div>');
			} else {
				$('.apprise' + inner).append('<div class="aInput"><input type="text" class="aTextbox" t="aTextbox" /></div>');
			}
			$('.aTextbox').focus();
		} else if(args['textarea']) {
			if ($('.appriseInnerContent').length == 0){
				var inner = 'Inner';
			} else {
				var inner = 'InnerContent';
			}
			if(typeof(args['textarea'])=='string') {
				$('.apprise' + inner).append('<div class="aInput"><textarea class="aTextbox" t="aTextbox">'+args['input']+'</textarea></div>');
			} else {
				$('.apprise' + inner).append('<div class="aInput"><textarea class="aTextbox" t="aTextbox"></textarea></div>');
			}
			$('.aTextbox').focus();
		} else if(args['key']) {
			if ($('.appriseInnerContent').length == 0){
				var inner = 'Inner';
			} else {
				var inner = 'InnerContent';
			}
			if(typeof(args['key'])=='string') {
				$('.apprise' + inner).append('<div class="aInput"><table><tr><th>Chiave di cifratura:</th><td><input size="36" type="password" name="key" class="aTextbox" t="aTextbox" value="'+args['key']+'" /></td></tr></table></div>');
			} else {
				$('.apprise' + inner).append('<div class="aInput"><table><tr><th>Chiave di cifratura:</th><td><input size="36" type="password"  name="key" class="aTextbox" t="aTextbox" /></td></tr></table></div>');
			}
			$('.aTextbox').iphonePassword({mask: '•', duration: 1000}).focus();
			$('.aTextbox').focus();
		} else if(args['password']) {
			if ($('.appriseInnerContent').length == 0){
				var inner = 'Inner';
			} else {
				var inner = 'InnerContent';
			}
			if(typeof(args['password'])=='string') {
				$('.apprise' + inner).append('<div class="aInput"><table><tr><th>Password:</th><td><input type="password" class="aTextbox" t="aTextbox" value="'+args['password']+'" /></td></tr></table></div>');
			} else {
				$('.apprise' + inner).append('<div class="aInput"><table><tr><th>Password:</th><td><input type="password" class="aTextbox" t="aTextbox" /></td></tr></table></div>');
			}
			$('.aTextbox').focus();
		}
		if (args['noButton']){
			$(".appriseInner").css({"padding-bottom": "20px"});
		} else {
			$('.appriseInner').append('<div class="aButtons"></div>');
			if(args) {
				if(args['confirm'] || args['input'] || args['key'] || args['password'] || args['textarea']) { 
					$('.aButtons').append('<button value="ok">'+args['textOk']+'</button>');
					$('.aButtons').append('<button value="cancel">'+args['textCancel']+'</button>'); 
				} else if(args['verify']) {
					$('.aButtons').append('<button value="ok">'+args['textYes']+'</button>');
					$('.aButtons').append('<button value="cancel">'+args['textNo']+'</button>');
				} else {
					$('.aButtons').append('<button value="ok">'+args['textOk']+'</button>');
				}
			} else {
				$('.aButtons').append('<button value="ok">Ok</button>');
			}
		}
	}
	
	$(document).keyup(function(e)  {
		if($('.appriseOverlay').is(':visible')) {
			if(args) {
				if(!args['textarea']){
					if(e.keyCode == 13)  { $('.aButtons > button[value="ok"]').click(); }
				}
			}
			if(e.keyCode == 27)  { $('.aButtons > button[value="cancel"]').click(); close_apprise(); }
		}
	});
	
	var aText = $('.aTextbox').val();
	if(!aText) { aText = false; }
	$('.aTextbox').keyup(function(){
		if(args){
			if (args['key']){
				aText = $(this).attr("real");
			} else {
				aText = $('.aTextbox').val();
			}
		} else {
			aText = $(this).val();
		}
	});
	
	$('.aButtons > button').click(function(){
		$('.appriseOverlay').fadeOut(150, function(){ $(this).remove(); });
		$('.appriseOuter').fadeOut(150, function(){ $(this).remove(); });
		if(callback){
			var wButton = $(this).attr("value");
			if(wButton=='ok') { 
				if(args){
					if(args['input'] || args['key'] || args['password'] || args['textarea']){
						callback(aText);
					} else {
						callback(true);
					}
				} else {
					callback(true);
				}
			} else if(wButton=='cancel') {
				callback(false);
			}
		}
	});
	$(".appriseOverlay").click(function(){
		if(callback){
			callback(false);
		}
		close_apprise();
	});
}
function close_apprise(){
	$('.appriseOverlay').fadeOut(300, function(){ $(this).remove(); });
	$('.appriseOuter').fadeOut(300, function(){ $(this).remove(); });
}