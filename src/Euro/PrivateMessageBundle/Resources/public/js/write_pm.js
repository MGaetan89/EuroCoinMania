$(function() {
	var to_user = $('#euro_privatemessagebundle_conversationtype_to_user'), users = [];

	to_user.typeahead({
		minLength: 2,
		source: function (query, process) {
			$.post('/users/query', {
				query: query
			}, function (data) {
				users = data;

				data = [];
				for (var i in users) {
					data.push(users[i]);
				}

				process(data);
			});
		}
	});

	$('#submit').on('click', function (e) {
		var id = null, user = to_user.val();
		for (var i in users) {
			if (users[i] === user) {
				id = i;

				break;
			}
		}

		if (id === null) {
			e.preventDefault();
			return false;
		}

		to_user.val(id);

		return true;
	});
});
