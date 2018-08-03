$(document).ready(function() {
    $('#opportunitiesTable').DataTable({
        'order': [[1, 'desc']],
        'lengthMenu': [[8, 25, 50, 100], [8, 25, 50, 100]],
        'stateSave': true
    });
});

