import ApexCharts from 'apexcharts';

document.addEventListener('alpine:init', () => {
    Alpine.data('apexchart', () => ({
        instance: null,

        renderChart(chartId, config, chartHeight) {
            const container = document.getElementById(chartId);
            if (!container) return;

            const chartConfig = typeof config === 'string' ? JSON.parse(config) : config;

            // Dynamic dark mode integration
            const isDark = document.documentElement.classList.contains('dark');
            
            const defaultOptions = {
                chart: {
                    height: chartHeight || '100%',
                    width: '100%',
                    parentHeightOffset: 0,
                    fontFamily: '"Inter", ui-sans-serif, system-ui, sans-serif',
                    background: 'transparent',
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                    }
                },
                theme: {
                    mode: isDark ? 'dark' : 'light'
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: '"Inter", ui-sans-serif, system-ui, sans-serif'
                    }
                },
                grid: {
                    borderColor: isDark ? '#333333' : '#ebebeb',
                    strokeDashArray: 4,
                }
            };

            const finalOptions = { ...defaultOptions, ...chartConfig };
            // Deep merge essential objects
            if (chartConfig.chart) {
                finalOptions.chart = { ...defaultOptions.chart, ...chartConfig.chart };
            }

            this.instance = new ApexCharts(container, finalOptions);
            this.instance.render();

            // Observe dark mode class changes
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        const newIsDark = document.documentElement.classList.contains('dark');
                        this.instance.updateOptions({
                            theme: { mode: newIsDark ? 'dark' : 'light' },
                            tooltip: { theme: newIsDark ? 'dark' : 'light' },
                            grid: { borderColor: newIsDark ? '#333333' : '#ebebeb' }
                        });
                    }
                });
            });

            observer.observe(document.documentElement, { attributes: true });
        },

        destroy() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        }
    }));
});
