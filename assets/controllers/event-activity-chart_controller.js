import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
    static values = {
        events: String,
    }

    connect() {
        this.loadChart();

    }

    loadChart() {
        const chartCanvas = this.element;
        const dailyActivity = JSON.parse(this.eventsValue);

        const ctx = chartCanvas.getContext('2d');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(dailyActivity),
                datasets: [{
                    data: Object.values(dailyActivity),
                    borderColor: '#00695c',
                    borderWidth: 5,
                    backgroundColor: 'transparent',
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 0,
                }],
            },
            options: {
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 10,
                        bottom: 0,
                    },
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'EEE, d MMM',
                        },
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            display: false,
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        enabled: true,
                        intersect: false,
                        mode: 'index',
                        position: 'nearest',
                        callbacks: {
                            label: (context) => {
                                return context.dataset.label
                            },
                        },
                    },
                },
                cubicInterpolationMode: 'monotone',
                responsive: true,
                maintainAspectRatio: false,
            },
        });
    }
}