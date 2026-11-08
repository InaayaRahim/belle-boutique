const chart = document.querySelector('.order-status-chart');

if (chart) {
    const orderCounts = {
        pending: Number(chart.dataset.pending),
        progress: Number(chart.dataset.progress),
        completed: Number(chart.dataset.completed)
    };

    const total = Object.values(orderCounts)
        .reduce((sum, count) => sum + count, 0);

    chart.querySelectorAll('.chart-fill').forEach(bar => {
        const status = bar.dataset.status;
        let count = 0;

        if (status === 'Pending') count = orderCounts.pending;
        if (status === 'In Progress') count = orderCounts.progress;
        if (status === 'Completed') count = orderCounts.completed;

        const percentage = total > 0
            ? (count / total) * 100
            : 0;

        bar.style.width = percentage + '%';

        const percentageLabel = bar
            .closest('.chart-item')
            .querySelector('.chart-percentage');

        percentageLabel.textContent =
            percentage.toFixed(1) + '%';
    });
}