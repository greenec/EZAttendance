$(document).ready(function() {

    var form = $('#memberForm');

    var missingMembersTable = $('#missingMembers');

	var members = $('#members').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'targets': 4, 'type': 'date' }
		]
	});

    var missingMembers = missingMembersTable.DataTable({
        'order': [[1, 'asc']]
    });

	form.submit(function(event) {
		$('.form-control').removeClass('is-invalid');
		$('.invalid-feedback').remove();
		var formData = {
			'email': $('input[name=email]').val(),
			'firstName': $('input[name=firstName]').val(),
			'lastName': $('input[name=lastName]').val(),
			'graduatingYear': $('select[name=graduatingYear]').val(),
			'meetingID': qString('meetingID'),
			'action': 'signin'
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/signinMember.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
			.done(function(data) {
				if(!data.success) {
					if(data.errors.email) {
						$('#email-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.email + '</div>');
					}
					if(data.errors.firstName) {
						$('#firstName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.firstName + '</div>');
					}
					if(data.errors.lastName) {
						$('#lastName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.lastName + '</div>');
					}
					if(data.errors.graduatingYear) {
						$('#graduatingYear-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.graduatingYear + '</div>');
					}
					if(data.errors.error) {
						alert(data.errors.error);
					}
				} else {
					form.find('input, select').val('');

					var row = members.row.add([
						data.firstName,
						data.lastName,
						data.email,
						data.graduating,
						data.attendanceTime
						]).draw().node();
					$(row).attr('id', data.id);

					missingMembers.row( missingMembersTable.find('[id="' + data.id + '"]') ).remove().draw();
				}
			});

		event.preventDefault();
	});

    // sign in student in a 'Class' manually (if they have a library pass, for example)
    missingMembersTable.on('click', '.signinMember', function(e) {
        var row = $(this).closest('tr');

        var formData = {
            'email': row.find('td:eq(2)').text(),
            'firstName': row.find('td:eq(0)').text(),
            'lastName': row.find('td:eq(1)').text(),
            'graduatingYear': row.find('td:eq(3)').text(),
            'meetingID': qString('meetingID'),
            'action': 'signin'
        };

        $.ajax({
            type: 'POST',
            url: 'handlers/signinMember.php',
            data: formData,
            dataType: 'json',
            encode: true
        })
            .done(function(data) {
                var row = members.row.add([
                    data.firstName,
                    data.lastName,
                    data.email,
                    data.graduating,
                    data.attendanceTime
                ]).draw().node();
                $(row).attr('id', data.id);

                missingMembers.row( missingMembersTable.find('[id="' + data.id + '"]') ).remove().draw();
            });

        e.preventDefault();
    });

	// remove student if the club type is 'Class'
    missingMembersTable.on('click', '.removeMember', function(e) {
    	var row = $(this).closest('tr');
    	var name = row.find('td:eq(0)').text() + ' ' + row.find('td:eq(1)').text();
    	if(confirm('Are you sure you want to delete the student from the class, ' + name + '?')) {
            var formData = {
                'action': 'remove',
                'memberID': row.attr('id'),
                'clubID': clubID
            };
            $.ajax({
                type: 'POST',
                url: '/handlers/updateMember.php',
                data: formData,
                dataType: 'json',
                encode: true
            })
                .done(function (data) {
                    if (data.success) {
                        missingMembers.row(missingMembersTable.find('[id="' + data.id + '"]')).remove().draw();
                    } else {
                        alert(data.errors.error);
                    }
                });

            e.preventDefault();
        }
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
            form.find('select[name=graduatingYear]').val(data.graduatingYear);
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