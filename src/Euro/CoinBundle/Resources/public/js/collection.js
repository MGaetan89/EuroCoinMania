$(function () {
	var body = $('body');

	// Manage Coin tooltip information
	body.on('click', 'td a.coin-info', function () {
		var $this = $(this), coin = $this.data('coin'), id = 'coin-info-' + coin;

		if ($('#' + id).length) {
			$this.popover('toggle');
		} else {
			$this.addClass('wait');
			$.post('/coin/' + coin + '/get', function (data) {
				data = $(data).appendTo(body);

				$this.popover({
					content: data.html(),
					placement: 'left',
					trigger: 'manual'
				}).popover('toggle').removeClass('wait');
			});
		}

		return false;
	}).on('click', 'td div.coin-collection button:enabled', function () {
		var $this = $(this), quantity = $this.siblings('button.coin-quantity'),
		id = $this.parent('.coin-collection').data('uc') || 0;

		$this.siblings('button:enabled').andSelf().prop('disabled', true);
		if ($this.hasClass('coin-remove') && id > 0) {
			$.post('/coin/' + id + '/remove', function (data) {
				if (data != quantity.text()) {
					quantity.text(data);

					$this.siblings('button.coin-add').prop('disabled', false);
					$this.prop('disabled', data <= 0)
					.toggleClass('btn-danger', data != 0);
				} else {
				// Display error message
				}
			});
		} else if ($this.hasClass('coin-add')) {
			if (id == 0) {
				id = 'c' + $this.parent('.coin-collection').data('coin');
			}

			$.post('/coin/' + id + '/add', function (data) {
				data = $.parseJSON(data);
				if (data.quantity != quantity.text()) {
					quantity.text(data.quantity);

					$this.siblings('button.coin-add,button.coin-remove').andSelf().prop('disabled', false);
					$this.siblings('button.coin-remove').toggleClass('btn-danger', data.quantity > 0);

					$this.parent('.coin-collection').data('uc', data.uc);
				} else {
				// Display error message
				}
			});
		}
	});
});
