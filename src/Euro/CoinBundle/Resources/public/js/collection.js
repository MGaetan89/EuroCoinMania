$(function () {
	var body = $('body');

	// Manage Coin tooltip information
	body.on('mouseover', 'td a.coin-info', function () {
		var $this = $(this), coin = $this.data('coin'), id = 'coin-info-' + coin;

		if ($('#' + id).length) {
			$('#' + id).popover('toggle');
		} else {
			$.post('/coin/' + coin + '/get', function (data) {
				data = $(data).appendTo(body);

				$this.popover({
					content: data.html(),
					placement: 'left'
				}).popover('show').removeClass('wait');
			});
		}
	}).on('click', 'td div.coin-collection button:enabled', function () {
		var $this = $(this), quantity = $this.siblings('button.coin-quantity'),
			id = $this.parent('.coin-collection').data('uc') || 0;

		$this.siblings('button:enabled').andSelf().prop('disabled', true);
		if ($this.hasClass('coin-remove') && id > 0) {
			$this.addClass('btn-danger');
			$.post('/coin/' + id + '/remove', function (data) {
				if (data != quantity.text()) {
					quantity.text(data);

					$this.siblings('button.coin-add').prop('disabled', false);
					if (data > 0) {
						$this.siblings('button.coin-remove').prop('disabled', false);
					}

					$this.removeClass('btn-danger');
				} else {
					// Display error message
				}
			});
		} else if ($this.hasClass('coin-add')) {
			if (id == 0) {
				id = 'c' + $this.parent('.coin-collection').data('coin');
			}

			$this.addClass('btn-success');
			$.post('/coin/' + id + '/add', function (data) {
				if (data != quantity.text()) {
					quantity.text(data);
					$this.siblings('button.coin-add, button.coin-remove').andSelf().prop('disabled', false);
					$this.removeClass('btn-success');
				} else {
					// Display error message
				}
			});
		}
	});
});