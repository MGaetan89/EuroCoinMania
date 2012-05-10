$(function () {
	$('.btn-toolbar').on('click', 'button', function () {
		var $this = $(this), action = $this.data('markup'), textarea = $('#euro_privatemessagetype_text'), markup;

		switch (action) {
			case 'bold' :
				markup = ' **message** ';
				break;

			case 'image' :
				markup = ' ![message](url) ';
				break;

			case 'italic' :
				markup = ' *message* ';
				break;

			case 'link' :
				markup = ' [message](url) ';
				break;

			case 'quote' :
				markup = "\n" + ' > message ';
				break;

			case 'strike' :
				markup = ' -message- ';
				break;

			case 'underline' :
				markup = ' _message_ ';
				break;
		}

		textarea.html(textarea.html() + markup);

		return false;
	});
});
