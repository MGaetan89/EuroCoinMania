$(function () {
	var body = $('body'), opened = null, placement = location.pathname.match('collector') ? 'left' : 'bottom';

	$('[data-action=add-coin], [data-action=remove-coin]').on('click', function () {
		var $this = $(this), action = $this.data('action').split('-')[0], id = $this.parents('[data-coin]').data('coin');

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

	$('[data-action=hide-items]').on('click', function () {
		$(this).nextUntil('.divider', 'li').toggle();

		return false;
	});

	$('[data-action=query-coin-info]').on('click', function () {
		var $this = $(this).button('loading'), id = $this.parents('[data-coin]').data('coin');

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
					placement: placement,
					trigger: 'manual'
				}).popover('toggle').button('reset');
			});
		}
	});

	$('[data-action=popover]').popover({
		trigger: 'hover'
	});

	$('#customize_filter').on('submit', function () {
		var $this = $(this), from = $this.find('[name=from]').val(), to = $this.find('[name=to]').val();

		if (!from.match(/[0-9]{4}/) || (to && (!to.match(/[0-9]{4}/) || from > to))) {
			return false;
		}

		if (from == to) {
			to = undefined;
		}

		if (to) {
			location.href = $this.attr('action').replace('FROM..TO', from + '..' + to);
		} else {
			location.href = $this.attr('action').replace('FROM..TO', from);
		}

		return false;
	});
});
