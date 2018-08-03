$(document).ready(function() {

	// update club info
	$('body').on('click', 'button.saveChanges', function(event) {
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback, .text-danger, .text-success').remove();
		var formData = {
			'clubID': qString('clubID'),
			'clubName': $('input[name=clubName]').val(),
            'abbreviation': $('input[name=abbreviation]').val(),
			'organizationType': $('#organizationType').val(),
			'trackService': $('input[name=trackService]').is(':checked'),
			'action': 'update'
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateClub.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.clubName) {
					$('#clubName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.clubName + '</div>');
				}
                if(data.errors.abbreviation) {
                    $('#abbreviation-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.abbreviation + '</div>')
                }
                if(data.errors.organizationType) {
                    $('#organizationType-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.organizationType + '</div>')
                }
			} else {
				$('#clubName-group').find('.form-control').addClass('is-valid')
				$('.saveChanges').before('<p class="text-success">Club successfully updated!</p>');
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
