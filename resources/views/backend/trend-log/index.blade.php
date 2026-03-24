@extends('backend.layout.main')

@push('style')
<style>
  .trend-cards { display:flex; flex-wrap:wrap; gap:16px; margin-bottom:24px; }
  .trend-card { flex:1; min-width:140px; background:#1e2a3a; border-radius:10px; padding:16px; text-align:center; }
  .trend-card-label { font-size:12px; color:#6e778c; text-transform:uppercase; margin-bottom:4px; }
  .trend-card-val { font-size:28px; font-weight:700; }

  .tbl-wrap { background:#1e2a3a; border-radius:10px; padding:20px; margin-bottom:24px; }
  .tbl-head { font-weight:600; margin-bottom:14px; font-size:16px; }
  .tbl-wrap table { width:100%; font-size:13px; border-collapse:collapse; }
  .tbl-wrap th { color:#6e778c; text-transform:uppercase; font-size:10px; padding:10px 8px; border-bottom:1px solid rgba(255,255,255,0.08); text-align:left; }
  .tbl-wrap td { padding:10px 8px; border-bottom:1px solid rgba(255,255,255,0.04); vertical-align:top; }
  .mono { font-family:'JetBrains Mono',monospace; }

  .trend-analysis { line-height:1.6; font-size:12.5px; color:#c8cfe0; }

  .badge-src { display:inline-block; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:700; }
  .badge-gemini { background:rgba(79,122,248,0.15); color:#4f7af8; }
  .badge-fallback { background:rgba(245,158,11,0.15); color:#f59e0b; }

  .badge-trend { display:inline-block; padding:2px 10px; border-radius:4px; font-size:11px; font-weight:700; }
  .badge-up { background:rgba(34,201,122,0.12); color:#22c97a; }
  .badge-down { background:rgba(245,82,82,0.12); color:#f55252; }
  .badge-flat { background:rgba(255,255,255,0.06); color:#909ab2; }

  .pct-change { font-weight:700; font-family:'JetBrains Mono',monospace; }
  .pct-up   { color:#22c97a; }
  .pct-down { color:#f55252; }

  .accuracy-btns { display:flex; gap:6px; align-items:center; }
  .acc-btn {
    padding:4px 10px; border-radius:5px; font-size:11px; font-weight:600;
    border:1px solid rgba(255,255,255,0.1); background:transparent;
    cursor:pointer; transition:all .2s; color:#909ab2;
  }
  .acc-btn:hover { border-color:rgba(255,255,255,0.3); }

  /* ── Result badges (shown after review) ── */
  .acc-result {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 14px; border-radius:6px; font-size:12px; font-weight:700;
  }
  .acc-result.is-correct {
    background:rgba(34,201,122,0.15); color:#22c97a; border:1px solid rgba(34,201,122,0.3);
  }
  .acc-result.is-wrong {
    background:rgba(245,82,82,0.15); color:#f55252; border:1px solid rgba(245,82,82,0.3);
  }
  .acc-pending {
    font-size:11px; color:#6e778c; font-style:italic;
  }

  .pagination-wrap { display:flex; justify-content:center; margin-top:16px; }
  .pagination-wrap .pagination { display:flex; gap:4px; list-style:none; padding:0; }
  .pagination-wrap .page-link {
    padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600;
    background:#1e2a3a; border:1px solid rgba(255,255,255,0.08); color:#909ab2;
    text-decoration:none; transition:all .2s;
  }
  .pagination-wrap .page-link:hover { border-color:#4f7af8; color:#4f7af8; }
  .pagination-wrap .page-item.active .page-link { background:#4f7af8; color:#fff; border-color:#4f7af8; }
  .pagination-wrap .page-item.disabled .page-link { opacity:0.4; cursor:not-allowed; }

  /* ── Confirm Modal ── */
  .confirm-overlay {
    display:none; position:fixed; top:0; left:0; right:0; bottom:0;
    background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);
    z-index:10001; justify-content:center; align-items:center;
  }
  .confirm-overlay.is-open { display:flex; }
  .confirm-box {
    background:#1a1d2e; border:1px solid rgba(255,255,255,0.1);
    border-radius:12px; padding:28px 32px; max-width:380px; width:90%;
    text-align:center;
    box-shadow:0 20px 60px rgba(0,0,0,0.5);
    animation: confirmPopIn .2s ease;
  }
  @keyframes confirmPopIn {
    from { transform:scale(0.9); opacity:0; }
    to { transform:scale(1); opacity:1; }
  }
  .confirm-icon { font-size:36px; margin-bottom:10px; }
  .confirm-title { font-size:16px; font-weight:700; color:#e4e8f2; margin-bottom:8px; }
  .confirm-desc { font-size:13px; color:#909ab2; margin-bottom:20px; line-height:1.5; }
  .confirm-actions { display:flex; gap:10px; justify-content:center; }
  .confirm-btn {
    padding:8px 24px; border-radius:8px; font-size:13px; font-weight:700;
    border:none; cursor:pointer; transition:all .2s;
  }
  .confirm-btn-yes {
    color:#fff;
  }
  .confirm-btn-yes.for-correct { background:#22c97a; }
  .confirm-btn-yes.for-correct:hover { background:#1db36a; }
  .confirm-btn-yes.for-wrong { background:#f55252; }
  .confirm-btn-yes.for-wrong:hover { background:#e03e3e; }
  .confirm-btn-cancel {
    background:rgba(255,255,255,0.06); color:#909ab2;
    border:1px solid rgba(255,255,255,0.1);
  }
  .confirm-btn-cancel:hover { border-color:rgba(255,255,255,0.25); color:#e4e8f2; }
</style>
@endpush

@section('content')
<div class="content-page">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">🤖 Nhận Định Xu Hướng — AI Log</h4>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Overview cards --}}
        <div class="trend-cards">
            <div class="trend-card">
                <div class="trend-card-label">Tổng nhận định</div>
                <div class="trend-card-val" id="cardTotal" style="color:#4f7af8">{{ $logs->total() }}</div>
            </div>
            <div class="trend-card">
                <div class="trend-card-label">Gemini AI</div>
                <div class="trend-card-val" style="color:#a78bfa">{{ \App\Models\SilverTrendLog::where('source','gemini')->count() }}</div>
            </div>
            <div class="trend-card">
                <div class="trend-card-label">Fallback</div>
                <div class="trend-card-val" style="color:#f59e0b">{{ \App\Models\SilverTrendLog::where('source','fallback')->count() }}</div>
            </div>
            <div class="trend-card">
                <div class="trend-card-label">Đúng ✓</div>
                <div class="trend-card-val" id="cardCorrect" style="color:#22c97a">{{ \App\Models\SilverTrendLog::where('is_accurate',true)->count() }}</div>
            </div>
            <div class="trend-card">
                <div class="trend-card-label">Sai ✗</div>
                <div class="trend-card-val" id="cardWrong" style="color:#f55252">{{ \App\Models\SilverTrendLog::where('is_accurate',false)->count() }}</div>
            </div>
        </div>

        {{-- Table --}}
        <div class="tbl-wrap">
            <div class="tbl-head">📋 Lịch sử nhận định</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:30px">#</th>
                        <th style="width:110px">Thời gian</th>
                        <th style="width:70px">Nguồn</th>
                        <th style="width:80px">Xu hướng</th>
                        <th style="width:80px">% Thay đổi</th>
                        <th>Nội dung nhận định</th>
                        <th style="width:100px">Đánh giá</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="mono">{{ $log->id }}</td>
                        <td class="mono" style="white-space:nowrap">{{ $log->created_at->format('H:i d/m/Y') }}</td>
                        <td>
                            <span class="badge-src {{ $log->source === 'gemini' ? 'badge-gemini' : 'badge-fallback' }}">
                                {{ strtoupper($log->source) }}
                            </span>
                        </td>
                        <td>
                            @if($log->trend === 'tăng')
                                <span class="badge-trend badge-up">▲ Tăng</span>
                            @elseif($log->trend === 'giảm')
                                <span class="badge-trend badge-down">▼ Giảm</span>
                            @else
                                <span class="badge-trend badge-flat">▸ Đi ngang</span>
                            @endif
                        </td>
                        <td>
                            <span class="pct-change {{ $log->pct_change > 0 ? 'pct-up' : ($log->pct_change < 0 ? 'pct-down' : '') }}">
                                {{ $log->pct_change > 0 ? '+' : '' }}{{ $log->pct_change }}%
                            </span>
                        </td>
                        <td>
                            <div class="trend-analysis">{!! nl2br(e($log->analysis)) !!}</div>
                        </td>
                        <td>
                            <div class="accuracy-cell" data-log-id="{{ $log->id }}">
                                @if($log->is_accurate === true)
                                    <span class="acc-result is-correct">✓ Đúng</span>
                                @elseif($log->is_accurate === false)
                                    <span class="acc-result is-wrong">✗ Sai</span>
                                @else
                                    <div class="accuracy-btns">
                                        <button class="acc-btn" onclick="confirmAccuracy({{ $log->id }}, true)" title="Đánh giá: Đúng">✓ Đúng</button>
                                        <button class="acc-btn" onclick="confirmAccuracy({{ $log->id }}, false)" title="Đánh giá: Sai">✗ Sai</button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#6e778c;padding:32px">Chưa có nhận định nào.</td></tr>
                @endforelse
                </tbody>
            </table>

            @if($logs->hasPages())
                <div class="pagination-wrap">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon" id="confirmIcon"></div>
        <div class="confirm-title" id="confirmTitle"></div>
        <div class="confirm-desc" id="confirmDesc"></div>
        <div class="confirm-actions">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeConfirm()">Hủy</button>
            <button class="confirm-btn confirm-btn-yes" id="confirmYesBtn" onclick="submitAccuracy()">Xác nhận</button>
        </div>
    </div>
</div>
@stop

@push('script')
<script>
var pendingLogId = null;
var pendingValue = null;

function confirmAccuracy(logId, isAccurate) {
    pendingLogId = logId;
    pendingValue = isAccurate;

    var icon = document.getElementById('confirmIcon');
    var title = document.getElementById('confirmTitle');
    var desc = document.getElementById('confirmDesc');
    var yesBtn = document.getElementById('confirmYesBtn');

    if (isAccurate) {
        icon.textContent = '✅';
        title.textContent = 'Xác nhận nhận định ĐÚNG?';
        desc.textContent = 'Bạn đánh giá rằng nhận định #' + logId + ' là chính xác. Hành động này không thể hoàn tác.';
        yesBtn.className = 'confirm-btn confirm-btn-yes for-correct';
    } else {
        icon.textContent = '❌';
        title.textContent = 'Xác nhận nhận định SAI?';
        desc.textContent = 'Bạn đánh giá rằng nhận định #' + logId + ' không chính xác. Hành động này không thể hoàn tác.';
        yesBtn.className = 'confirm-btn confirm-btn-yes for-wrong';
    }

    document.getElementById('confirmOverlay').classList.add('is-open');
}

function closeConfirm() {
    document.getElementById('confirmOverlay').classList.remove('is-open');
    pendingLogId = null;
    pendingValue = null;
}

function submitAccuracy() {
    if (pendingLogId === null) return;

    var logId = pendingLogId;
    var isAccurate = pendingValue;
    closeConfirm();

    fetch('{{ url("management/trend-log") }}/' + logId + '/accuracy', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ is_accurate: isAccurate })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            // Update cell: replace buttons with result badge
            var cell = document.querySelector('[data-log-id="' + logId + '"]');
            if (cell) {
                if (isAccurate) {
                    cell.innerHTML = '<span class="acc-result is-correct">✓ Đúng</span>';
                } else {
                    cell.innerHTML = '<span class="acc-result is-wrong">✗ Sai</span>';
                }
            }

            // Update overview cards realtime
            var cardCorrect = document.getElementById('cardCorrect');
            var cardWrong = document.getElementById('cardWrong');
            if (isAccurate && cardCorrect) {
                cardCorrect.textContent = parseInt(cardCorrect.textContent) + 1;
            } else if (!isAccurate && cardWrong) {
                cardWrong.textContent = parseInt(cardWrong.textContent) + 1;
            }
        }
    });
}

// Click overlay to close
document.getElementById('confirmOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});
</script>
@endpush
