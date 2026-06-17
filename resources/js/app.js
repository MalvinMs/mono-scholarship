import { Chart, BarController, LineController, DoughnutController,
         BarElement, LineElement, PointElement, ArcElement,
         LinearScale, CategoryScale, Tooltip, Legend, Filler } from 'chart.js';

Chart.register(
    BarController, LineController, DoughnutController,
    BarElement, LineElement, PointElement, ArcElement,
    LinearScale, CategoryScale,
    Tooltip, Legend, Filler
);

// Design token defaults (DESIGN.md — Inter geometric sans + muted palette)
Chart.defaults.font.family = '"Inter", ui-sans-serif, system-ui, sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.color = '#4d4d4d';
Chart.defaults.borderColor = '#ebebeb';
Chart.defaults.plugins.tooltip.backgroundColor = '#171717';
Chart.defaults.plugins.tooltip.titleFont = { weight: 600, size: 13 };
Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 4;

document.addEventListener('alpine:init', () => {
    Alpine.data('chart', () => ({
        instance: null,

        init(chartId, config) {
            const ctx = document.getElementById(chartId);
            if (!ctx) return;

            const chartConfig = typeof config === 'string' ? JSON.parse(config) : config;

            this.instance = new Chart(ctx, chartConfig);
        },

        destroy() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        }
    }));
});
