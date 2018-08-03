$(document).ready(function() {

	var meetings = $('#missedMeetings').DataTable({
		'order': [[1, 'asc']],
        'columnDefs': [
            { 'targets': 1, 'type': 'date' }
        ]
	});

	// update account info
	$('body').on('click', 'button.saveChanges', function(event) {
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback, .text-danger, .text-success').remove();
		var formData = {
			'memberID': qString('memberID'),
			'firstName': $('input[name=firstName]').val(),
			'lastName': $('input[name=lastName]').val(),
			'email': $('input[name=email]').val(),
			'graduating': $('select[name=graduating]').val(),
			'action': 'update'
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateMember.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.firstName) {
					$('#firstName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.firstName + '</div>');
				}
				if(data.errors.lastName) {
					$('#lastName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.lastName + '</div>');
				}
				if(data.errors.email) {
					$('#email-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.email + '</div>');
				}
				if(data.errors.graduating) {
					$('#graduating-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.graduating + '</div>');
				}
			} else {
				$('#firstName-group').find('.form-control').addClass('is-valid');
				$('#lastName-group').find('.form-control').addClass('is-valid');
				$('#email-group').find('.form-control').addClass('is-valid');
				$('#graduating-group').find('.form-control').addClass('is-valid');

				$('.saveChanges').before('<p class="text-success">Account successfully updated!</p>');
			}
		});
		event.preventDefault();
	});

	function qString(name) {
		url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}
});
