$(document).ready(function() {

    // update account info
    $('#saveChanges').click(function(e) {
        $('.form-control').removeClass('is-invalid').removeClass('is-valid');
        $('.text-danger, .text-success, .invalid-feedback').remove();

        var formData = {
            'action': 'updateOpportunity',
            'opportunityID': qString('opportunityID'),
            'serviceName': $('input[name=opportunityName]').val(),
            'serviceType': $('input[name=serviceType]').val(),
            'serviceDescription': $('textarea[name=description]').val(),
            'contactName': $('input[name=contactName]').val(),
            'contactPhone': $('input[name=contactPhone]').val()
        };

        $.ajax({
            type: 'POST',
            url: 'handlers/serviceHours.php',
            data: formData,
            dataType: 'json',
            encode: true
        })
            .done(function(data) {
                if(!data.success) {
                    if(data.errors.serviceName) {
                        $('#opportunityName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.opportunityName + '</div>');
                    }
                    if(data.errors.description) {
                        $('#description-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.description + '</div>');
                    }
                    if(data.errors.contactName) {
                        $('#contactName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.contactName + '</div>');
                    }
                    if(data.errors.contactPhone) {
                        $('#contactPhone-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.contactPhone + '</div>');
                    }
                } else {
                    $('#opportunityName-group, #description-group, #contactName-group, #contactPhone-group').find('.form-control').addClass('is-valid');
                    $('#saveChanges').before('<p class="text-success">Opportunity successfully updated!</p>');
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