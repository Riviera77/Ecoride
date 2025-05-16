document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('creditChart');
    if (!canvas) return;

    const data = JSON.parse(canvas.dataset.chart);
    const labels = data.map(item => item.date);
    const totals = data.map(item => item.total);

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Crédits gagnés',
                data: totals,
                fill: true,
                tension: 0.3,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});