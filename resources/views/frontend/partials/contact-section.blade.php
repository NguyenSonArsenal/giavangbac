{{-- ═══════════════════════════════════════════════════════
     SHARED CONTACT SECTION
     @include('frontend.partials.contact-section', ['variant' => 'home|page'])
     - 'home' = compact style cho Homepage
     - 'page' = full-width style cho trang /lien-he
═══════════════════════════════════════════════════════ --}}

@php $variant = $variant ?? 'home'; @endphp

@push('styles')
<style>
  /* ── CONTACT SECTION — shared styles ── */
  .cs-section { margin-top: 36px; }
  .cs-header {
    display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
  }
  .cs-header-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: linear-gradient(135deg, #2563eb, #4f7af8);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
  }
  .cs-header h2 { font-size: 17px; font-weight: 800; color: #e4e8f2; margin: 0; }
  .cs-header p  { font-size: 11.5px; color: var(--muted); margin: 2px 0 0; }

  .cs-wrapper {
    display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: flex-start;
  }
  @media (max-width: 800px) { .cs-wrapper { grid-template-columns: 1fr; } }

  /* Form card */
  .cs-form-card {
    background: var(--bg2, rgba(255,255,255,0.03));
    border: 1px solid rgba(79,122,248,0.15);
    border-radius: var(--radius, 14px);
    padding: 24px 20px;
  }
  .cs-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  @media (max-width: 600px) { .cs-row { grid-template-columns: 1fr; } }
  .cs-group { margin-bottom: 14px; }
  .cs-group label {
    display: block; color: var(--text2, #c4cad8); font-size: 12.5px;
    font-weight: 600; margin-bottom: 5px;
  }
  .cs-req { color: #ef4444; }
  .cs-group input,
  .cs-group textarea {
    width: 100%; background: rgba(255,255,255,0.05);
    border: 1px solid var(--border, rgba(255,255,255,0.08));
    border-radius: 8px; color: var(--text, #e4e8f2); font-size: 13.5px;
    padding: 10px 14px; transition: border-color .2s, box-shadow .2s;
    font-family: inherit; outline: none; box-sizing: border-box;
  }
  .cs-group input::placeholder,
  .cs-group textarea::placeholder { color: var(--muted, #6e778c); }
  .cs-group input:focus,
  .cs-group textarea:focus {
    border-color: var(--blue, #4f7af8);
    box-shadow: 0 0 0 3px rgba(79,122,248,0.15);
  }
  .cs-group textarea { min-height: 100px; resize: vertical; }
  .cs-error { color: #f87171; font-size: 11.5px; margin-top: 3px; }

  .cs-submit {
    width: 100%; background: linear-gradient(135deg, #2563eb, #4f7af8);
    color: #fff; border: none; border-radius: 22px; padding: 12px 24px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    transition: transform .15s, box-shadow .15s, opacity .15s;
  }
  .cs-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(79,122,248,0.35); }
  .cs-submit:active { transform: translateY(0); }
  .cs-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

  /* Info column */
  .cs-info { display: flex; flex-direction: column; gap: 18px; padding-top: 8px; }
  .cs-info-item { display: flex; align-items: flex-start; gap: 14px; }
  .cs-info-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0; font-style: normal;
  }
  .cs-info-label { font-weight: 700; font-size: 13.5px; color: var(--text, #e4e8f2); margin-bottom: 2px; }
  .cs-info-value { color: var(--muted, #6e778c); font-size: 13px; line-height: 1.5; }

  /* Toast — appended to body via JS, so no stacking context issue */
  .cs-toast {
    position: fixed; top: 20px; right: 20px; padding: 13px 20px; border-radius: 10px;
    font-size: 13px; font-weight: 600; color: #fff; z-index: 99999;
    opacity: 0; transform: translateX(40px); transition: opacity .35s, transform .35s; max-width: 360px;
    pointer-events: none;
  }
  .cs-toast.show { opacity: 1; transform: translateX(0); pointer-events: auto; }
  .cs-toast.success { background: linear-gradient(135deg, #22c55e, #16a34a); box-shadow: 0 4px 16px rgba(34,197,94,0.3); }
  .cs-toast.error   { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 16px rgba(239,68,68,0.3); }

  /* variant: page — heading lớn hơn */
  .cs-section.cs-page .cs-header { justify-content: center; flex-direction: column; align-items: center; text-align: center; }
  .cs-section.cs-page .cs-header-icon { display: none; }
  .cs-section.cs-page .cs-header h2 { font-size: 30px; }
  .cs-section.cs-page .cs-header h2 span { color: var(--blue, #4f7af8); font-style: italic; }

  @media (max-width: 600px) {
    .cs-section { margin-top: 20px; }
    .cs-header { padding: 0 12px; }
    .cs-form-card { border-radius: 0; border-left: none; border-right: none; }
    .cs-section.cs-page .cs-header h2 { font-size: 22px; }
  }
</style>
@endpush

<section class="cs-section {{ $variant === 'page' ? 'cs-page' : '' }}" id="section-contact">
  <div class="cs-header">
    <div class="cs-header-icon">📩</div>
    <div>
      <h2>Liên hệ <span style="color:var(--blue,#4f7af8);font-style:italic">với chúng tôi</span></h2>
      <p>Hãy để lại thông tin, chúng tôi sẽ liên hệ với bạn sớm nhất</p>
    </div>
  </div>
  <div class="cs-wrapper">
    <div class="cs-form-card">
      <form class="cs-form" id="csContactForm">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="cs-row">
          <div class="cs-group">
            <label>Họ và tên <span class="cs-req">*</span></label>
            <input type="text" name="name" placeholder="Nguyễn Văn A" required>
            <div class="cs-error" id="cs-err-name"></div>
          </div>
          <div class="cs-group">
            <label>Email <span class="cs-req">*</span></label>
            <input type="email" name="email" placeholder="example@email.com" required>
            <div class="cs-error" id="cs-err-email"></div>
          </div>
        </div>
        <div class="cs-row">
          <div class="cs-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone" placeholder="0123456789">
          </div>
        </div>
        <div class="cs-group">
          <label>Tin nhắn <span class="cs-req">*</span></label>
          <textarea name="message" placeholder="Nội dung tin nhắn..." required></textarea>
          <div class="cs-error" id="cs-err-message"></div>
        </div>
        <button type="submit" class="cs-submit" id="csContactBtn">Gửi tin nhắn</button>
      </form>
    </div>
    <div class="cs-info">
      <div class="cs-info-item">
        <div class="cs-info-icon" style="background:linear-gradient(135deg,#ef4444,#f97316)">📍</div>
        <div><div class="cs-info-label">Địa chỉ</div><div class="cs-info-value">1 Đ. Đại Cồ Việt, Bách Khoa, Bạch Mai, Hà Nội</div></div>
      </div>
      <div class="cs-info-item">
        <div class="cs-info-icon" style="background:linear-gradient(135deg,#4f7af8,#6366f1)">📧</div>
        <div><div class="cs-info-label">Email</div><div class="cs-info-value">vanson297.nguyen@gmail.com</div></div>
      </div>
      <div class="cs-info-item">
        <div class="cs-info-icon" style="background:linear-gradient(135deg,#ef4444,#ec4899)">🕐</div>
        <div><div class="cs-info-label">Giờ làm việc</div><div class="cs-info-value">8h30–18h00</div></div>
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
(function () {
  var form = document.getElementById('csContactForm');
  var btn  = document.getElementById('csContactBtn');
  if (!form || !btn) return;
  var btnTxt = btn.textContent;

  // Append toast to body — escapes any stacking context
  var toast = document.createElement('div');
  toast.id = 'csContactToast';
  toast.className = 'cs-toast';
  document.body.appendChild(toast);

  function showToast(msg, type) {
    toast.className = 'cs-toast ' + type;
    toast.textContent = msg;
    setTimeout(function(){ toast.classList.add('show'); }, 10);
    setTimeout(function(){ toast.classList.remove('show'); }, 4000);
  }

  function clearErrors() {
    form.querySelectorAll('.cs-error').forEach(function(el){ el.textContent = ''; });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    clearErrors();
    btn.disabled = true;
    btn.textContent = 'Đang gửi...';

    fetch('/lien-he', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: new FormData(form)
    })
    .then(function(res) { return res.json().then(function(json) { return {ok: res.ok, status: res.status, data: json}; }); })
    .then(function(result) {
      if (result.ok) {
        showToast(result.data.message, 'success');
        form.reset();
      } else if (result.status === 422) {
        var errors = result.data.errors;
        for (var field in errors) {
          var el = document.getElementById('cs-err-' + field);
          if (el) el.textContent = errors[field][0];
        }
      } else {
        showToast(result.data.message || 'Đã có lỗi xảy ra', 'error');
      }
    })
    .catch(function() { showToast('Đã có lỗi xảy ra, vui lòng thử lại', 'error'); })
    .finally(function() { btn.disabled = false; btn.textContent = btnTxt; });
  });
})();
</script>
@endpush
