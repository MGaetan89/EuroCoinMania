$(function () {
	$('#answer-form').on('submit', function () {
		$(this).find('[type=submit]').addClass('disabled').attr('disabled', true);
	});
});
