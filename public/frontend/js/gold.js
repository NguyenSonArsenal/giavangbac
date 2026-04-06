/**
 * gold.js – Giá Vàng Thương Hiệu (BTMC + BTMH)
 * Dùng chung shared chart, giống pattern của silver.js
 */
document.addEventListener('DOMContentLoaded', function () {

    // ─── State ────────────────────────────────────────────────
    let activeBrand    = 'btmc'; // 'btmc' | 'btmh'
    let activeUnit     = 'NHAN_TRON';
    let activeUnitBtmh = 'KGB';
    let chartDays      = 7;
    let chartInstance  = null;

    // ─── API endpoints ─────────────────────────────────────────
    const API = {
        btmc: { current: '/api/gold/btmc/current', history: '/api/gold/btmc/history' },
        btmh: { current: '/api/gold/btmh/current', history: '/api/gold/btmh/history' },
    };

    // ─── DOM refs ─────────────────────────────────────────────
    const elBtmcUpdated = document.getElementById('btmc-updated');
    const elBtmcBuy     = document.getElementById('btmc-buy');
    const elBtmcSell    = document.getElementById('btmc-sell');
    const elBtmcSpread  = document.getElementById('btmc-spread');

    const elBtmhUpdated = document.getElementById('btmh-updated');
    const elBtmhBuy     = document.getElementById('btmh-buy');
    const elBtmhSell    = document.getElementById('btmh-sell');
    const elBtmhSpread  = document.getElementById('btmh-spread');

    const elLoading     = document.getElementById('gold-chart-loading');
    const elCanvas      = document.getElementById('goldSharedChart');
    const elUnitTabs    = document.getElementById('gold-chart-unit-tabs');
    const elUnitLbl     = document.getElementById('gold-chart-unit-lbl');
    const elFootnote    = document.getElementById('gold-chart-footnote');

    if (!elCanvas) return;
    const ctx = elCanvas.getContext('2d');

    const fmtVnd = (n) => new Intl.NumberFormat('vi-VN').format(n);

    // ─── Load BTMC current price ───────────────────────────────
    function loadBtmcCurrent() {
        fetch(API.btmc.current)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                window.btmcGoldData = res.data;
                updateBtmcCard();
            })
            .catch(err => console.error('[Gold] BTMC current error:', err));
    }

    function updateBtmcCard() {
        const d = window.btmcGoldData?.[activeUnit];
        if (!d) return;
        if (elBtmcBuy)    elBtmcBuy.textContent    = d.buy_formatted;
        if (elBtmcSell)   elBtmcSell.textContent   = d.sell_formatted;
        if (elBtmcSpread) elBtmcSpread.textContent = fmtVnd(d.sell_price - d.buy_price);
        if (elBtmcUpdated) elBtmcUpdated.textContent = d.recorded_at ? `Cập nhật: ${d.recorded_at}` : '';
    }

    // ─── Load BTMH current price ───────────────────────────────
    function loadBtmhCurrent() {
        fetch(API.btmh.current)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                const d = res.data[activeUnitBtmh];
                if (!d) return;
                if (elBtmhBuy)    elBtmhBuy.textContent    = d.buy_formatted;
                if (elBtmhSell)   elBtmhSell.textContent   = d.sell_formatted;
                if (elBtmhSpread) elBtmhSpread.textContent = fmtVnd(d.sell_price - d.buy_price);
                if (elBtmhUpdated) elBtmhUpdated.textContent = d.recorded_at ? `Cập nhật: ${d.recorded_at}` : '';
            })
            .catch(err => console.error('[Gold] BTMH current error:', err));
    }

    // ─── Shared Chart ─────────────────────────────────────────
    function loadChart() {
        if (elLoading) elLoading.style.display = 'flex';
        if (elCanvas)  elCanvas.style.display  = 'none';

        const unit = activeBrand === 'btmc' ? activeUnit : activeUnitBtmh;
        const url  = `${API[activeBrand].history}?days=${chartDays}&type=${unit}`;

        fetch(url)
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data?.dates?.length) {
                    renderChart(res.data, res.type_label || unit);
                } else {
                    if (elLoading) elLoading.innerHTML = '<span style="color:#64748b;font-size:.85rem">Chưa có dữ liệu</span>';
                }
            })
            .catch(err => {
                console.error('[Gold] chart error:', err);
                if (elLoading) elLoading.innerHTML = '<span style="color:#ef4444;font-size:.85rem">Lỗi tải biểu đồ</span>';
            });
    }

    function renderChart(data, typeLabel) {
        if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

        if (elLoading) elLoading.style.display = 'none';
        if (elCanvas)  elCanvas.style.display  = 'block';

        const gradBuy  = ctx.createLinearGradient(0, 0, 0, 320);
        gradBuy.addColorStop(0, 'rgba(34,197,94,0.18)');
        gradBuy.addColorStop(1, 'rgba(34,197,94,0)');

        const gradSell = ctx.createLinearGradient(0, 0, 0, 320);
        gradSell.addColorStop(0, 'rgba(239,68,68,0.18)');
        gradSell.addColorStop(1, 'rgba(239,68,68,0)');

        const allP = [...data.buy_prices, ...data.sell_prices].filter(Boolean);
        const minP = Math.min(...allP);
        const maxP = Math.max(...allP);
        const pad  = Math.round((maxP - minP) * 0.12) || 300000;

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [
                    {
                        label: 'Mua vào',
                        data: data.buy_prices,
                        borderColor: '#22c55e',
                        backgroundColor: gradBuy,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#22c55e',
                    },
                    {
                        label: 'Bán ra',
                        data: data.sell_prices,
                        borderColor: '#ef4444',
                        backgroundColor: gradSell,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#ef4444',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 350 },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', font: { family: 'Inter', size: 12 }, boxWidth: 14 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        titleColor: '#f1f5f9',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(245,197,24,0.3)',
                        borderWidth: 1,
                        padding: 11,
                        cornerRadius: 8,
                        callbacks: {
                            label: (c) => ` ${c.dataset.label}: ${fmtVnd(c.parsed.y)} đ`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                        ticks: {
                            color: '#64748b', font: { size: 11 },
                            maxTicksLimit: chartDays <= 7 ? 7 : (chartDays <= 30 ? 10 : 12),
                            maxRotation: 0,
                        }
                    },
                    y: {
                        min: minP - pad,
                        max: maxP + pad,
                        grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                        ticks: {
                            color: '#64748b', font: { size: 11 },
                            callback: (v) => v >= 1_000_000 ? (v / 1_000_000).toFixed(1) + 'tr' : fmtVnd(v)
                        }
                    }
                }
            }
        });

        // Cập nhật footnote
        if (elFootnote) {
            const src = activeBrand === 'btmc' ? 'Bảo Tín Minh Châu' : 'Bảo Tín Mạnh Hải';
            const u   = activeBrand === 'btmc' ? 'VND/Lượng' : 'đồng/chỉ';
            elFootnote.textContent = `Nguồn: ${src} · ${u}`;
        }
    }

    // ─── Brand card click ─────────────────────────────────────
    document.querySelectorAll('.gold-brand-card').forEach(card => {
        card.addEventListener('click', function () {
            const brand = this.dataset.brand;
            if (brand === activeBrand) return;

            // Cập nhật active card
            document.querySelectorAll('.gold-brand-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            // Cập nhật active chart brand tab
            document.querySelectorAll('.gold-chart-brand').forEach(b => {
                b.classList.toggle('active', b.dataset.brand === brand);
            });

            activeBrand = brand;

            // Ẩn/hiện unit tabs (BTMH chỉ có 1 loại) – dùng visibility để giữ chiều cao ổn định
            if (elUnitTabs) elUnitTabs.style.visibility = brand === 'btmh' ? 'hidden' : 'visible';
            if (elUnitLbl)  elUnitLbl.style.visibility  = brand === 'btmh' ? 'hidden' : 'visible';

            loadChart();
        });
    });

    // ─── Chart brand tab click ────────────────────────────────
    document.querySelectorAll('.gold-chart-brand').forEach(btn => {
        btn.addEventListener('click', function () {
            const brand = this.dataset.brand;
            if (brand === activeBrand) return;

            // Kích hoạt brand card tương ứng
            const card = document.getElementById(`gold-card-${brand}`);
            if (card) card.click();
        });
    });

    // ─── BTMC type tab (trong card) ──────────────────────────
    document.querySelectorAll('#gold-card-btmc .gold-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            const unit = this.dataset.unit;
            activeUnit = unit;

            // Sync card tabs
            document.querySelectorAll('#gold-card-btmc .gold-tab').forEach(t =>
                t.classList.toggle('active', t.dataset.unit === unit)
            );
            // Sync chart unit tabs
            document.querySelectorAll('.gold-chart-unit-btn').forEach(t =>
                t.classList.toggle('active', t.dataset.unit === unit)
            );

            updateBtmcCard();
            if (activeBrand === 'btmc') loadChart();
        });
    });

    // ─── Chart unit tab (trong chart bar) ─────────────────────
    document.querySelectorAll('.gold-chart-unit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const unit = this.dataset.unit;
            if (activeBrand !== 'btmc') return;
            activeUnit = unit;
            // Sync card tabs
            document.querySelectorAll('#gold-card-btmc .gold-tab').forEach(t =>
                t.classList.toggle('active', t.dataset.unit === unit)
            );
            document.querySelectorAll('.gold-chart-unit-btn').forEach(t =>
                t.classList.toggle('active', t.dataset.unit === unit)
            );
            updateBtmcCard();
            loadChart();
        });
    });

    // ─── Period tabs ──────────────────────────────────────────
    document.querySelectorAll('.gold-prd').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.gold-prd').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            chartDays = parseInt(this.dataset.days, 10);
            loadChart();
        });
    });

    // ─── Init ─────────────────────────────────────────────────
    loadBtmcCurrent();
    loadBtmhCurrent();
    loadChart();

    // Auto-refresh giá mỗi 3 phút
    setInterval(loadBtmcCurrent, 3 * 60 * 1000);
    setInterval(loadBtmhCurrent, 10 * 60 * 1000);
});
