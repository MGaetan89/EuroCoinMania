$(function () {
	$('body').popover({
		selector: '[data-hover=popover]',
		template: '<div class="popover"><div class="close" data-dismiss="alert">&times;</div><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>'
	}).tooltip({
		selector: '[data-hover=tooltip]'
	});

	$('#stats-carousel').carousel();
});
