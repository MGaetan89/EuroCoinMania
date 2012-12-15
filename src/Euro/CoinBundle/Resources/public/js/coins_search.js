$(function () {
	var addButton = $('#add-coin'), model = $('#coin-model');

	addButton.on('click', function () {
		addButton.parent().before(model.clone().removeAttr('id'));

		return false;
	});

	$('form').on('click', '[data-action=remove-coin]', function () {
		if ($('.control-group').length > 1) {
			$(this).parents('.control-group').remove();
		}

		return false;
	});
});

