import Chart from 'chart.js';

var ctx = document.getElementById('myChart');

var obj = jQuery.parseJSON(ctx.dataset.stat);

var myChart = new Chart(ctx, {
    type: obj['type'],
    data: {
        labels: obj['data']['labels'],
        datasets: [{
            label: obj['data']['datasets']['label'],
            data: obj['data']['datasets']['data'],
            backgroundColor: obj['data']['datasets']['backgroundColor'],
            borderColor: obj['data']['datasets']['borderColor'],
            borderWidth: obj['data']['datasets']['borderWidth']
        }]
    },
    options: {
        title: {
            display: obj['options']['title']['display'],
            text: obj['options']['title']['text']
        },
        scales: {
            yAxes: [{
                ticks: {
                    min: 0,
                    max: 100
                }
            }]
        }
    }
});