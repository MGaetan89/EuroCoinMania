$(function () {
	$.fn.extend({
		getCursorPosition: function() {
			var that = (typeof this[0].name !== 'undefined') ? this[0] : this;

			if ('selectionStart' in that) {
				return that.selectionStart;
			}

			if ('selection' in document) {
				that.focus();

				var range = document.selection.createRange(), length = range.text.length;

				range.moveStart('character', -that.value.length);

				return range.text.length - length;
			}

			return 0;
		},
		getSelection: function () {
			var that = (typeof this[0].name !== 'undefined') ? this[0] : this;

			if (document.selection && document.selection.createRange().text != '') {
				return document.selection.createRange().text;
			}

			return that.value.substring(that.selectionStart, that.selectionEnd);
		},
		insertAtCaret: function(text){
			var that = (typeof this[0].name !== 'undefined') ? this[0] : this;

			if ($.browser.msie) {
				that.focus();

				document.selection.createRange().text = text;
			} else if ($.browser.mozilla || $.browser.webkit) {
				that.value = that.value.substring(0, that.selectionStart) + text + that.value.substring(that.selectionEnd);
			} else {
				that.value += text;
			}

			that.focus();

			return this;
		},
		setCursorPosition: function(pos) {
			var that = (typeof this[0].name !== 'undefined') ? this[0] : this;

			if (pos[0] === '+') {
				pos = parseInt(pos.substring(1)) + target.getCursorPosition();
			} else if (pos[0] === '-') {
				pos = target.getCursorPosition() - parseInt(pos.substring(1));
			}

			if (that.setSelectionRange) {
				that.setSelectionRange(pos, pos);
			} else if (that.createTextRange) {
				var range = that.createTextRange();

				range.collapse(true);
				range.moveEnd('character', pos);
				range.moveStart('character', pos);
				range.select();
			}

			return this;
		}
	})

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
			target.insertAtCaret(markup.replace('?', target.getSelection())).setCursorPosition('+' + markup.indexOf('?'));
		}
	});
});
