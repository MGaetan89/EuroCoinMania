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
		value: null,
		year: null,
	}

	function restore() {
		// Reset country filter marker
		$('[data-action=show-country]').css('opacity', 1);
		// Reset value & year filters markers
		$('[data-action=show-value], [data-action=show-year]').css('font-weight', 'normal');
		// Show all cells
		$('table:hidden, table :hidden').show();

		if (filters.country != null) {
			// Mark the right country filter
			$('[data-action=show-country]:not([data-target="' + filters.country + '"])').css('opacity', .4);
			// Hide tables for other countries
			$('.filter-country:not(' + filters.country + ')').hide();
		}

		if (filters.value != null) {
			// Mark the right value filter
			$('[data-target="' + filters.value + '"]').css('font-weight', 'bold');
			// Hide columns for other values
			$('table [class^=value-]:not(' + filters.value + ')').hide();
		}

		if (filters.year != null) {
			// Mark the right year filter
			$('[data-target="' + filters.year + '"]').css('font-weight', 'bold');
			// Hide rows for other years
			$('.filter-year:not(' + filters.year + ')').hide();
		}

		// Improve display
		$('table:visible').each(function () {
			var table = $(this), rows = table.find('tr:visible');

			// Hide empty tables
			if (rows.length == table.find('tr.table-header').length) {
				return table.hide();
			}

			// Hide table headers
			table.find('.table-header:gt(0)').hide();

			// Hide empty rows
			rows.each(function () {
				var row = $(this);

				if (row.find('td:visible:not(:empty)').length == 1) {
					row.hide();
				}
			});

			// Hide empty tables
			table.toggle(table.find('tr:visible:not(.table-header)').length != 0);
		});

		// Display message if no coins match
		$('#no-coins').toggle($('.filter-country:visible').size() == 0);

		return false;
	}

	$('#filters').on('click', '[data-action^=reset-][data-action$=-filter]', function () {
		var filter = $(this).data('action').match(/reset-([a-z]+)-filter/)[1];

		filters[filter] = null;

		return restore();
	}).on('click', '[data-action^=show-]', function () {
		var $this = $(this), target = $this.data('target'),
			filter = $this.data('action').match(/show-([a-z]+)/)[1];

		if (filters[filter] == target) {
			filters[filter] = null;
		} else {
			filters[filter] = target;
		}

		return restore();
	});
});

