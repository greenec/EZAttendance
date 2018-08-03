$(document).ready(function() {

	// update account info
	$('body').on('click', 'button.saveChanges', function(event) {
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback').remove();
		$('.text-danger').remove();
		$('.text-success').remove();
		var formData = {
			'firstName': $('input[name=firstName]').val(),
			'lastName': $('input[name=lastName]').val(),
			'oldPassword': $('input[name=oldPassword]').val(),
			'newPassword': $('input[name=newPassword]').val(),
			'newPasswordConfirm': $('input[name=newPasswordConfirm]').val()
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateAccount.php',
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
				if(data.errors.oldPassword) {
					$('#oldPassword-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.oldPassword + '</div>');
				}
				if(data.errors.newPassword) {
					$('#newPassword-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.newPassword + '</div>');
				}
				if(data.errors.newPasswordConfirm) {
					$('#newPasswordConfirm-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.newPasswordConfirm + '</div>');
				}
			} else {
				$('#firstName-group').find('.form-control').addClass('is-valid');
				$('#lastName-group').find('.form-control').addClass('is-valid');
				$('.saveChanges').before('<p class="text-success">Account successfully updated!</p>');
				
				$('input[name=oldPassword]').val('');
				$('input[name=newPassword]').val('');
				$('input[name=newPasswordConfirm]').val('');
			}
		});
		event.preventDefault();
	});

});
