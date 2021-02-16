import Chart from 'chart.js';

var ctx = document.getElementById('myChart');

var obj = jQuery.parseJSON(ctx.dataset.stat);

var myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Télétravail', 'Présentiel'],
        datasets: [{
            label: '# of Votes',
            data: [obj[0]["nbrTime"], obj[1]["nbrTime"]],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
            ],
            borderWidth: 1
        }]
    },
    options: {
        title: {
            display: true,
            text: 'Mois 02/2021'
        }
    }
});