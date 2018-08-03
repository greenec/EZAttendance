$(document).ready(function ($) {
    var serviceOpportunityContainer = $('#serviceOpportunityContainer');
	var individualContainer = $('#individualHours').find('.opportunityContainer');
	var groupContainer = $('#groupHours').find('.opportunityContainer');

	var opportunityContainer;

    var serviceOpportunityFormContainer = $('#serviceOpportunityFormContainer');
    var serviceOpportunityForm = $('#serviceOpportunityForm');

    var requestInProgress = false;

    // slide toggle opportunity form
    $('.serviceOpportunityToggle').click(function (e) {
        serviceOpportunityFormContainer.slideToggle();
        e.preventDefault();
    });

    // open the first accordion card
    individualContainer.find('.collapse-body:first').show();
    groupContainer.find('.collapse-body:first').show();

    // accordion toggle
    individualContainer.on('click', '.collapse-target', function() {
        individualContainer.find('.collapse-body').slideUp();
        $(this).parent().find('.collapse-body').slideDown();
    });
    groupContainer.on('click', '.collapse-target', function() {
        groupContainer.find('.collapse-body').slideUp();
        $(this).parent().find('.collapse-body').slideDown();
    });

    // add an opportunity
    serviceOpportunityForm.submit(function (e) {
        var form = serviceOpportunityForm;
        form.find('.form-group').removeClass('is-invalid').removeClass('is-valid');
        form.find('.invalid-feedback, .text-danger, .text-success').remove();
        var formData = {
            'action': 'addOpportunity',
            'serviceName': form.find('input[name=serviceName]').val(),
            'serviceType': form.find('select[name=serviceType]').val(),
            'serviceDescription': form.find('textarea[name=serviceDescription]').val(),
            'contactName': form.find('input[name=contactName]').val(),
            'contactPhone': form.find('input[name=contactPhone]').val()
        };

        $.ajax({
            type: 'POST',
            url: '/handlers/serviceHours.php',
            data: formData,
            dataType: 'json',
            encode: true
        }).done(function (data) {
            if (!data.success) {
                if (data.errors.serviceName) {
                    $('#serviceName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.serviceName + '</div>');
                }
                if (data.errors.serviceType) {
                    $('#serviceType-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.serviceType + '</div>');
                }
                if (data.errors.contactName) {
                    $('#contactName-group').find('.form-control').addClass('is-invalid').parent().append('<div class="invalid-feedback">' + data.errors.contactName + '</div>');
                }
            } else {
                form.find('input, select, textarea').val('');

                if(data.serviceType === "group") {
                    opportunityContainer = groupContainer;
                	$('a[href="#groupHours"]').tab('show');
                } else {
                	opportunityContainer = individualContainer;
                	$('a[href="#individualHours"]').tab('show');
                }

                // if the service opportunity does not exist, add it to the view
                if(app[data.serviceType].find(function(x) { return x.id === data.id; }) === undefined) {
                    app[data.serviceType].push(data);
                }

                serviceOpportunityFormContainer.slideToggle();
                opportunityContainer.find('.collapse-body').slideUp();

                Vue.nextTick(function() {
                    opportunityContainer.find('.collapse-body[data-service-id=' + data.id + ']').slideDown();
                });
            }
        });

        e.preventDefault();
    });

    // service hour entry
    serviceOpportunityContainer.on('submit', '.serviceEntryForm', function (e) {
        if(requestInProgress) {
            return;
        }

        requestInProgress = true;

        var form = $(this);
        form.find('.form-group').removeClass('is-invalid').removeClass('is-valid');
        form.find('.invalid-feedback, .text-danger, .text-success').remove();
        var formData = {
            'action': 'addEntry',
            'serviceID': form.closest('.serviceEntryContainer').data('service-id'),
            'memberID': qString('memberID'),
            'clubID': qString('clubID'),
            'serviceDate': form.find('input[name=serviceDate]').val(),
            'serviceHours': form.find('input[name=serviceHours]').val()
        };

        $.ajax({
            type: 'POST',
            url: '/handlers/serviceHours.php',
            data: formData,
            dataType: 'json',
            encode: true
        }).done(function (data) {
            requestInProgress = false;

            if (!data.success) {
                if (data.errors.error) {
                    alert(data.errors.error);
                }
                if (data.errors.serviceDate) {
                    form.find('.serviceDate-group').addClass('is-invalid');
                    form.find('.serviceDate-group').append('<div class="invalid-feedback">' + data.errors.serviceDate + '</div>');
                }
                if (data.errors.serviceHours) {
                    form.find('.serviceHours-group').addClass('is-invalid');
                    form.find('.serviceHours-group').append('<div class="invalid-feedback">' + data.errors.serviceHours + '</div>');
                }
            } else {
                var serviceOpportunity = serviceOpportunityContainer.find('.card[data-service-id=' + data.serviceID + ']');
                serviceOpportunity.find('input, select, textarea').val('');

                var opportunity = app[data.serviceType].find(function(x) { return x.id === parseInt(data.serviceID) });
                opportunity.entries.push({ 'date': data.serviceDate, 'hours': data.serviceHours, 'id': data.serviceEntryID });
            }
        });

        e.preventDefault();
    });

    // delete service entry
    $('#app').on('click', '.deleteEntry', function(e) {

        var entryID = $(this).data('service-entry-id');

        var formData = {
            action: 'removeEntry',
            serviceEntryID: entryID
        };

        $.post('/handlers/serviceHours.php', formData, function(data) {
            if(data.success) {
                // remove from individual hours
                for(var i = 0; i < app.individual.length; i++) {
                    var opportunity = app.individual[i];
                    for(var j = 0; j < opportunity.entries.length; j++) {
                        if (opportunity.entries[j].id === entryID) {
                            opportunity.entries.splice(j, 1);
                        }
                    }
                }

                // remove from group hours
                for(i = 0; i < app.group.length; i++) {
                    opportunity = app.group[i];
                    for(j = 0; j < opportunity.entries.length; j++) {
                        if (opportunity.entries[j].id === entryID) {
                            opportunity.entries.splice(j, 1);
                        }
                    }
                }
            }
        }, 'json');

        e.preventDefault();
    });

    // auto-complete
    serviceOpportunityForm.find('input[name=serviceName]').autocomplete({
        noCache: true,
        serviceUrl: '/handlers/serviceHours.php',
        type: 'POST',
        onSelect: function (suggestion) {
            var form = serviceOpportunityForm, data = suggestion.data;
            form.find('input[name=serviceName]').val(data.serviceName);
            form.find('select[name=serviceType]').val(data.serviceType);
            form.find('textarea[name=serviceDescription]').val(data.description);
            form.find('input[name=contactName]').val(data.contactName);
            form.find('input[name=contactPhone]').val(data.contactPhone);
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
