$(function () {
	var color = {
		from: '#0044CC',
		to: '#006DCC'
	}, map = $('#map'), countries = map.data('countries');

	$.get(map.attr('src'), function (data) {
		map.replaceWith($(data).find('svg').attr('id', map.attr('id')));
		map = $('#map');

		$('table tbody').on('mouseenter', 'tr', function () {
			var id = $(this).attr('id').substr(0, 2);

			$('#' + id).css('fill', color.from);
		}).on('mouseleave', 'tr', function () {
			var id = $(this).attr('id').substr(0, 2);

			$('#' + id).css('fill', color.to);
		});

		function hoverIn() {
			$('#' + this.id + '-list').addClass('success');
		}

		function hoverOut() {
			$('#' + this.id + '-list').removeClass('success');
		}

		$.each(countries, function (index, country) {
			map
				.find('#' + country.nameiso)
				.attr('title', country.name)
				.hover(hoverIn, hoverOut)
				.on('click', function () {
					location.href = country.path;
				});
		});
	});
});
