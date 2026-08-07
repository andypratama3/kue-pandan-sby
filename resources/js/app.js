import '../css/app.css'
import './plugins/chartjs.min.js';
import './plugins/Chart.extension.js';
import './plugins/perfect-scrollbar.min.js';

window.escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
}[char]));
