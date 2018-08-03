$(document).ready(function() {

	// update meeting info
	$('body').on('click', 'button.saveChanges', function(event) {
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback, .text-danger, .text-success').remove();
		var formData = {
			'meetingID': qString('meetingID'),
			'meetingName': $('input[name=meetingName]').val(),
			'meetingDate': $('input[name=meetingDate]').val(),
			'action': 'update'
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateMeeting.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.meetingName) {
					$('#meetingName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.meetingName + '</div>');
				}
				if(data.errors.meetingDate) {
					$('#meetingDate-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.meetingDate + '</div>');
				}
			} else {
				$('input[name=meetingDate]').val(data.meetingDate);

				$('#meetingName-group').find('.form-control').addClass('is-valid')
				$('.saveChanges').before('<p class="text-success">Meeting successfully updated!</p>');
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
