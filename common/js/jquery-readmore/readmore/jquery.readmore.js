/**
 * jquery.readmore
 * 
 * Substring long paragraphs and make expandable with "Read More" link.
 * Paragraph will be split either at exactly 'substr_len' chars or at the next
 * space char after 'substr_len' words (default).
 * 
 * @date 02 Jul 2012
 * @author Jake Trent (original author) http://www.builtbyjake.com
 * @author Mike Wendt http://www.mikewendt.net
 * @version 1.6
 */
(function ($) {
	$.fn.readmore = function (settings) {
		var defaults = {
			substr_len: 125,
			split_word: false,
			show_txt: '» Estendi',
			hide_txt: '« Contrai',
			ellipses: '&#8230;',
			more_clzz: 'readm-more',
			ellipse_clzz: 'readm-continue',
			hidden_clzz: 'readm-hidden'
		};

		var opts = $.extend({}, defaults, settings);
		this.each(function () {
			var $this = $(this);
			if ($this.html().length > opts.substr_len) {
				abridge($this);
				linkage($this);
			} else {
				linkage($this);
			}
		});
		function linkage(elem) {
			elem.append('<a href="javascript: void(0);" class="' + opts.more_clzz + '" style="display: inline-block;">' + opts.show_txt + '</a>');
			elem.find('.' + opts.more_clzz).click(function () {
				elem.find('.' + opts.hidden_clzz).slideToggle(300, function (){
					elem.find('.' + opts.more_clzz).text($(this).is(':visible') ? opts.hide_txt : opts.show_txt);
				});
			});
		}
		function abridge(elem) {
			var txt = elem.html();
			var dots = "<span class='" + opts.ellipse_clzz + "'>" + opts.ellipses + "</span>";
			var shown = txt.substring(0, (opts.split_word ? opts.substr_len : txt.indexOf(' ', opts.substr_len))) + dots;
			var hidden =
				'<span class="' + opts.hidden_clzz + '" style="display:none;">' +
					txt.substring((opts.split_word ? opts.substr_len : txt.indexOf(' ', opts.substr_len)), txt.length) +
				'</span>';
			elem.html(shown + hidden);
		}
		return this;
	};
})(jQuery);