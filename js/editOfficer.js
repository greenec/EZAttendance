$(document).ready(function() {

	// update account info
	$('body').on('click', 'button.saveChanges', function(event) {
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback, .text-danger, .text-success').remove();
		var formData = {
			'action': 'update',
			'officerID': qString('clubOfficerID'),
			'firstName': $('input[name=firstName]').val(),
			'lastName': $('input[name=lastName]').val(),
			'email': $('input[name=email]').val(),
			'position': $('input[name=position]').val(),
			'graduating': $('select[name=graduating]').val()
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateOfficer.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.officerFirstName) {
					$('#firstName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.officerFirstName + '</div>');
				}
				if(data.errors.officerLastName) {
					$('#lastName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.officerLastName + '</div>');
				}
				if(data.errors.officerEmail) {
					$('#email-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.officerEmail + '</div>');
				}
				if(data.errors.officerPosition) {
					$('#position-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.officerPosition + '</div>');
				}
				if(data.errors.officerGraduating) {
					$('#graduating-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.officerGraduating + '</div>');
				}
			} else {
				$('#firstName-group').find('.form-control').addClass('is-valid');
				$('#lastName-group').find('.form-control').addClass('is-valid');
				$('#email-group').find('.form-control').addClass('is-valid');
				$('#position-group').find('.form-control').addClass('is-valid');
				$('#graduating-group').find('.form-control').addClass('is-valid');

				$('input[name=email]').val(data.officerEmail);

				$('.saveChanges').before('<p class="text-success">Account successfully updated!</p>');
			}
		});
		event.preventDefault();
	});

	$('#resetPassword').click(function(e) {
		var formData = {
			'action': 'resetPassword',
			'officerID': qString('clubOfficerID')
		};

        $.post( "/handlers/updateOfficer.php", formData, function(data) {
            if(data.success) {
            	$('.saveChanges').before('<p class="text-success">Password successfully reset!</p>')
			}
        }, 'json');

        e.preventDefault();
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
