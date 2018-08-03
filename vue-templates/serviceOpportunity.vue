<div class="card" :data-service-id="opportunity.id">
    <div class="card-header collapse-target pointer">
        <b>{{ opportunity.name }}</b> (<span class="totalHours">{{ opportunityHours }}</span> Hours)
    </div>
    <div class="collapse-body" style="display: none;" :data-service-id="opportunity.id">
        <div class="card-body">
            <p><b>Contact Name:</b> {{ opportunity.contactName }}</p>
            <p><b>Contact Phone:</b> {{ opportunity.contactPhone }}</p>
            <p>
                <b>Description: </b> {{ opportunity.description }}
            </p>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="entry in opportunity.entries">
                    <td>{{ entry.date }}</td>
                    <td>{{ entry.hours }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm deleteEntry" :data-service-entry-id="entry.id">
                            <span class="fa fa-fw fa-trash"></span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <b>Total hours:</b> <span class="totalHours">{{ opportunityHours }}</span>
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
            <div class="row serviceEntryContainer" :data-service-id="opportunity.id">
                <div class='col-md-12'>
                    <h2>Add Service Entry</h2>
                    <br>
                    <form class="serviceEntryForm">
                        <div class="form-group row serviceDate-group">
                            <div class="col-sm-3">
                                <label class="control-label">Service Date:</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="serviceDate"
                                       placeholder="Service Date">
                            </div>
                        </div>
                        <div class="form-group row serviceHours-group">
                            <label class="control-label col-sm-3">Service Hours:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="serviceHours"
                                       placeholder="Service Hours">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-secondary">Add Service Entry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>