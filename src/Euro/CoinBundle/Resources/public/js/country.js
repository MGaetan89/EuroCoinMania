var mapElt = document.getElementById('map'),
	countries = JSON.parse(mapElt.getAttribute('data-countries')),
	data = [];

for (var i in countries) {
	var country = countries[i];

	data.push([ country.nameiso, country.name ]);
}

google.load('visualization', '1', { packages: ['geochart'] });
google.setOnLoadCallback(function () {
	var size = parseInt(window.getComputedStyle(mapElt, null).getPropertyValue('width')) - 20,
		map = new google.visualization.GeoChart(mapElt);

	map.draw(google.visualization.arrayToDataTable(data), {
		backgroundColor: { fill: '#C4E3F3' },
		height: size * .624,
		region: '150',
		width: size
	});
});

