import Chart from 'chart.js';
import {French} from "flatpickr/dist/l10n/fr";

$(document).ready(function() {

    let dateTimePickers = $('input[type=datetime-local]');
    dateTimePickers.prop('enabled', true);

    dateTimePickers.flatpickr({
        allowInput: true,
        altInput: true,
        altFormat: "n/Y",
        dateFormat: "n/Y ",
        locale: French,
        enableTime: true
    });

    dateTimePickers.on('change', function() {
        location.href='?month='+dateTimePickers[0].value;
    });

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
                text: obj['titre']
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
                text: obj['titre']
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

    var ctx = document.getElementById('st5');
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
                text: obj['titre']
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

});