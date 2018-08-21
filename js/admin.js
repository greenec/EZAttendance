$(document).ready(function() {

	// manage organizations
	var organizationsContainer = $('#organizations');
	var organizations = organizationsContainer.DataTable({
		'order': [[0, 'asc']]
	});
	$('.organizationFormToggle').click(function(e) {
		$('.organizationForm').slideToggle();
		e.preventDefault();
	});

	// manage admins
	var adminsContainer = $('#admins');
	var admins = adminsContainer.DataTable({
		'order': [[0, 'asc']]
	});
	$('.adminFormToggle').click(function(e) {
		$('.adminForm').slideToggle();
		e.preventDefault();
	});

	// truncate attendanceCodes table
	$('#clearCodes').click(function(e) {
		$('.text-success').remove();

		var formData = {
			'action': 'clearCodes'
		};

		$.post( "/handlers/updateAdmin.php", formData, function(data) {
            if(data.success) {
            	$('#cleanupStatus').html('<p class="text-success"><br />Codes successfully cleared!</p>');
            	$('#numCodes').text('0');
			}
        }, 'json');
	});

    // clean sessions table
    $('#cleanSessions').click(function(e) {
        $('.text-success').remove();

        var formData = {
            'action': 'cleanSessions'
        };

        $.post( "/handlers/updateAdmin.php", formData, function(data) {
            if(data.success) {
                $('#cleanupStatus').html('<p class="text-success"><br />Sessions successfully cleaned!</p>');
                $('#numSessions').text(data.sessionCount);
            }
        }, 'json');
    });

	var organizationForm = $('#createClub');

	// create organization
	organizationForm.submit(function(e) {
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback, .text-danger, .text-success').remove();
		var formData = {
			'organizationName': organizationForm.find('input[name=organizationName]').val(),
			'action': 'add'
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
					if(data.errors.organizationName) {
						$('#organizationName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.organizationName + '</div>');
					}
				} else {
					$('#organizationName-group').find('.form-control').addClass('is-valid').parent().append('<div class="invalid-feedback">Club successfully created!</div>');

					organizationForm.find('input').val('');
					organizationForm.find('input[type=checkbox]').attr('checked', false);

					var row = organizations.row.add( $(Mustache.to_html(organizationRowTpl, data))[0] ).draw().node();
					$(row).attr('id', data.organizationID);
				}
			});
		e.preventDefault();
	});

	// delete an organization
	organizationsContainer.on('click', '.deleteClub', function() {
		if(confirm("Are you sure you want to delete this organization?")) {
			$('.text-success').remove();
			$('.text-danger').remove();
			var formData = {
				'organizationID': $(this).closest('tr').attr('id'),
				'action': 'remove'
			};
			$.ajax({
					type: 'POST',
					url: '/handlers/updateClub.php',
					data: formData,
					dataType: 'json',
					encode: true
				})
				.done(function(data) {
					if(!data.success) {
						if(data.errors.error) {
							organizationsContainer.before('<p class="text-danger">' + data.errors.error + '</p>');
						}
					} else {
						organizations.row( $('[id="' + data.organizationID + '"]') ).remove().draw();
					}
				});
			event.preventDefault();
		}
	});

	var adminForm = $('#addAdmin');

	// auto-complete admin form
	var email = adminForm.find('input[name=email]');
    email.autocomplete({
        // noCache: true,
        serviceUrl: '/handlers/autofill.php',
    		params: {
    			'type': 'admin'
    		},
        type: 'POST',
        onSelect: function (suggestion) {
            var data = suggestion.data;
            email.val(data.email);
            adminForm.find('input[name=firstName]').val(data.firstName);
            adminForm.find('input[name=lastName]').val(data.lastName);
            adminForm.find('select[name=graduatingYear]').val(data.graduatingYear);
        }
    });

	// add admin
	adminForm.submit(function(e) {
		var emailGroup = $('#email-group');
		var firstNameGroup = $('#firstName-group');
		var lastNameGroup = $('#lastName-group');
		var graduatingGroup = $('#graduating-group');

		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback, .text-danger, .text-success').remove();
		var formData = {
			'action': 'add',
			'email': emailGroup.find('input').val(),
			'firstName': firstNameGroup.find('input').val(),
			'lastName': lastNameGroup.find('input').val(),
			'graduating': graduatingGroup.find('select').val()
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateAdmin.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
			.done(function(data) {
				if(!data.success) {
					if(data.errors.email) {
						emailGroup.find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.email + '</div>');
					}
					if(data.errors.firstName) {
						firstNameGroup.find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.firstName + '</div>');
					}
					if(data.errors.lastName) {
						lastNameGroup.find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.lastName + '</div>');
					}
					if(data.errors.graduating) {
						graduatingGroup.find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.graduating + '</div>');
					}
				} else {
					adminForm.find('input, select').val('');
					emailGroup.find('.form-control').addClass('is-valid').parent().append('<div class="invalid-feedback">Admin successfully added!</div>');

					admins.row.add( $(Mustache.to_html(adminRowTpl, data))[0] ).draw().node();
				}
			});
		e.preventDefault();
	});

	// remove an admin
	adminsContainer.on('click', '.removeAdmin', function(e) {
		if(confirm("Are you sure you want to remove this admin?")) {
			$('.text-success').remove();
			$('.text-danger').remove();
			var formData = {
				'action': 'remove',
				'memberID': $(this).closest('tr').attr('id')
			};
			$.ajax({
				type: 'POST',
				url: '/handlers/updateAdmin.php',
				data: formData,
				dataType: 'json',
				encode: true
			})
				.done(function(data) {
					if(!data.success) {
						if(data.errors.error) {
							adminsContainer.before('<p class="text-danger">' + data.errors.error + '</p>');
						}
					} else {
						admins.row( $('[id="' + data.id + '"]') ).remove().draw();
					}
				});
			e.preventDefault();
		}
	});
});