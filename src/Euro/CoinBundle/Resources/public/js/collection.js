$(function () {
	var body = $('body'),
		modal = $('#coin-modal'),
		opened = null,
		placement = location.pathname.match('commemorative') ? 'left' : 'right',
		quantityTotal = $('#quantity-total');

	var text = '−';
	$('[data-action=hide-items]').on('click', function () {
		var $this = $(this);

		$this.nextUntil('.divider', 'li').slideToggle();
		$this.find('span').text(text);
		text = (text == '−') ? '+' : '−';

		return false;
	});

	$('[data-action=popover]').popover({
		html: true,
		trigger: 'hover'
	});

	$('table').on('click', '[data-action=add-coin], [data-action=remove-coin]', function () {
		var $this = $(this), action = $this.data('action').split('-')[0], id = $this.parents('[data-coin]').data('coin');

		if ($this.hasClass('disabled')) {
			return;
		}

		$this.button('loading');

		$.post('/coin/' + id + '/' + action, function (quantity) {
			if (!quantity) {
				return;
			}

			var index = $('#quantity-' + id).text(quantity).index(),
				total = quantityTotal.find(':eq(' + index + ') .quantity'),
				oldTotal = parseInt(total.text().replace(/[^0-9]/g, ''));

			if (action == 'add') {

				total.text(oldTotal + 1);
			} else {
				total.text(oldTotal - 1);
			}

			if (quantity > 0) {
				if (action === 'add' && quantity == 1) {
					$this.siblings('[data-action=remove-coin]').button('reset');
				}

				$this.button('reset');
			}
		});
	}).on('click', '[data-action=query-coin-info]', function () {
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
			opened = $this.button('reset');
		} else {
			$.post('/coin/' + id + '/get', function (data) {
				data = $(data).hide().appendTo(body);

				opened = $this.popover({
					content: $(data).html(),
					html: true,
					placement: placement
				}).popover('show').button('reset');
			});
		}
	}).on('click', 'i[data-action=toggle-collection]', function () {
		$(this).toggleClass('icon-minus icon-plus')
			.parents('tr').nextUntil('.values', 'tr').slideToggle();
	});

	$('body').on('click', '.zoomable img', function () {
		var $this = $(this);

		modal.css('display', 'table').find('img').attr({
			alt: $this.attr('alt'),
			src: $this.attr('src').replace('_small', '_big'),
			title: $this.attr('title')
		});
	});

	modal.on('click', '.btn', function () {
		modal.hide();
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
