$(document).ready(function() {
    var meetingsTable = $('#meetings');

    // show the help modal no meeting has been created for the day
    var helpModal = $('#helpModal');
    if(helpModal.length && !todaysMeetingCreated()) {
        helpModal.modal('show');
    }

    var meetings = meetingsTable.DataTable({
        'order': [[1, 'dsc']],
	    'columnDefs': [
            { 'targets': 1, 'type': 'date' }
        ]
    });

    $('.meetingFormToggle').click(function(e) {
        $('.meetingForm').slideToggle();
        e.preventDefault();
    });

    // create a meeting
    $('#createMeeting').submit(function(event) {
        $('.text-success').remove();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        var formData = {
            'meetingName': $('input[name=meetingName]').val(),
            'meetingDate': $('input[name=meetingDate]').val(),
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
                    if(data.errors.error) {
                        alert(data.errors.error);
                    }
                } else {
                    $('#meetingNameGroup').find('.form-control').addClass('is-valid').parent().append('<div class="invalid-feedback">Meeting successfully created!</div>');

                    $('#createMeeting').find('input').val('');

                    var row = meetings.row.add([
                        data.meetingName,
                        data.meetingDate,
                        0,
                        '<a class="btn btn-success btn-sm" href="/attendanceClient.php?meetingID=' + data.meetingID + '"><i class="fa fa-fw fa-play"></i></a> ' +
                        '<a class="btn btn-primary btn-sm" href="/manageMeeting.php?meetingID=' + data.meetingID + '"><i class="fa fa-fw fa-pencil"></i></a> ' +
                        '<button class="btn btn-danger btn-sm deleteMeeting"><i class="fa fa-fw fa-trash"></i></button>'
                    ]).draw().node();
                    $(row).attr('id', data.meetingID);

                    $('.meetingForm').slideToggle();

                    var modalHelpContent = "Now that you've created a meeting, click the green play button to launch the QR code generator.";
                    modalHelpContent += "<br /><br />";
                    modalHelpContent += "When you're done, come back here and click the blue pencil to see the attendance.";
                    helpModal.find('.modal-body').html(modalHelpContent);
                    helpModal.modal('show');
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

    function qString(name) {
        url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    function todaysMeetingCreated() {
        var today = getDateStr(new Date());
        var found = false;

        meetingsTable.find('tr td:eq(1)').each(function() {
            if( $(this).text() === today ) {
                found = true;
                return false;
            }
        });

        return found;
    }

    function getDateStr(date) {
        var year = date.getFullYear();

        var month = (1 + date.getMonth()).toString();
        month = month.length > 1 ? month : '0' + month;

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        return month + '/' + day + '/' + year;
    }
});
