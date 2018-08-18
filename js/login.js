$(document).ready(function() {

	var $role = $('#role');
	$('input').attr('disabled', $role.val() === '');

	$role.on('change', function() {
		var role = $role.val();

		// TODO: admin email domain display
		if(role === 'officer' || role === 'admin') {
			$('#email-extension').text('@roverkids.org');
		} else if(role === 'teacher') {
			$('#email-extension').text('@eastonsd.org');
		}

        $('input').attr('disabled', role === '');
	});

	// login to the site
	$('#login-form').on('submit', function(e) {
		$('.form-control').removeClass('is-invalid');
		$('.invalid-feedback').remove();
		var formData = {
			'email': $('input[name=email]').val(),
			'password': $('input[name=password]').val(),
			'role': $role.val()
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/login.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.role) {
					$('#role-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.role + '</div>');
				}
				if(data.errors.error) {
					$('#email-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.error + '</div>');
				}
			} else {
				window.location.href = "account.php";
			}
		});
		e.preventDefault();
	});

});
