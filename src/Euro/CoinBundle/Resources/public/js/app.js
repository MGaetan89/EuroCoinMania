$(function () {
	$('body').popover({
		html: true,
		selector: '[data-hover=popover]',
		template: '<div class="popover"><div class="close" data-dismiss="alert">&times;</div><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	}).tooltip({
		selector: '[data-hover=tooltip]'
	});
});

