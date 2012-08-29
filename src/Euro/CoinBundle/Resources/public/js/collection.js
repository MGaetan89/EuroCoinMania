$(function () {
	var body = $('body'), opened = null;

	$('[data-action=add-coin], [data-action=remove-coin]').on('click', function () {
		var $this = $(this), action = $this.data('action').split('-')[0], id = $this.parents('.btn-toolbar').data('coin');

		if ($this.hasClass('disabled')) {
			return;
		}

		$this.button('loading');

		$.post('/coin/' + id + '/' + action, function (quantity) {
			if (!quantity) {
				return;
			}

			$('#quantity-' + id).text(quantity);

			if (quantity > 0) {
				if (action === 'add' && quantity == 1) {
					$this.siblings('[data-action=remove-coin]').button('reset');
				}

				$this.button('reset');
			}
		});
	});

	$('[data-action=query-coin-info]').on('click', function () {
		var $this = $(this).button('loading'), id = $this.parents('.btn-toolbar').data('coin');

		if (opened) {
			opened.popover('hide');

			if (opened[0] == $this[0]) {
				opened = null;
				$this.button('reset');

				return;
			}

			opened = null;
		}

		if ($('#coin-info-' + id).length) {
			opened = $this.popover('toggle').button('reset');
		} else {
			$.post('/coin/' + id + '/get', function (data) {
				data = $(data).appendTo(body);

				opened = $this.popover({
					content: data.html(),
					placement: 'bottom',
					trigger: 'manual'
				}).popover('toggle').button('reset');
			});
		}
	});

	$('[data-action=popover]').popover({
		trigger: 'hover'
	});
});
