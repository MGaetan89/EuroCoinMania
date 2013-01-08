var mapElt = document.getElementById('map'),
countries = JSON.parse(mapElt.getAttribute('data-countries')),
data = [],
germany = undefined;

for (var i in countries) {
	var country = countries[i];

	if (country.nameiso == 'de') {
		germany = country;

		continue;
	}

	data.push([ country.nameiso, country.name ]);
}

if (germany != undefined) {
	data.push([ germany.nameiso, germany.name ]);
}

google.load('visualization', '1', {
	packages: ['geochart']
});
google.setOnLoadCallback(function () {
	var size = parseInt(window.getComputedStyle(mapElt, null).getPropertyValue('width')) + 200,
	map = new google.visualization.GeoChart(mapElt);

	map.draw(google.visualization.arrayToDataTable(data), {
		backgroundColor: {
			fill: '#C4E3F3'
		},
		height: size * .624,
		region: '150',
		tooltip: {
			trigger: 'none'
		},
		width: size
	});
});
