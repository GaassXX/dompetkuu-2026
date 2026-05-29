import Chart from 'chart.js/auto';

// Make Chart globally available immediately
window.Chart = Chart;
window.ChartLibrary = Chart;

// Try to set it on globalThis too
try {
  globalThis.Chart = Chart;
} catch (e) {
  // Fallback
}

console.log('Chart.js loaded and exposed to window');

export default Chart;

