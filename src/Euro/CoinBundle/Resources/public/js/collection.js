$(function () {
	var body = $('body'), idPrefix = 'coin-info-';

	body.on('mouseover', 'td a[id^="' + idPrefix + '"]', function () {
		var $this = $(this),
		id = parseInt($this.attr('id').replace(idPrefix, '')),
		newId = idPrefix + id + '-popover';

		if ($('#' + newId).length) {
			$('#' + newId).popover('toggle');
		} else {
			$.post('/coin/' + id + '/get', function (data) {
				data = $(data).appendTo(body);

				$this.popover({
					content: data.html(),
					placement: 'left'
				}).popover('show');
			});
		}
	});
});
