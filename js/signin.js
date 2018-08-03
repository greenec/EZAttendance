$(document).ready(function() {

	var form = $('#memberForm');

	/*
	var socket;
    var host = "wss://eahsnhs.com/socket/connect";
    try {
        socket = new WebSocket(host);
        socket.onopen = function(msg) {
            var data = {
                'action': 'authenticate',
                'guid': qString('guid')
            };
            socket.send(JSON.stringify(data));
        };
    } catch(ex) {
        console.log(ex);
    }
    */

    // auto-complete
    var email = form.find('input[name=email]');
    email.autocomplete({
        // noCache: true,
        serviceUrl: '/handlers/autofill.php',
        type: 'POST',
        onSelect: function (suggestion) {
            var data = suggestion.data;
            email.val(data.email);
            form.find('input[name=firstName]').val(data.firstName);
            form.find('input[name=lastName]').val(data.lastName);
            form.find('select[name=graduatingYear]').val(data.graduatingYear);
        }
    });

	form.submit(function(e) {
		$('.form-control').removeClass('is-invalid');
		$('.invalid-feedback').remove();
		var formData = {
			'email': $('input[name=email]').val(),
			'firstName': $('input[name=firstName]').val(),
			'lastName': $('input[name=lastName]').val(),
			'graduatingYear': $('select[name=graduatingYear]').val(),
			'meetingID': meetingID,
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
				if(data.errors.error) {
					alert(data.errors.error);
				}
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
			} else {
				// https://codepen.io/scottloway/pen/zqoLyQ
				$('.container').html(
					'<br />' +
					'<div class="text-center">' +
						'<h1 style="color: #5cb85c;">Thanks for signing in!</h1>' +
						'<br />' +
						'<div class="circle-loader">' +
							'<div class="checkmark draw"></div>' +
						'</div>' +
					'</div>'
				);

				setTimeout(function() {
					$('.circle-loader').toggleClass('load-complete');
					$('.checkmark').toggle();
				}, 500);
			}
		});
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
