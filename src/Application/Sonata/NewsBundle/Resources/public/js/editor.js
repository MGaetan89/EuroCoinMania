$(function () {
	String.prototype.reverse = function () {
		return this.split('').reverse().join('');
	};

	$.fn.setCursorPosition = function(pos) {
		var $this = $(this).get(0);

		if ($this.setSelectionRange) {
			$this.setSelectionRange(pos, pos);
		} else if ($this.createTextRange) {
			var range = $this.createTextRange();

			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
		}
	}

	var editor = $('[data-action=editor]'), target = $('#' + editor.data('target'));
	var tags = {
		'editor-bold': '**?**',
		'editor-ordered-list': '1. ?',
		'editor-image': '![?](url)',
		'editor-italic': '*?*',
		'editor-link': '[?](url)',
		'editor-list': '* ?',
		'editor-separator': '-----\n?',
		'editor-underline': '<u>?</u>'
	};

	editor.on('click', '.btn', function () {
		var tag = $(this).attr('class').replace('btn ', ''), markup = tags[tag];

		if (markup != undefined) {
			var text = target.val();

			target.val(text + markup.replace('?', ''));
			target.setCursorPosition(text.length + markup.indexOf('?'));
		}
	});
});
