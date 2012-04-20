$(function () {
	var body = $('body');

	// Manage Coin tooltip information
	body.on('mouseover', 'td a.coin-info', function () {
		var $this = $(this), coin = $this.data('coin'), id = 'coin-info' + coin;

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
	});
});
