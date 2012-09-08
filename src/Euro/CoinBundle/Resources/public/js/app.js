$(function () {
	var footer_indicator = $('#footer-toggle').find('i'), footer_short = $('#footer-short');

	$('body').tooltip({
		selector: '[rel=tooltip]'
	});

	$('#footer-extend').on('show hide', function () {
		footer_indicator.toggleClass('icon-minus icon-plus');
		footer_short.toggle();
	});
});
