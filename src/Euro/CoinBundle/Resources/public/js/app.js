$(function () {
	var footer_indicator = $('#footer-toggle').find('i'), footer_short = $('#footer-short');

	$('body').popover({
		selector: '[rel=popover]',
		template: '<div class="popover"><div class="close" data-dismiss="alert">&times;</div><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
	}).tooltip({
		selector: '[rel=tooltip]'
	});

	$('#footer-extend').on('show hide', function () {
		footer_indicator.toggleClass('icon-minus icon-plus');
		footer_short.toggle();
	});
});

