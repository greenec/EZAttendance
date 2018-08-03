var timeFormat = 'MM/DD/YYYY HH:mm';

var lineChartDataset = [];

// convert UNIX timestamp to JS Date object
for(var i = 0; i < times.length; i++) {
    lineChartDataset[i] = {
        x: new Date(times[i] * 1000),
        y: attendees[i]
    };
}

var color = Chart.helpers.color;
var attendeesConfig = {
    type: 'line',
    data: {
        datasets: [{
            label: "Members Signed In",
            backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
            borderColor: window.chartColors.green,
            fill: true,
            data: lineChartDataset
        }]
    },
    options: {
        scales: {
            xAxes: [{
                type: "time",
                time: {
                    parser: timeFormat,
                    tooltipFormat: 'll hh:mm a'
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Time'
                }
            }],
            yAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Members Signed In'
                }
            }]
        }
    }
};

// convert object to array
var pieChartColorsArr = [];
for (var property in window.chartColors) {
    pieChartColorsArr.push(window.chartColors[property]);
}

// assign colors for each member
var pieChartColors = [];
for(var i = 0; i < pieChartData.length; i++) {
    pieChartColors[i] = pieChartColorsArr[i % pieChartColorsArr.length];
}

var pieConfig = {
    type: 'pie',
    data: {
        datasets: [{
            data: pieChartData,
            backgroundColor: pieChartColors
        }],
        labels: pieChartLabels
    },
    options: {
        responsive: true
    }
};

window.onload = function() {
    var lineCtx = document.getElementById("lineChart").getContext("2d");
    window.myLine = new Chart(lineCtx, attendeesConfig);

    var pieCtx = document.getElementById("pieChart").getContext("2d");
    window.myPie = new Chart(pieCtx, pieConfig);
};