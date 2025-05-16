document.addEventListener('DOMContentLoaded', function () {
    const chartCanvas = document.getElementById('carpoolingChart');
    if (!chartCanvas) return;

    const chartData = JSON.parse(chartCanvas.dataset.chart);
    const labels = chartData.map(item => item.date);
    const data = chartData.map(item => item.count);

    const ctx = chartCanvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Covoiturages par jour',
                data: data,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
});