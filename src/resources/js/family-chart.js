import Chart from 'chart.js/auto';

// Store chart data globally to access from Alpine
window.chartInstances = window.chartInstances || {};

// Alpine.js component for chart initialization
window.initializeFamilyChart = function() {
  return {
    chartInstance: null,

    async init() {
      console.log('Alpine init called for chart');

      // Wait a bit for Chart to be available
      for (let i = 0; i < 10; i++) {
        if (window.Chart || window.ChartLibrary || Chart) {
          break;
        }
        await new Promise(r => setTimeout(r, 100));
      }

      this.renderChart();

      // Re-render on Livewire updates
      document.addEventListener('livewire:updated', () => {
        this.renderChart();
      });
    },

    async renderChart() {
      try {
        console.log('renderChart called');

        const ChartLib = window.Chart || window.ChartLibrary || Chart;
        if (!ChartLib) {
          console.error('Chart library not available');
          return;
        }

        console.log('Chart library available:', typeof ChartLib);

        const canvas = document.getElementById('familyFinanceChart');
        if (!canvas) {
          console.warn('Canvas not found');
          return;
        }

        // Get the parent container dimensions
        const parent = canvas.parentElement;
        const rect = parent.getBoundingClientRect();
        const dpi = window.devicePixelRatio || 1;

        // Set canvas resolution
        canvas.width = rect.width * dpi;
        canvas.height = rect.height * dpi;

        // Scale the canvas back down using CSS
        canvas.style.width = rect.width + 'px';
        canvas.style.height = rect.height + 'px';

        const ctx = canvas.getContext('2d');
        ctx.scale(dpi, dpi);

        console.log(`Canvas resized to ${rect.width}x${rect.height}, DPI: ${dpi}`);

        // Destroy existing chart
        if (this.chartInstance) {
          this.chartInstance.destroy();
        }

        // Get data from window (set by blade template in data attribute or similar)
        const data = window.familyChartData || {
          labels: [],
          datasets: []
        };

        console.log('Creating chart with data:', data);

        // Create new chart
        this.chartInstance = new ChartLib(canvas, {
          type: 'bar',
          data: data,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            animation: {
              duration: 750,
              easing: 'easeInOutQuart'
            },
            plugins: {
              legend: {
                display: true,
                position: 'top',
                labels: {
                  usePointStyle: true,
                  padding: 15,
                  font: { size: 12, weight: '500' },
                  color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#374151'
                }
              },
              tooltip: {
                backgroundColor: document.documentElement.classList.contains('dark') ? 'rgba(31, 41, 55, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                titleColor: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#111827',
                bodyColor: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#4b5563',
                borderColor: document.documentElement.classList.contains('dark') ? 'rgba(107, 114, 128, 0.3)' : 'rgba(209, 213, 219, 0.5)',
                borderWidth: 1,
                padding: 12,
                displayColors: true,
                callbacks: {
                  label: function(context) {
                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(context.parsed.y));
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  callback: function(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                      notation: 'compact',
                      compactDisplay: 'short'
                    }).format(value);
                  },
                  color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
                  font: { size: 11 }
                },
                grid: {
                  color: document.documentElement.classList.contains('dark') ? 'rgba(107, 114, 128, 0.1)' : 'rgba(209, 213, 219, 0.5)',
                  drawBorder: false
                }
              },
              x: {
                grid: { display: false, drawBorder: false },
                ticks: {
                  color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
                  font: { size: 11 }
                }
              }
            }
          }
        });

        console.log('Chart rendered successfully');

      } catch (error) {
        console.error('Error rendering chart:', error);
      }
    }
  };
};
