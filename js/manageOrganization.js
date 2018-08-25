$(document).ready(function() {

    // manage clubs
    var clubsContainer = $('#clubs');
    var clubs = clubsContainer.DataTable({
        'order': [[0, 'asc']]
    });
    $('.clubFormToggle').click(function(e) {
        $('.clubForm').slideToggle();
        e.preventDefault();
    });

    var clubForm = $('#createClub');

    // create club
    clubForm.submit(function(e) {
        $('.form-control').removeClass('is-invalid').removeClass('is-valid');
        $('.invalid-feedback, .text-danger, .text-success').remove();
        var formData = {
            'clubName': clubForm.find('input[name=clubName]').val(),
            'abbreviation': clubForm.find('input[name=abbreviation]').val(),
            'clubType': clubForm.find('#clubType').val(),
            'trackService': clubForm.find('input[name=trackService]').is(':checked'),
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
                    if(data.errors.clubName) {
                        $('#clubName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.clubName + '</div>');
                    }
                    if(data.errors.abbreviation) {
                        $('#abbreviation-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.abbreviation + '</div>')
                    }
                    if(data.errors.clubType) {
                        $('#clubType-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.clubType + '</div>')
                    }
                } else {
                    $('#clubName-group').find('.form-control').addClass('is-valid').parent().append('<div class="invalid-feedback">Club successfully created!</div>');

                    clubForm.find('input').val('');
                    clubForm.find('input[type=checkbox]').attr('checked', false);

                    var row = clubs.row.add([
                        data.clubName,
                        data.abbreviation,
                        data.trackService,
                        0,
                        '<a class="btn btn-primary btn-sm" href="/manageClub.php?clubID=' + data.clubID + '"><i class="fa fa-fw fa-pencil"></i></a> ' +
                        '<btn class="btn btn-danger btn-sm deleteClub"><i class="fa fa-fw fa-trash"></i></btn>'
                    ]).draw().node();
                    $(row).attr('id', data.clubID);
                }
            });
        e.preventDefault();
    });

    // delete a club
    clubsContainer.on('click', '.deleteClub', function() {
        if(confirm("Are you sure you want to delete this club?")) {
            $('.text-success').remove();
            $('.text-danger').remove();
            var formData = {
                'clubID': $(this).closest('tr').attr('id'),
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
                            clubsContainer.before('<p class="text-danger">' + data.errors.error + '</p>');
                        }
                    } else {
                        clubs.row( $('[id="' + data.clubID + '"]') ).remove().draw();
                    }
                });
            event.preventDefault();
        }
    });
});