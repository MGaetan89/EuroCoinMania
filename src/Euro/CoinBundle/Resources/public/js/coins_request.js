$(function () {
	var coins_list = $('#coins_list .divider'), count = 0, model = $('#item-model').detach().html(), submit = $('#exchange'), total = 0, total_elt = coins_list.next('.nav-header').find('span');

	submit.on('click', function (e) {
		var coins = [];

		$('.btn.active').each(function () {
			coins.push($(this).data('coin'));
		});

		coins.sort();

		$('#coins').val(coins);
	});

	$('table').on('click', 'button.btn', function () {
		var $this = $(this), id = 'coin-' + $this.data('coin'), value = $this.data('value');

		if ($this.is('.active')) {
			count--;
			total -= value;

			$('#' + id).remove();
		} else {
			count++;
			total += value;

			var elt = $('<li id="' + id + '"></li>').html(model.replace('%COIN%', $this.data('desc'))).data('value', value);

			coins_list.before(elt.data('value', value));
		}

		total_elt.text(Math.round(total * 100) / 100);

		if (count == 0) {
			submit.addClass('disabled').prop('disabled', true);
		} else if (count == 1) {
			submit.removeClass('disabled').removeAttr('disabled');
		}
	});

	$('#coins_list').on('click', '[data-action=remove-coin]', function () {
		var parent = $(this).parent(), id = parent.attr('id').replace('coin-', '');

		total -= parent.data('value');
		total_elt.text(Math.round(total * 100) / 100);

		$('button[data-coin=' + id + ']').button('toggle');
		parent.detach();
	});

	var filters = {
		country: null,
		year: null,
	}

	function restore() {
		$('[data-action=show-country]').css('opacity', 1);
		$('[data-action=show-year]').css('font-weight', 'normal');
		$('.table-header').show();

		if (filters.country != null) {
			$('[data-action=show-country]').css('opacity', .4);
			$('[data-target="' + filters.country + '"]').css('opacity', 1);
			$('.filter-country').hide().filter(filters.country).show();
		} else {
			$('.filter-country').show();
		}

		if (filters.year != null) {
			$('[data-target="' + filters.year + '"]').css('font-weight', 'bold');
			$('.table-header').hide();
			$('.filter-year').hide().filter(filters.year).show().parents('table').find('.table-header:first').show();

			$('table').each(function () {
				var $this = $(this).find('tr:visible');

				if ($this.size() == $this.filter('.table-header').size()) {
					$(this).hide();
				}
			});
		} else {
			$('.filter-year').show();
		}

		$('#no-coins').toggle($('.filter-country:visible').size() == 0);

		return false;
	}

	$('#filters').on('click', '[data-action=reset-country-filter]', function () {
		filters.country = null;

		return restore();
	}).on('click', '[data-action=show-country]', function () {
		if (filters.country == $(this).data('target')) {
			filters.country = null;
		} else {
			filters.country = $(this).data('target');
		}

		return restore();
	}).on('click', '[data-action=reset-year-filter]', function () {
		filters.year = null;

		return restore();
	}).on('click', '[data-action=show-year]', function () {
		if (filters.year == $(this).data('target')) {
			filters.year = null;
		} else {
			filters.year = $(this).data('target');
		}

		return restore();
	});
});

