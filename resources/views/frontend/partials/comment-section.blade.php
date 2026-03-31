{{-- ═══════════════════════════════════════════════════════
     COMMENT SECTION — Dynamic
     Requires: $comments (collection), $commentCount (int), $post
═══════════════════════════════════════════════════════ --}}

@push('styles')
<style>
/* ══ COMMENT SECTION ══ */
.cmt-section {
  margin-top: 44px;
  padding-top: 28px;
  border-top: 1px solid var(--border);
}

/* Header */
.cmt-heading {
  display: flex; align-items: center; gap: 10px;
  margin-bottom: 24px;
}
.cmt-heading-icon { font-size: 20px; line-height: 1; }
.cmt-heading h3 { font-size: 18px; font-weight: 800; color: var(--text); margin: 0; }
.cmt-heading .cmt-count { color: var(--muted); font-weight: 600; }

/* ── Compose box ── */
.cmt-compose {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius, 14px);
  padding: 20px;
  margin-bottom: 32px;
}
.cmt-compose-fields {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  margin-bottom: 12px;
}
@media (max-width: 500px) { .cmt-compose-fields { grid-template-columns: 1fr; } }
.cmt-compose-field input {
  width: 100%; background: rgba(255,255,255,0.04);
  border: 1px solid var(--border); border-radius: 8px;
  padding: 10px 14px; font-size: 13.5px; font-family: inherit;
  color: var(--text); outline: none; box-sizing: border-box;
  transition: border-color .2s, box-shadow .2s;
}
.cmt-compose-field input::placeholder { color: var(--muted); }
.cmt-compose-field input:focus {
  border-color: var(--blue); box-shadow: 0 0 0 3px rgba(79,122,248,0.12);
}
.cmt-compose-field label {
  display: block; font-size: 12px; font-weight: 600;
  color: var(--text2); margin-bottom: 5px;
}
.cmt-compose-field .cmt-field-req { color: #ef4444; }
.cmt-compose-field .cmt-field-error {
  color: #f87171; font-size: 11.5px; margin-top: 3px; min-height: 16px;
}
.cmt-compose textarea {
  width: 100%; min-height: 80px; resize: vertical;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border);
  border-radius: 10px; padding: 12px 14px;
  font-size: 14px; font-family: inherit;
  color: var(--text); outline: none;
  transition: border-color .2s, box-shadow .2s;
  box-sizing: border-box;
}
.cmt-compose textarea::placeholder { color: var(--muted); }
.cmt-compose textarea:focus {
  border-color: var(--blue); box-shadow: 0 0 0 3px rgba(79,122,248,0.12);
}
.cmt-compose-footer {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: 10px;
}
.cmt-char-count { font-size: 12px; color: var(--muted); font-family: 'JetBrains Mono', monospace; }
.cmt-submit-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 20px; border-radius: 22px; border: none;
  background: linear-gradient(135deg, #2563eb, #4f7af8);
  color: #fff; font-size: 13px; font-weight: 700;
  cursor: pointer; transition: transform .15s, box-shadow .15s;
  font-family: 'Inter', sans-serif;
}
.cmt-submit-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(79,122,248,0.35);
}
.cmt-submit-btn:active { transform: translateY(0); }
.cmt-submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.cmt-submit-btn svg { width: 14px; height: 14px; }

/* ── Comment list ── */
.cmt-list { display: flex; flex-direction: column; gap: 0; }

.cmt-item { padding: 18px 0; border-top: 1px solid rgba(255,255,255,0.04); }
.cmt-list > .cmt-item:first-child { border-top: none; }
.cmt-row { display: flex; gap: 12px; align-items: flex-start; }

/* Avatar */
.cmt-avatar {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; font-weight: 800; color: #fff; text-transform: uppercase;
}

.cmt-body { flex: 1; min-width: 0; }
.cmt-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap; }
.cmt-author { font-size: 14px; font-weight: 700; color: var(--text); }
.cmt-admin-badge {
  display: inline-flex; align-items: center; gap: 3px;
  font-size: 10.5px; font-weight: 800;
  color: #4f7af8; background: rgba(79,122,248,0.12);
  padding: 2px 8px; border-radius: 10px; letter-spacing: 0.02em;
}
.cmt-admin-badge svg { width: 12px; height: 12px; }
.cmt-time { font-size: 12px; color: var(--muted); }
.cmt-text { font-size: 14px; line-height: 1.65; color: var(--text2); margin: 0; word-break: break-word; }
.cmt-item.is-admin .cmt-text { color: #7da0fa; }

.cmt-actions { display: flex; gap: 12px; margin-top: 6px; }
.cmt-reply-btn {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 12.5px; font-weight: 600; color: var(--muted);
  cursor: pointer; background: none; border: none;
  padding: 2px 0; transition: color .18s; font-family: 'Inter', sans-serif;
}
.cmt-reply-btn:hover { color: var(--blue); }
.cmt-reply-btn svg { width: 13px; height: 13px; }

/* ── Nested replies ── */
.cmt-replies {
  margin-left: 50px; padding-left: 16px;
  border-left: 2px solid rgba(79,122,248,0.12);
}
.cmt-replies .cmt-item { padding: 14px 0; }
.cmt-replies .cmt-avatar { width: 30px; height: 30px; font-size: 12px; }

/* ── Reply compose (inline) ── */
.cmt-reply-compose {
  margin-left: 50px; margin-top: 8px;
  padding: 14px;
  background: rgba(79,122,248,0.04);
  border: 1px solid rgba(79,122,248,0.12);
  border-radius: 10px;
  display: none;
}
.cmt-reply-compose.open { display: block; }
.cmt-reply-compose .cmt-reply-fields {
  display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
  margin-bottom: 10px;
}
@media (max-width: 500px) { .cmt-reply-compose .cmt-reply-fields { grid-template-columns: 1fr; } }
.cmt-reply-compose input {
  width: 100%; background: rgba(255,255,255,0.04);
  border: 1px solid var(--border); border-radius: 8px;
  padding: 8px 12px; font-size: 13px; font-family: inherit;
  color: var(--text); outline: none; box-sizing: border-box;
}
.cmt-reply-compose input:focus { border-color: var(--blue); }
.cmt-reply-compose textarea {
  width: 100%; min-height: 60px; resize: vertical;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border); border-radius: 8px;
  padding: 10px 12px; font-size: 13px; font-family: inherit;
  color: var(--text); outline: none; box-sizing: border-box;
  transition: border-color .2s;
}
.cmt-reply-compose textarea:focus { border-color: var(--blue); }
.cmt-reply-compose-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px; }
.cmt-reply-cancel {
  padding: 6px 14px; border-radius: 18px; border: 1px solid var(--border);
  background: none; color: var(--muted2); font-size: 12px; font-weight: 600;
  cursor: pointer; font-family: 'Inter', sans-serif;
}
.cmt-reply-cancel:hover { border-color: var(--border2); color: var(--text); }
.cmt-reply-send {
  padding: 6px 16px; border-radius: 18px; border: none;
  background: linear-gradient(135deg, #2563eb, #4f7af8);
  color: #fff; font-size: 12px; font-weight: 700;
  cursor: pointer; font-family: 'Inter', sans-serif;
}
.cmt-reply-send:hover { box-shadow: 0 3px 12px rgba(79,122,248,0.35); }

/* Toast */
.cmt-toast {
  position: fixed; top: 20px; right: 20px; padding: 13px 20px; border-radius: 10px;
  font-size: 13px; font-weight: 600; color: #fff; z-index: 99999;
  opacity: 0; transform: translateX(40px); transition: opacity .35s, transform .35s;
  max-width: 360px; pointer-events: none;
}
.cmt-toast.show { opacity: 1; transform: translateX(0); pointer-events: auto; }
.cmt-toast.success { background: linear-gradient(135deg, #22c55e, #16a34a); box-shadow: 0 4px 16px rgba(34,197,94,0.3); }
.cmt-toast.error { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 16px rgba(239,68,68,0.3); }

/* Empty state */
.cmt-empty {
  text-align: center; padding: 32px 16px;
  color: var(--muted); font-size: 14px;
}
.cmt-empty-icon { font-size: 36px; margin-bottom: 8px; }

/* Mobile */
@media (max-width: 600px) {
  .cmt-compose { padding: 14px; }
  .cmt-replies { margin-left: 28px; padding-left: 12px; }
  .cmt-reply-compose { margin-left: 28px; }
  .cmt-avatar { width: 32px; height: 32px; font-size: 13px; }
  .cmt-heading h3 { font-size: 16px; }
}
</style>
@endpush

@php
  $avatarGradients = [
    'linear-gradient(135deg,#6366f1,#4f46e5)',
    'linear-gradient(135deg,#22c97a,#059669)',
    'linear-gradient(135deg,#f59e0b,#d97706)',
    'linear-gradient(135deg,#ec4899,#be185d)',
    'linear-gradient(135deg,#14b8a6,#0d9488)',
    'linear-gradient(135deg,#ef4444,#dc2626)',
    'linear-gradient(135deg,#8b5cf6,#7c3aed)',
    'linear-gradient(135deg,#06b6d4,#0284c7)',
  ];
@endphp

<section class="cmt-section" id="comment-section">
  {{-- Heading --}}
  <div class="cmt-heading">
    <span class="cmt-heading-icon">💬</span>
    <h3>Bình luận <span class="cmt-count" id="cmtTotalCount">({{ $commentCount ?? 0 }})</span></h3>
  </div>

  {{-- Compose --}}
  <div class="cmt-compose">
    <form id="cmtMainForm">
      <div class="cmt-compose-fields">
        <div class="cmt-compose-field">
          <label>Tên hiển thị <span class="cmt-field-req">*</span></label>
          <input type="text" name="name" id="cmtName" placeholder="Nguyễn Văn A" required maxlength="100">
          <div class="cmt-field-error" id="cmt-err-name"></div>
        </div>
        <div class="cmt-compose-field">
          <label>Email <span class="cmt-field-req">*</span></label>
          <input type="email" name="email" id="cmtEmail" placeholder="example@email.com" required>
          <div class="cmt-field-error" id="cmt-err-email"></div>
        </div>
      </div>
      <textarea id="cmtTextarea" name="body" placeholder="Viết bình luận của bạn..." maxlength="1000" required></textarea>
      <div class="cmt-compose-footer">
        <span class="cmt-char-count"><span id="cmtCharCount">0</span>/1000</span>
        <button type="submit" class="cmt-submit-btn" id="cmtSubmitBtn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Gửi bình luận
        </button>
      </div>
    </form>
  </div>

  {{-- Comment list --}}
  <div class="cmt-list" id="cmtList">
    @forelse($comments as $comment)
      @php $gradient = $avatarGradients[$comment->id % count($avatarGradients)]; @endphp
      <div class="cmt-item" data-id="{{ $comment->id }}">
        <div class="cmt-row">
          <div class="cmt-avatar" style="background:{{ $gradient }}">{{ strtoupper(mb_substr($comment->name, 0, 1)) }}</div>
          <div class="cmt-body">
            <div class="cmt-meta">
              <span class="cmt-author">{{ $comment->name }}</span>
              <span class="cmt-time">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="cmt-text">{{ $comment->body }}</p>
            <div class="cmt-actions">
              <button type="button" class="cmt-reply-btn" onclick="cmtToggleReply(this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                Trả lời
              </button>
            </div>
          </div>
        </div>

        {{-- Replies --}}
        @if($comment->replies->count() > 0)
          <div class="cmt-replies">
            @foreach($comment->replies as $reply)
              <div class="cmt-item {{ $reply->is_admin ? 'is-admin' : '' }}">
                <div class="cmt-row">
                  @if($reply->is_admin)
                    <div class="cmt-avatar" style="background:linear-gradient(135deg,#2563eb,#4f7af8)">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#fff" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    </div>
                  @else
                    @php $rGradient = $avatarGradients[$reply->id % count($avatarGradients)]; @endphp
                    <div class="cmt-avatar" style="background:{{ $rGradient }}">{{ strtoupper(mb_substr($reply->name, 0, 1)) }}</div>
                  @endif
                  <div class="cmt-body">
                    <div class="cmt-meta">
                      <span class="cmt-author">{{ $reply->name }}</span>
                      @if($reply->is_admin)
                        <span class="cmt-admin-badge">
                          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                          ADMIN
                        </span>
                      @endif
                      <span class="cmt-time">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="cmt-text">{{ $reply->body }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Inline reply compose --}}
        <div class="cmt-reply-compose" data-parent="{{ $comment->id }}">
          <div class="cmt-reply-fields">
            <input type="text" name="reply_name" placeholder="Tên hiển thị *" required>
            <input type="email" name="reply_email" placeholder="Email *" required>
          </div>
          <textarea name="reply_body" placeholder="Trả lời {{ $comment->name }}..." maxlength="1000" required></textarea>
          <div class="cmt-reply-compose-footer">
            <button type="button" class="cmt-reply-cancel" onclick="cmtCloseReply(this)">Hủy</button>
            <button type="button" class="cmt-reply-send" onclick="cmtSendReply(this)">Gửi</button>
          </div>
        </div>
      </div>
    @empty
      <div class="cmt-empty" id="cmtEmpty">
        <div class="cmt-empty-icon">💭</div>
        <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
      </div>
    @endforelse
  </div>
</section>

@push('scripts')
<script>
(function(){
  var postId = {{ $post->id }};
  var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  // Toast
  var toast = document.createElement('div');
  toast.className = 'cmt-toast';
  document.body.appendChild(toast);

  function showToast(msg, type) {
    toast.className = 'cmt-toast ' + type;
    toast.textContent = msg;
    setTimeout(function(){ toast.classList.add('show'); }, 10);
    setTimeout(function(){ toast.classList.remove('show'); }, 4000);
  }

  // Character counter
  var ta = document.getElementById('cmtTextarea');
  var counter = document.getElementById('cmtCharCount');
  if (ta && counter) {
    ta.addEventListener('input', function() { counter.textContent = ta.value.length; });
  }

  // LocalStorage for Guest Info (1 hour expiry)
  var nameInput = document.getElementById('cmtName');
  var emailInput = document.getElementById('cmtEmail');

  function initGuestInfo() {
    var storedName = localStorage.getItem('giavang_cmt_name');
    var storedEmail = localStorage.getItem('giavang_cmt_email');
    var expiry = localStorage.getItem('giavang_cmt_expiry');
    var now = new Date().getTime();

    if (storedName && storedEmail && expiry) {
      if (now > parseInt(expiry)) {
        // Expired (older than 1 hour)
        localStorage.removeItem('giavang_cmt_name');
        localStorage.removeItem('giavang_cmt_email');
        localStorage.removeItem('giavang_cmt_expiry');
      } else {
        // Valid
        if (nameInput) nameInput.value = storedName;
        if (emailInput) emailInput.value = storedEmail;
      }
    }
  }
  function saveGuestInfo(name, email) {
    var now = new Date();
    // Save for 1 hour (3600000 ms)
    var expiry = now.getTime() + 3600000;
    localStorage.setItem('giavang_cmt_name', name);
    localStorage.setItem('giavang_cmt_email', email);
    localStorage.setItem('giavang_cmt_expiry', expiry.toString());
  }
  
  // Init on load
  initGuestInfo();

  // Avatar gradients (JS side)
  var gradients = [
    'linear-gradient(135deg,#6366f1,#4f46e5)',
    'linear-gradient(135deg,#22c97a,#059669)',
    'linear-gradient(135deg,#f59e0b,#d97706)',
    'linear-gradient(135deg,#ec4899,#be185d)',
    'linear-gradient(135deg,#14b8a6,#0d9488)',
    'linear-gradient(135deg,#ef4444,#dc2626)',
    'linear-gradient(135deg,#8b5cf6,#7c3aed)',
    'linear-gradient(135deg,#06b6d4,#0284c7)',
  ];

  function buildCommentHtml(c) {
    var grad = gradients[c.id % gradients.length];
    var initial = c.name.charAt(0).toUpperCase();
    return '<div class="cmt-item" data-id="' + c.id + '">' +
      '<div class="cmt-row">' +
        '<div class="cmt-avatar" style="background:' + grad + '">' + initial + '</div>' +
        '<div class="cmt-body">' +
          '<div class="cmt-meta">' +
            '<span class="cmt-author">' + escHtml(c.name) + '</span>' +
            '<span class="cmt-time">' + escHtml(c.created_at) + '</span>' +
          '</div>' +
          '<p class="cmt-text">' + escHtml(c.body) + '</p>' +
        '</div>' +
      '</div>' +
    '</div>';
  }

  function escHtml(s) {
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  function updateCount(delta) {
    var el = document.getElementById('cmtTotalCount');
    if (!el) return;
    var match = el.textContent.match(/\d+/);
    var n = match ? parseInt(match[0]) : 0;
    el.textContent = '(' + (n + delta) + ')';
  }

  // Main form submit
  var mainForm = document.getElementById('cmtMainForm');
  if (mainForm) {
    mainForm.addEventListener('submit', function(e) {
      e.preventDefault();
      // Clear errors
      document.querySelectorAll('.cmt-field-error').forEach(function(el){ el.textContent = ''; });

      var btn = document.getElementById('cmtSubmitBtn');
      var btnTxt = btn.innerHTML;
      btn.disabled = true;
      btn.textContent = 'Đang gửi...';

      var tName = document.getElementById('cmtName').value;
      var tEmail = document.getElementById('cmtEmail').value;

      fetch('/post/' + postId + '/comment', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: tName,
          email: tEmail,
          body: document.getElementById('cmtTextarea').value,
        })
      })
      .then(function(res) { return res.json().then(function(j){ return {ok:res.ok, status:res.status, data:j}; }); })
      .then(function(r) {
        if (r.ok && r.data.success) {
          showToast(r.data.message, 'success');
          saveGuestInfo(tName, tEmail); // Save info

          // Remove empty state
          var empty = document.getElementById('cmtEmpty');
          if (empty) empty.remove();
          // Prepend new comment
          var list = document.getElementById('cmtList');
          list.insertAdjacentHTML('afterbegin', buildCommentHtml(r.data.comment));
          updateCount(1);
          // Reset only textarea
          document.getElementById('cmtTextarea').value = '';
          counter.textContent = '0';
        } else if (r.status === 422) {
          var errs = r.data.errors;
          for (var f in errs) {
            var el = document.getElementById('cmt-err-' + f);
            if (el) el.textContent = errs[f][0];
          }
        } else {
          showToast(r.data.message || 'Đã có lỗi xảy ra', 'error');
        }
      })
      .catch(function(){ showToast('Đã có lỗi xảy ra, vui lòng thử lại', 'error'); })
      .finally(function(){ btn.disabled = false; btn.innerHTML = btnTxt; });
    });
  }

  // Toggle reply
  window.cmtToggleReply = function(btn) {
    var item = btn.closest('.cmt-item');
    // Walk up to top-level .cmt-item (direct child of .cmt-list)
    while (item.parentElement && !item.parentElement.classList.contains('cmt-list')) {
      if (item.parentElement.classList.contains('cmt-replies')) {
        item = item.parentElement.parentElement; // go to parent .cmt-item
      } else {
        break;
      }
    }
    var compose = item.querySelector('.cmt-reply-compose');
    if (!compose) return;
    document.querySelectorAll('.cmt-reply-compose.open').forEach(function(el){ if(el!==compose) el.classList.remove('open'); });
    compose.classList.toggle('open');
    if (compose.classList.contains('open')) {
      // Pre-fill local storage data
      var storedName = localStorage.getItem('giavang_cmt_name');
      var storedEmail = localStorage.getItem('giavang_cmt_email');
      var rName = compose.querySelector('input[name="reply_name"]');
      var rEmail = compose.querySelector('input[name="reply_email"]');
      if (storedName && !rName.value) rName.value = storedName;
      if (storedEmail && !rEmail.value) rEmail.value = storedEmail;

      compose.querySelector('textarea').focus();
    }
  };

  window.cmtCloseReply = function(btn) {
    var c = btn.closest('.cmt-reply-compose');
    if (c) c.classList.remove('open');
  };

  // Send reply
  window.cmtSendReply = function(btn) {
    var compose = btn.closest('.cmt-reply-compose');
    var parentId = compose.dataset.parent;
    var nameInput = compose.querySelector('input[name="reply_name"]');
    var emailInput = compose.querySelector('input[name="reply_email"]');
    var bodyTa = compose.querySelector('textarea');

    if (!nameInput.value.trim() || !emailInput.value.trim() || !bodyTa.value.trim()) {
      showToast('Vui lòng điền đầy đủ thông tin', 'error');
      return;
    }

    btn.disabled = true;
    btn.textContent = 'Đang gửi...';

    fetch('/post/' + postId + '/comment', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: nameInput.value,
        email: emailInput.value,
        body: bodyTa.value,
        parent_id: parentId,
      })
    })
    .then(function(res) { return res.json().then(function(j){ return {ok:res.ok, status:res.status, data:j}; }); })
    .then(function(r) {
      if (r.ok && r.data.success) {
        showToast(r.data.message, 'success');
        saveGuestInfo(nameInput.value, emailInput.value); // Save info
        // Append reply to the replies container
        var topItem = compose.closest('.cmt-item[data-id="' + parentId + '"]');
        var repliesContainer = topItem.querySelector('.cmt-replies');
        if (!repliesContainer) {
          repliesContainer = document.createElement('div');
          repliesContainer.className = 'cmt-replies';
          topItem.querySelector('.cmt-row').parentElement.appendChild(repliesContainer);
          // Move compose after replies
          topItem.appendChild(compose);
        }
        // Insert before compose
        repliesContainer.insertAdjacentHTML('beforeend', buildCommentHtml(r.data.comment));
        updateCount(1);
        bodyTa.value = '';
        compose.classList.remove('open');
      } else {
        showToast(r.data.message || 'Đã có lỗi xảy ra', 'error');
      }
    })
    .catch(function(){ showToast('Đã có lỗi xảy ra, vui lòng thử lại', 'error'); })
    .finally(function(){ btn.disabled = false; btn.textContent = 'Gửi'; });
  };
})();
</script>
@endpush
