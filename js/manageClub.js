$(document).ready(function() {

    var meetingForm = $('#createMeeting');
    var officerForm = $('#addOfficer');
    var adviserForm = $('#addAdviser');

    var meetingsTable = $('#meetings');
    var meetings = meetingsTable.DataTable({
        'order': [[1, 'dsc']],
        'columnDefs': [
            { 'targets': 1, 'type': 'date' }
        ]
    });

    var officersTable = $('#officers');
    var officers = officersTable.DataTable({
        'order': [[0, 'asc']]
    });

    var advisersTable = $('#advisers');
    var advisers = advisersTable.DataTable({
        'order': [[0, 'asc']]
    });

    $('.meetingFormToggle').click(function(e) {
        $('.meetingForm').slideToggle();
        e.preventDefault();
    });

    $('.officerFormToggle').click(function(e) {
        $('.officerForm').slideToggle();
        e.preventDefault();
    });

    $('.adviserFormToggle').click(function(e) {
        $('.adviserForm').slideToggle();
        e.preventDefault();
    });

    // create a meeting
    meetingForm.submit(function(event) {
        $('.text-success, .invalid-feedback').remove();
        $('.form-control').removeClass('is-invalid');
        var formData = {
            'meetingName': meetingForm.find('input[name=meetingName]').val(),
            'meetingDate': meetingForm.find('input[name=meetingDate]').val(),
            'clubID': qString('clubID'),
            'action': 'add'
        };
        $.ajax({
                type: 'POST',
                url: '/handlers/updateMeeting.php',
                data: formData,
                dataType: 'json',
                encode: true
            })
            .done(function(data) {
                if(!data.success) {
                    if(data.errors.meetingName) {
                        $('#meetingNameGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.meetingName + '</div>');
                    }
                    if(data.errors.meetingDate) {
                        $('#meetingDateGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.meetingDate + '</div>');
                    }
                } else {
                    $('#meetingNameGroup').find('.form-control').addClass('is-valid').parent().append('<div class="invalid-feedback">Meeting successfully created!</div>');

                    meetingForm.find('input').val('');

                    var row = meetings.row.add([
                        data.meetingName,
                        data.meetingDate,
                        0,
                        '<a class="btn btn-success btn-sm" href="/attendanceClient.php?meetingID=' + data.meetingID + '"><i class="fa fa-fw fa-play"></i></a> ' +
                        '<a class="btn btn-primary btn-sm" href="/manageMeeting.php?meetingID=' + data.meetingID + '"><i class="fa fa-fw fa-pencil"></i></a> ' +
                        '<btn class="btn btn-danger btn-sm deleteMeeting"><i class="fa fa-fw fa-trash"></i></btn>'
                    ]).draw().node();
                    $(row).attr('id', data.meetingID);
                }
            });
        event.preventDefault();
    });

    // delete a meeting
    meetingsTable.on('click', '.deleteMeeting', function(e) {
        if(confirm("Are you sure you want to delete this meeting?")) {
            $('.text-success, .text-danger').remove();
            var formData = {
                'meetingID': $(this).closest('tr').attr('id'),
                'action': 'remove'
            };
            $.ajax({
                    type: 'POST',
                    url: '/handlers/updateMeeting.php',
                    data: formData,
                    dataType: 'json',
                    encode: true
                })
                .done(function(data) {
                    if(!data.success) {
                        if(data.errors.error) {
                            $('#meetings').before('<p class="text-danger">' + data.errors.error + '</p>');
                        }
                    } else {
                        meetings.row( meetingsTable.find('[id="' + data.meetingID + '"]') ).remove().draw();
                    }
                });
            e.preventDefault();
        }
    });

    // add an officer
    officerForm.submit(function(event) {
        $('.text-success, .invalid-feedback').remove();
        $('.form-control').removeClass('is-invalid');
        var formData = {
            'clubID' : qString('clubID'),
            'firstName': officerForm.find('input[name=firstName]').val(),
            'lastName': officerForm.find('input[name=lastName]').val(),
            'email': officerForm.find('input[name=email]').val(),
            'position': officerForm.find('input[name=officerPosition]').val(),
            'graduating': officerForm.find('select[name=graduatingYear]').val(),
            'action': 'add'
        };
        $.ajax({
                type: 'POST',
                url: '/handlers/updateOfficer.php',
                data: formData,
                dataType: 'json',
                encode: true
            })
            .done(function(data) {
                if(!data.success) {
                    if(data.errors.firstName) {
                        $('#firstNameGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.firstName + '</div>');
                    }
                    if(data.errors.lastName) {
                        $('#lastNameGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.lastName + '</div>');
                    }
                    if(data.errors.email) {
                        $('#emailGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.email + '</div>');
                    }
                    if(data.errors.position) {
                        $('#positionGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.position + '</div>');
                    }
                    if(data.errors.graduating) {
                        $('#graduatingGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.graduating + '</div>');
                    }
                } else {
                    $('#emailGroup').find('.form-control').addClass('is-valid').parent().append('<div class="invalid-feedback">Officer successfully added!</div>');

                    officerForm.find('input, select').val('');

                    var row = officers.row.add([
                        data.officerFirstName + ' ' + data.officerLastName,
                        data.officerPosition,
                        data.officerEmail,
                        "<a class='btn btn-primary btn-sm' href='editOfficer.php?clubOfficerID=" + data.officerID + "'><i class='fa fa-fw fa-pencil'></i></a> " +
                        '<btn class="btn btn-danger btn-sm removeOfficer"><i class="fa fa-fw fa-trash"></i></btn>'
                    ]).draw().node();
                    $(row).attr('id', data.officerID);
                }
            });
        event.preventDefault();
    });

    // remove an officer
    officersTable.on('click', '.removeOfficer', function() {
        if(confirm("Are you sure you want to remove this officer?")) {
            $('.text-success').remove();
            $('.text-danger').remove();
            var formData = {
                'officerID': $(this).closest('tr').attr('id'),
                'action': 'remove'
            };
            $.ajax({
                    type: 'POST',
                    url: '/handlers/updateOfficer.php',
                    data: formData,
                    dataType: 'json',
                    encode: true
                })
                .done(function(data) {
                    if(!data.success) {
                        if(data.errors.error) {
                            $('#officers').before('<p class="text-danger">' + data.errors.error + '</p>');
                        }
                    } else {
                        officers.row( officersTable.find('[id="' + data.officerID + '"]') ).remove().draw();
                    }
                });
            event.preventDefault();
        }
    });

    // officer auto-complete
    officerForm.find('input[name=email]').autocomplete({
        // noCache: true,
        serviceUrl: '/handlers/autofill.php',
        type: 'POST',
        onSelect: function (suggestion) {
            var form = officerForm, data = suggestion.data;
            form.find('input[name=email]').val(data.email);
            form.find('input[name=firstName]').val(data.firstName);
            form.find('input[name=lastName]').val(data.lastName);
            form.find('select[name=graduatingYear]').val(data.graduatingYear);
        }
    });

    // add an adviser
    adviserForm.submit(function(event) {
        $('.text-success, .invalid-feedback').remove();
        $('.form-control').removeClass('is-invalid');
        var formData = {
            'clubID' : qString('clubID'),
            'firstName': adviserForm.find('input[name=firstName]').val(),
            'lastName': adviserForm.find('input[name=lastName]').val(),
            'email': adviserForm.find('input[name=email]').val(),
            'action': 'add'
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
                        $('#adviserFirstNameGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.firstName + '</div>');
                    }
                    if(data.errors.lastName) {
                        $('#adviserLastNameGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.lastName + '</div>');
                    }
                    if(data.errors.email) {
                        $('#adviserEmailGroup').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.email + '</div>');
                    }
                } else {
                    $('#adviserEmailGroup').find('.form-control').addClass('is-valid').parent().append('<div class="invalid-feedback">Adviser successfully added!</div>');

                    adviserForm.find('input').val('');

                    advisers.row.add( $(Mustache.to_html(adviserRowTpl, data))[0] ).draw().node();
                }
            });
        event.preventDefault();
    });

    // remove an adviser
    advisersTable.on('click', '.removeAdviser', function(e) {
        if(confirm("Are you sure you want to remove this adviser?")) {
            $('.text-success').remove();
            $('.text-danger').remove();
            var formData = {
                'adviserID': $(this).closest('tr').data('adviser-id'),
                'clubID': qString('clubID'),
                'action': 'remove'
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
                        if(data.errors.error) {
                            advisersTable.before('<p class="text-danger">' + data.errors.error + '</p>');
                        }
                    } else {
                        advisers.row( advisersTable.find('tr[data-adviser-id="' + data.id + '"]') ).remove().draw();
                    }
                });
            e.preventDefault();
        }
    });

    // adviser auto-complete
    adviserForm.find('input[name=email]').autocomplete({
        // noCache: true,
        serviceUrl: '/handlers/autofill.php',
        params: {
            'type': 'teacher'
        },
        type: 'POST',
        onSelect: function (suggestion) {
            var form = adviserForm, data = suggestion.data;
            form.find('input[name=email]').val(data.email);
            form.find('input[name=firstName]').val(data.firstName);
            form.find('input[name=lastName]').val(data.lastName);
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