$(document).ready(function() {

    // update account info
    $('body').on('click', 'button.saveChanges', function(event) {
        $('.form-control').removeClass('is-invalid').removeClass('is-valid');
        $('.invalid-feedback, .text-danger, .text-success').remove();
        var formData = {
            'adviserID': qString('adviserID'),
            'firstName': $('input[name=firstName]').val(),
            'lastName': $('input[name=lastName]').val(),
            'email': $('input[name=email]').val(),
            'action': 'update'
        };
        $.ajax({
            type: 'POST',
            url: '/handlers/updateAdviser.php',
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
                } else {
                    $('#firstName-group').find('.form-control').addClass('is-valid')
                    $('#lastName-group').find('.form-control').addClass('is-valid')
                    $('#email-group').find('.form-control').addClass('is-valid')
                    $('#graduating-group').find('.form-control').addClass('is-valid')

                    $('.saveChanges').before('<p class="text-success">Account successfully updated!</p>');
                }
            });
        event.preventDefault();
    });

    $('#resetPassword').click(function(e) {
        var formData = {
            'action': 'resetPassword',
            'adviserID': qString('adviserID')
        };

        $.post( "/handlers/updateAdviser.php", formData, function(data) {
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
