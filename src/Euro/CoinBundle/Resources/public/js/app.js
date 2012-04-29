$(function () {
	var a_login = $('#login_toggle'), li = a_login.parent('li');

	a_login.popover({
		content: $('#login_form').html(),
		placement: 'bottom',
		trigger: 'manual'
	}).click(function () {
		a_login.popover('toggle');
		li.toggleClass('active');

		return false;
	});
});
