$(function () {
	var addButton = $('#add-coin'), model = $('#coin-model').clone().removeAttr('id');

	addButton.on('click', function () {
		addButton.parent().before(model.clone());

		return false;
	});

	$('form').on('click', '[data-action=remove-coin]', function () {
		if ($('.control-group').length > 1) {
			$(this).parents('.control-group').remove();
		}

		return false;
	}).on('change', 'select', function () {
		var $this = $(this), container = $this.parent();

		var selects = {
			countries: container.find('[name="countries[]"]'),
			types: container.find('[name="types[]"]'),
			values: container.find('[name="values[]"]'),
			years: container.find('[name="years[]"]')
		};

		var params = {
			countries: selects.countries.val(),
			types: selects.types.val(),
			values: selects.values.val(),
			years: selects.years.val()
		};

		$.post('/coin/find', params, function (data) {
			$.each(data, function (name, values) {
				var select = selects[name];

				select.find('option:gt(0)').remove();

				$.each(values, function (id, value) {
					select.append('<option value="' + id + '">' + value + '</option>');
				});

				select.val(params[name]);
			});
		});
	});
});
