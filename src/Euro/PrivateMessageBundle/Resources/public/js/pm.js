$(function () {
	$('.btn-toolbar').on('click', 'button', function () {
		var $this = $(this), action = $this.data('markup'),
		textarea = $('#euro_privatemessagetype_text'),
		markdown = {
			bold: ' **message** ',
			image: ' ![message](url) ',
			italic: ' *message* ',
			link: ' [message](url) ',
			quote: "\n" + ' > message ',
			strike: ' <s>message</s> ',
			underline: ' <u>message</u> ',
			header1: '# message ' + "\n",
			header2: '## message ' + "\n",
			header3: '### message ' + "\n"
		};

		textarea.val(textarea.val() + markdown[action]).focus();

		return false;
	});
});
