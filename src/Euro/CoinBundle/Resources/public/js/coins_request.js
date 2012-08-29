$(function () {
	var coins_list = $('#coins_list .divider'), count = 0, submit = $('#exchange'), total = 0, total_elt = coins_list.next('.nav-header').find('span');

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

			var elt = $('<li id="' + id +'"><a>' + $this.data('desc') + '</a></li>');

			coins_list.before(elt.data('value', value));
		}

		total_elt.text(Math.round(total * 100) / 100);

		if (count == 0) {
			submit.addClass('disabled').prop('disabled', true);
		} else if (count == 1) {
			submit.removeClass('disabled').removeAttr('disabled');
		}
	});
});
