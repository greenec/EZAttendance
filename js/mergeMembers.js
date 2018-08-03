$(document).ready(function() {

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

    var sourceMember, targetMember;
    $('.table').on('click', '.mergeMember', function() {
        if(sourceMember == null) {
            sourceMember = {};

            var target = $(this);
            var table = target.closest('table');

            sourceMember.id = target.closest('tr').attr('id');
            sourceMember.name = target.closest('tr').find('td:eq(0)').text() + ' ';
            sourceMember.name += target.closest('tr').find('td:eq(1)').text() + ' ';
            sourceMember.name += '(' + target.closest('tr').find('td:eq(2)').text() + ')';
            sourceMember.meetings = parseInt(target.closest('tr').find('td:eq(3)').text());

            toggleMerge('start');

            target = table.find('tr[id=' + sourceMember.id + '] .mergeMember');

            target.find('span').toggleClass('fa-download fa-ban');
            target.toggleClass('btn-success btn-danger');
        } else {
            targetMember = {};

            target = $(this);
            table = target.closest('table');

            targetMember.id = target.closest('tr').attr('id');
            targetMember.name = target.closest('tr').find('td:eq(0)').text() + ' ';
            targetMember.name += target.closest('tr').find('td:eq(1)').text() + ' ';
            targetMember.name += '(' + target.closest('tr').find('td:eq(2)').text() + ')';
            targetMember.meetings = parseInt(target.closest('tr').find('td:eq(3)').text());

            toggleMerge('stop');

            if(sourceMember.id !== targetMember.id) {
                if(confirm('Are you sure you want to merge member ' + sourceMember.id + ' ' + sourceMember.name + ' into member ' + targetMember.id + ' ' + targetMember.name + '?')) {
                    var formData = {
                        'sourceID': sourceMember.id,
                        'targetID': targetMember.id
                    };
                    $.ajax({
                            type: 'POST',
                            url: 'handlers/mergeMembers.php',
                            data: formData,
                            dataType: 'json',
                            encode: true
                        })
                        .done(function(data) {
                            if(!data.success) {
                                if(data.errors.error) {
                                    alert(data.errors.error);
                                }
                            } else {
                                $('tr[id=' + targetMember.id + '] td:eq(3)').text(sourceMember.meetings + targetMember.meetings);

                                for(var year in graduatingYears) {
                                    if(graduatingYears.hasOwnProperty(year)) {
                                        tables[year].row('#' + sourceMember.id).remove().draw('full-hold');
                                    }
                                }

                                sourceMember = targetMember = null;
                                alert('Members have been merged.');
                            }
                        });
                } else {
                    alert('No changes have been made');
                    sourceMember = targetMember = null;
                }
            } else {
                sourceMember = targetMember = null;
            }
        }
    });

    function toggleMerge(stage) {
        for(var year in graduatingYears) {
            if(graduatingYears.hasOwnProperty(year)) {
                tables[year].rows().every(function () {
                    var d = tables[year].row(this).data();
                    var actions = $('<div>' + d[4] + '</div>');
                    if (stage === 'start') {
                        actions.find('.mergeMember span').toggleClass('fa-link fa-download');
                        actions.find('.mergeMember').toggleClass('btn-primary btn-success');
                    } else {
                        actions.find('.mergeMember span').removeClass('fa-download fa-ban').addClass('fa-link');
                        actions.find('.mergeMember').removeClass('btn-success btn-danger').addClass('btn-primary');
                    }
                    d[4] = actions.html();
                    tables[year].row(this).data(d);
                });
            }
        }
    }

});
