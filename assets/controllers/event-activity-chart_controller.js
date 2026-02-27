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
        const canvas = this.element;
        const dailyActivity = JSON.parse(this.eventsValue);
        const ctx = canvas.getContext('2d');

        // Brand gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.offsetHeight || 160);
        gradient.addColorStop(0, 'rgba(102, 0, 51, 0.18)');
        gradient.addColorStop(1, 'rgba(102, 0, 51, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: Object.keys(dailyActivity),
                datasets: [{
                    data: Object.values(dailyActivity),
                    borderColor: '#660033',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: '#660033',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    tension: 0.4,
                }],
            },
            options: {
                layout: {
                    padding: { top: 8, bottom: 0, left: 0, right: 0 },
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'EEE, d MMM',
                        },
                        grid: { display: false, drawBorder: false },
                        ticks: { display: false },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { display: false, drawBorder: false },
                        ticks: { display: false },
                        border: { display: false },
                    },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        intersect: false,
                        mode: 'index',
                        position: 'nearest',
                        backgroundColor: '#1f2937',
                        titleColor: '#9ca3af',
                        bodyColor: '#f9fafb',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: (items) => items[0]?.label ?? '',
                            label: (ctx) => `${ctx.parsed.y} event${ctx.parsed.y !== 1 ? 's' : ''}`,
                        },
                    },
                },
                responsive: true,
                maintainAspectRatio: false,
            },
        });
    }
}
