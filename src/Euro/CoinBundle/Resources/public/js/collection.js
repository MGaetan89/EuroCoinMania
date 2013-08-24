$(function () {
	var body = $('body'),
		modal = $('#coin-modal'),
		quantityTotal = $('#quantity-total'),
		text = '−';

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

	$('form[id^=report-form-]').on('submit', function (e) {
		e.preventDefault();

		var data = {};
		var formData = $(this).serializeArray();
		for (var i = 0; i < formData.length; ++i) {
			data[formData[i].name] = formData[i].value;
		}

		$.post('/coin/propose', data);
	});

	$('table').on('click', '[data-action=add-coin], [data-action=remove-coin]', function () {
		var $this = $(this),
			action = $this.data('action').split('-')[0],
			id = $this.parents('[data-coin]').data('coin'),
			quantityInput = $this.siblings('.quantity'),
			quantity = parseInt(quantityInput.val()),
			quantityElt = $('#quantity-' + id);

		if (isNaN(quantity) || quantity <= 0) {
			// Display error message

			return;
		}

		if ($this.hasClass('disabled')) {
			return;
		}

		$this.button('loading');

		$.post('/coin/' + id + '/' + action + '/' + quantity, function (newQuantity) {
			if (!newQuantity) {
				return;
			}

			var index = quantityElt.text(newQuantity).parents('td').index(),
			total = quantityTotal.find('th:eq(' + index + ') .quantity'),
			oldTotal = parseInt(total.text().replace(/[^0-9]/g, ''));

			if (action == 'add') {
				total.text(oldTotal + quantity);
			} else {
				total.text(oldTotal - quantity);
			}

			if (newQuantity > 0) {
				if (action === 'add') {
					$this.siblings('[data-action=remove-coin]').button('reset');
					$this.parents('.coin').removeClass('error');
				}

				$this.button('reset');
			} else if (newQuantity == 0) {
				$this.parents('.coin').addClass('error');
			}

			quantityElt.parent().next('[data-hover=popover]').popover('hide');
		});
	}).on('click', '[data-action=query-coin-info]', function () {
		var $this = $(this).button('loading'), id = $this.parents('[data-coin]').data('coin');

		if ($('#coin-info-' + id).length) {
			$this.button('reset');
		} else {
			$.post('/coin/' + id + '/get', function (data) {
				data = $($.trim(data)).hide().appendTo(body);

				$this.popover({
					content: $(data).html(),
					html: true,
					placement: 'left',
					template: '<div class="popover"><div class="close" data-dismiss="alert">&times;</div><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>'
				}).popover('show').button('reset');
			});
		}
	}).on('click', 'i[data-action=toggle-collection]', function () {
		$(this).toggleClass('icon-minus icon-plus').parents('tr').nextUntil('.values', 'tr').slideToggle();
	});

	body.on('click', '.zoomable img', function () {
		var $this = $(this), img = modal.css('display', 'table').find('img');

		img.data('loader', img.attr('src')).attr({
			alt: $this.attr('alt'),
			src: $this.attr('src').replace('_small', '_big'),
			title: $this.attr('title')
		});
	});

	modal.on('click', '.btn', function () {
		var img = modal.hide().find('img');

		img.attr('src', img.data('loader'));
	});

	$('#customize_filter').on('submit', function () {
		var $this = $(this), from = $this.find('[name=from]').val(), to = $this.find('[name=to]').val();

		if (!from.match(/[0-9]{4}/) || (to && (!to.match(/[0-9]{4}/) || from > to))) {
			return false;
		}

		if (from == to) {
			location.href = $this.attr('action').replace('FROM..TO', from);
		} else {
			location.href = $this.attr('action').replace('FROM..TO', from + '..' + to);
		}

		return false;
	});
});
