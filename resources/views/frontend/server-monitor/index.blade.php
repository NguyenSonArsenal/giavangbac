<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Monitor</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0a0a1a;
            color: #e0e0e0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding: 20px;
            min-height: 100vh;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            background: linear-gradient(135deg, #00d4ff, #7b2ff7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }
        .header p { color: #888; font-size: 14px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: linear-gradient(145deg, #12122a, #1a1a35);
            border: 1px solid #2a2a50;
            border-radius: 16px;
            padding: 24px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 212, 255, 0.1);
        }
        .card h2 {
            font-size: 16px;
            color: #00d4ff;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-container {
            position: relative;
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            gap: 10px;
        }
        .stat-item {
            text-align: center;
            flex: 1;
            padding: 10px;
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }
        .stat-label {
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }

        /* Project bar chart */
        .project-list { margin-top: 10px; }
        .project-item {
            margin-bottom: 12px;
        }
        .project-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 13px;
        }
        .project-name { color: #ccc; font-weight: 500; }
        .project-size { color: #00d4ff; font-weight: 600; }
        .project-bar-bg {
            height: 8px;
            background: #1e1e40;
            border-radius: 4px;
            overflow: hidden;
        }
        .project-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 1s ease;
        }

        .info-table {
            width: 100%;
            margin-top: 8px;
        }
        .info-table tr { border-bottom: 1px solid #1e1e40; }
        .info-table td {
            padding: 10px 0;
            font-size: 13px;
        }
        .info-table td:first-child { color: #888; width: 40%; }
        .info-table td:last-child { color: #fff; font-weight: 500; }

        .full-width { grid-column: 1 / -1; }

        /* Percent badge */
        .percent-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .percent-good { background: rgba(0, 200, 83, 0.15); color: #00c853; }
        .percent-warn { background: rgba(255, 171, 0, 0.15); color: #ffab00; }
        .percent-danger { background: rgba(255, 68, 68, 0.15); color: #ff4444; }

        .refresh-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            background: linear-gradient(135deg, #00d4ff, #7b2ff7);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
            transition: opacity 0.2s;
        }
        .refresh-btn:hover { opacity: 0.85; }

        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
            body { padding: 12px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚡ Server Monitor</h1>
        <p>{{ $data['system']['hostname'] }} • {{ $data['system']['os'] }} • Uptime: {{ $data['system']['uptime'] }}</p>
        <button class="refresh-btn" onclick="location.reload()">🔄 Refresh</button>
    </div>

    <div class="grid">
        {{-- DISK USAGE --}}
        <div class="card">
            <h2>💾 Disk Usage</h2>
            <div class="chart-container">
                <canvas id="diskChart"></canvas>
            </div>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $data['disk']['used'] }}</div>
                    <div class="stat-label">Đã dùng</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['disk']['free'] }}</div>
                    <div class="stat-label">Còn trống</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['disk']['total'] }}</div>
                    <div class="stat-label">Tổng</div>
                </div>
            </div>
        </div>

        {{-- RAM USAGE --}}
        <div class="card">
            <h2>🧠 RAM Usage</h2>
            <div class="chart-container">
                <canvas id="ramChart"></canvas>
            </div>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $data['ram']['used'] }}</div>
                    <div class="stat-label">Đã dùng</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['ram']['free'] }}</div>
                    <div class="stat-label">Còn trống</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $data['ram']['total'] }}</div>
                    <div class="stat-label">Tổng</div>
                </div>
            </div>
        </div>

        {{-- PROJECTS --}}
        <div class="card full-width">
            <h2>📁 Dung lượng theo Project</h2>
            <div class="project-list">
                @php
                    $colors = ['#00d4ff', '#7b2ff7', '#ff6b6b', '#ffd93d', '#6bcb77', '#4d96ff', '#ff8c32'];
                    $maxSize = collect($data['projects'])->max('size_raw') ?: 1;
                @endphp
                @foreach ($data['projects'] as $i => $project)
                    <div class="project-item">
                        <div class="project-header">
                            <span class="project-name">📂 {{ $project['name'] }}</span>
                            <span class="project-size">{{ $project['size'] }}</span>
                        </div>
                        <div class="project-bar-bg">
                            <div class="project-bar" style="width: {{ round(($project['size_raw'] / $maxSize) * 100) }}%; background: {{ $colors[$i % count($colors)] }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SYSTEM INFO --}}
        <div class="card full-width">
            <h2>🖥️ System Info</h2>
            <table class="info-table">
                <tr><td>Hostname</td><td>{{ $data['system']['hostname'] }}</td></tr>
                <tr><td>OS</td><td>{{ $data['system']['os'] }}</td></tr>
                <tr><td>PHP Version</td><td>{{ $data['system']['php'] }}</td></tr>
                <tr><td>Uptime</td><td>{{ $data['system']['uptime'] }}</td></tr>
                <tr><td>Server Time</td><td>{{ $data['system']['server_time'] }}</td></tr>
                <tr>
                    <td>Disk Used</td>
                    <td>
                        {{ $data['disk']['percent'] }}%
                        @if($data['disk']['percent'] < 50)
                            <span class="percent-badge percent-good">Healthy</span>
                        @elseif($data['disk']['percent'] < 80)
                            <span class="percent-badge percent-warn">Warning</span>
                        @else
                            <span class="percent-badge percent-danger">Critical</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>RAM Used</td>
                    <td>
                        {{ $data['ram']['percent'] }}%
                        @if($data['ram']['percent'] < 50)
                            <span class="percent-badge percent-good">Healthy</span>
                        @elseif($data['ram']['percent'] < 80)
                            <span class="percent-badge percent-warn">Warning</span>
                        @else
                            <span class="percent-badge percent-danger">Critical</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        // Doughnut chart config
        const doughnutOptions = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a35',
                    borderColor: '#2a2a50',
                    borderWidth: 1,
                    titleColor: '#00d4ff',
                    bodyColor: '#e0e0e0',
                    padding: 12,
                    cornerRadius: 8,
                }
            }
        };

        // Center text plugin
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                const { ctx, width, height } = chart;
                const percent = chart.config.data.datasets[0].data[0];
                const total = chart.config.data.datasets[0].data[0] + chart.config.data.datasets[0].data[1];
                const pct = Math.round((percent / total) * 100);

                ctx.save();
                ctx.font = 'bold 28px Segoe UI';
                ctx.fillStyle = '#fff';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(pct + '%', width / 2, height / 2);
                ctx.restore();
            }
        };

        Chart.register(centerTextPlugin);

        // Disk chart
        new Chart(document.getElementById('diskChart'), {
            type: 'doughnut',
            data: {
                labels: ['Đã dùng', 'Còn trống'],
                datasets: [{
                    data: [{{ $data['disk']['used_raw'] }}, {{ $data['disk']['free_raw'] }}],
                    backgroundColor: [
                        '{{ $data["disk"]["percent"] > 80 ? "#ff4444" : ($data["disk"]["percent"] > 50 ? "#ffab00" : "#00d4ff") }}',
                        'rgba(255,255,255,0.05)'
                    ],
                    borderWidth: 0,
                    borderRadius: 6,
                    spacing: 4,
                }]
            },
            options: doughnutOptions,
        });

        // RAM chart
        new Chart(document.getElementById('ramChart'), {
            type: 'doughnut',
            data: {
                labels: ['Đã dùng', 'Còn trống'],
                datasets: [{
                    data: [{{ $data['ram']['used_raw'] ?? 0 }}, {{ $data['ram']['free_raw'] ?? 0 }}],
                    backgroundColor: [
                        '{{ $data["ram"]["percent"] > 80 ? "#ff4444" : ($data["ram"]["percent"] > 50 ? "#ffab00" : "#7b2ff7") }}',
                        'rgba(255,255,255,0.05)'
                    ],
                    borderWidth: 0,
                    borderRadius: 6,
                    spacing: 4,
                }]
            },
            options: doughnutOptions,
        });
    </script>
</body>
</html>
