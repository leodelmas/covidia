import Chart from 'chart.js';

var ctx = document.getElementById('st1');
var obj = jQuery.parseJSON(ctx.dataset.stat);

var st1 = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['télétravail', 'présentiel'],
        datasets: obj['data']['datasets']
    },
    options: {
        title: {
            display: true,
            text: 'Pourcentage du temps de travail par salarié en télétravail ou présentiel, sur le mois de 02/2021, par salariés'
        },
        tooltips: {
            callbacks: {
                title: function(chart, data) {
                    return data.datasets[chart[0].label];
                }
            }
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

var ctx = document.getElementById('st2');
var obj = jQuery.parseJSON(ctx.dataset.stat);

var st2 = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['télétravail', 'présentiel'],
        datasets: obj['data']['datasets']
    },
    options: {
        title: {
            display: true,
            text: 'Pourcentage du temps de travail en présentiel ou télétravail, sur le mois de 02/2021, par personnel « cadre » ou « non cadre »'
        },
        tooltips: {
            callbacks: {
                title: function(chart, data) {
                    return data.datasets[chart[0].label];
                }
            }
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

var ctx = document.getElementById('st3');
var obj = jQuery.parseJSON(ctx.dataset.stat);

var st2 = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['télétravail', 'présentiel'],
        datasets: obj['data']['datasets']
    },
    options: {
        title: {
            display: true,
            text: 'Pourcentage des périodes passées en télétravail ou en présentiel par salariés, sur le mois de 02/2021'
        },
        tooltips: {
            callbacks: {
                title: function(chart, data) {
                    return data.datasets[chart[0].label];
                }
            }
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