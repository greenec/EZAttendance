<div class="tab-content" id="serviceOpportunityContainer">

    <div id="individualHours" class="tab-pane fade show active">
        <h3>(Total of {{ individualHours }} individual hours)</h3>
        <br/>
        <div class="card-panel-group opportunityContainer">
            <service-opportunity v-for="opportunity in individual" :opportunity="opportunity"> </service-opportunity>
            <h3 class='text-center' v-if="individual.length === 0">No Service Hours Yet</h3>
        </div>
    </div>

    <div id="groupHours" class="tab-pane fade">
        <h3>(Total of {{ groupHours }} group hours)</h3>
        <br/>
        <div class="card-panel-group opportunityContainer">
            <service-opportunity v-for="opportunity in group" :opportunity="opportunity"> </service-opportunity>
            <h3 class='text-center' v-if="group.length === 0">No Service Hours Yet</h3>
        </div>
    </div>

</div>