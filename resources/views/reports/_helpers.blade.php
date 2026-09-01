<script>
    function money(v, dec = 2) {
        return 'S/ ' + Number(v || 0).toLocaleString('es-PE', { minimumFractionDigits: dec, maximumFractionDigits: dec });
    }
    function pct(v) { return Number(v || 0).toFixed(1) + '%'; }

    function renderKpis(kpis) {
        const el = document.getElementById('report-kpis');
        if (!el) return;
        el.innerHTML = (kpis || []).map(k => `
            <div class="card p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">${k.label}</p>
                <p class="mt-1 text-xl font-bold text-gray-900 truncate" title="${k.value}">${k.value}</p>
                <p class="mt-0.5 text-xs text-gray-500 truncate">${k.sub || ''}</p>
            </div>`).join('');
    }

    function baseBar() {
        return {
            chart: { type: 'bar', fontFamily: 'inherit', toolbar: { show: false }, animations: { enabled: false } },
            colors: ['#2563eb'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#f1f5f9' },
            xaxis: { labels: { style: { colors: '#6b7280', fontSize: '11px' } }, axisBorder: { show: false } },
            yaxis: { labels: { style: { colors: '#6b7280', fontSize: '11px' } } },
            legend: { show: false },
            tooltip: { theme: 'light' },
        };
    }

    function baseDonut() {
        return {
            chart: { type: 'donut', fontFamily: 'inherit', animations: { enabled: false } },
            labels: [],
            colors: ['#2563eb', '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'],
            legend: { position: 'bottom', labels: { colors: '#6b7280' } },
            dataLabels: { enabled: false },
            stroke: { colors: ['#fff'] },
            tooltip: { theme: 'light', y: { formatter: (v) => Number(v || 0).toLocaleString('es-PE') } },
        };
    }

    function baseLine() {
        return {
            chart: { type: 'area', fontFamily: 'inherit', toolbar: { show: false }, animations: { enabled: false } },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.02 } },
            colors: ['#2563eb', '#10b981', '#ef4444'],
            dataLabels: { enabled: false },
            grid: { borderColor: '#f1f5f9' },
            xaxis: { labels: { style: { colors: '#6b7280', fontSize: '11px' } } },
            yaxis: { labels: { style: { colors: '#6b7280', fontSize: '11px' } } },
            legend: { position: 'bottom', labels: { colors: '#6b7280' } },
            tooltip: { theme: 'light' },
        };
    }

    function renderChart(id, options) {
        const el = document.getElementById(id);
        if (!el) return;
        el.innerHTML = '';
        new ApexCharts(el, options).render();
    }

    function buildQuery() {
        const params = new URLSearchParams();
        ['from', 'to', 'establishment_id', 'service_type', 'state', 'type', 'brand_id'].forEach(k => {
            const el = document.querySelector(`[name="${k}"]`);
            if (el && el.value) params.set(k, el.value);
        });
        return params.toString();
    }
</script>