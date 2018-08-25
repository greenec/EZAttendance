$(document).ready(function() {

    var form = $('#memberForm');

	var graduatingYears = graduatingYearData;
    var tables = [];

	for(var year in graduatingYears) {
		if(graduatingYears.hasOwnProperty(year)) {
            tables[year] = $('#' + graduatingYears[year]).DataTable({
                'order': [[1, 'asc']],
                'lengthMenu': [[8, 25, 50, 100], [8, 25, 50, 100]],
                'stateSave': true
            });
        }
	}

	$('.toggleMemberForm').click(function(e) {
		$('#memberForm').slideToggle();
		e.preventDefault();
	});

	form.submit(function(event) {
		$('.form-control').removeClass('is-invalid').removeClass('is-valid');
		$('.invalid-feedback').remove();
		$('.text-danger').remove();
		$('.text-success').remove();
		var formData = {
			'firstName': form.find('input[name=firstName]').val(),
			'lastName': form.find('input[name=lastName]').val(),
			'email': form.find('input[name=email]').val(),
			'graduating': form.find('select[name=graduating]').val(),
			'clubID': qString('clubID'),
			'action': 'add'
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
				form.find('input, select').val('');

                $('#' + graduatingYears[data.graduating] + 'Container').show();
                tables[data.graduating].row.add( $(Mustache.to_html(memberRowTpl, data))[0] ).draw();

                $('.saveChanges').before('<p class="text-success">Member successfully added!</p>');
			}
		});
		event.preventDefault();
	});

    // auto-complete
    var email = form.find('input[name=email]');
    email.autocomplete({
        // noCache: true,
        serviceUrl: '/handlers/autofill.php',
        type: 'POST',
        params: {
            'organizationID': organizationID
        },
        onSelect: function (suggestion) {
            var data = suggestion.data;
            email.val(data.email);
            form.find('input[name=firstName]').val(data.firstName);
            form.find('input[name=lastName]').val(data.lastName);
            form.find('select[name=graduating]').val(data.graduatingYear);
        }
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
