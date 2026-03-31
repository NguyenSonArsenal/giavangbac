@extends('backend.layout.main')

@push('style')
<style>
  .cmt-filter-bar {
    background: linear-gradient(135deg, #f8f9ff 0%, #eef1f8 100%);
    border: 1px solid #e2e6f0; border-radius: 10px;
    padding: 16px 20px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .cmt-filter-bar label { font-weight: 600; color: #4a5568; margin: 0; font-size: 13px; }
  .cmt-filter-bar select {
    border-radius: 8px; border: 1px solid #d1d9e6; padding: 6px 32px 6px 12px;
    font-size: 13px; background-color: #fff; width: auto;
  }
  .cmt-filter-bar select:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.15); }
  .cmt-filter-bar .btn { font-size: 13px; border-radius: 8px; padding: 6px 16px; }

  .cmt-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
  .cmt-page-header h5 { font-weight: 700; font-size: 17px; color: #2d3748; margin: 0; }

  .cmt-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; }
  .cmt-card .card-body { padding: 24px; }

  .cmt-table { border-collapse: separate; border-spacing: 0; }
  .cmt-table thead th {
    background: #f7f8fc; color: #4a5568; font-weight: 600; font-size: 12.5px;
    text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 14px;
    border: none; border-bottom: 2px solid #e2e6f0; white-space: nowrap;
  }
  .cmt-table tbody td {
    padding: 12px 14px; vertical-align: middle; font-size: 13.5px;
    border-bottom: 1px solid #f0f2f5; color: #4a5568;
  }
  .cmt-table tbody tr { transition: background .15s; }
  .cmt-table tbody tr:hover { background: #f7f8fc; }

  .cmt-msg-preview {
    max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #718096; font-size: 13px;
  }
  .cmt-post-link {
    color: #667eea; text-decoration: none; font-weight: 600; font-size: 13px;
    max-width: 180px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .cmt-post-link:hover { text-decoration: underline; color: #5a67d8; }

  .badge-admin {
    background: linear-gradient(135deg,#667eea,#764ba2); color: #fff;
    padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;
  }
  .badge-user {
    background: #edf2f7; color: #4a5568;
    padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;
  }
  .cmt-reply-count {
    font-size: 11.5px; color: #667eea; font-weight: 600;
    cursor: pointer; text-decoration: underline; text-decoration-style: dotted;
  }
  .cmt-reply-count:hover { color: #5a67d8; }

  .cmt-actions { display: flex; gap: 6px; }
  .cmt-actions .btn {
    border-radius: 7px; font-size: 12px; padding: 5px 12px;
    font-weight: 500; border: none; transition: transform .15s, box-shadow .15s;
  }
  .cmt-actions .btn:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.12); }
  .btn-reply-admin { background: linear-gradient(135deg,#667eea,#764ba2); color: #fff; }
  .btn-reply-admin:hover { color: #fff; }
  .btn-cmt-del { background: linear-gradient(135deg,#fc8181,#e53e3e); color: #fff; }
  .btn-cmt-del:hover { color: #fff; }

  /* Reply sub-row */
  .cmt-replies-row td { padding: 0 14px 14px 14px !important; background: #fafbff; border-bottom: 2px solid #e2e6f0; }
  .cmt-replies-row.hidden { display: none; }
  .cmt-replies-list { padding: 12px 16px; }
  .cmt-reply-item {
    display: flex; gap: 12px; align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px solid #edf2f7;
  }
  .cmt-reply-item:last-child { border-bottom: none; }
  .cmt-reply-avatar {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: #fff;
  }
  .cmt-reply-avatar.admin-av { background: linear-gradient(135deg,#667eea,#764ba2); }
  .cmt-reply-avatar.user-av { background: linear-gradient(135deg,#48bb78,#38a169); }
  .cmt-reply-body { flex: 1; min-width: 0; }
  .cmt-reply-meta {
    display: flex; align-items: center; gap: 6px; margin-bottom: 3px;
  }
  .cmt-reply-author { font-size: 13px; font-weight: 700; color: #2d3748; }
  .cmt-reply-time { font-size: 11.5px; color: #a0aec0; }
  .cmt-reply-text { font-size: 13px; color: #4a5568; line-height: 1.5; margin: 0; }
  .cmt-reply-text.admin-text { color: #667eea; }

  /* Reply form */
  .reply-form {
    background: #f7f8fc; border: 1px solid #e2e6f0; border-radius: 10px;
    padding: 14px; margin-top: 8px; display: none;
  }
  .reply-form.open { display: block; }
  .reply-form textarea {
    width: 100%; border: 1px solid #d1d9e6; border-radius: 8px;
    padding: 10px 14px; font-size: 13px; min-height: 70px; resize: vertical;
  }
  .reply-form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px; }

  /* Sub-reply delete button */
  .cmt-reply-del-btn {
    background-color: #fee2e2;
    color: #ef4444;
    border: 1px solid #fca5a5;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.2s;
  }
  .cmt-reply-del-btn:hover {
    background-color: #fef2f2;
    color: #dc2626;
    border-color: #ef4444;
  }
</style>
@endpush

@section('content')
  <div class="content-page">
    <div class="page-breadcrumb">
      <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
          <h4 class="page-title">Bình luận</h4>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card cmt-card">
            <div class="card-body">
              @include('backend.layout.structures._notification')

              <div class="cmt-page-header">
                <h5>💬 Quản lý bình luận</h5>
              </div>

              <div class="dataTables_wrapper dt-bootstrap4">
                <table class="table cmt-table" role="grid">
                  <thead>
                  <tr>
                    <th>STT</th>
                    <th>Bài viết</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Nội dung</th>
                    <th>Replies</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($data as $key => $entity)
                    <tr id="cmt-row-{{ $entity->id }}">
                      <td>{{ getSTTBackend($data, $key) }}</td>
                      <td>
                        @if($entity->post)
                          <a class="cmt-post-link" href="{{ url('/post/' . $entity->post->slug) }}" target="_blank" title="{{ $entity->post->title }}">
                            {{ $entity->post->title }}
                          </a>
                        @else
                          <span style="color:#a0aec0">—</span>
                        @endif
                      </td>
                      <td>
                        <strong>{{ $entity->name }}</strong>
                        @if($entity->is_admin) <span class="badge-admin">Admin</span> @endif
                      </td>
                      <td>{{ $entity->email }}</td>
                      <td><div class="cmt-msg-preview">{{ $entity->body }}</div></td>
                      <td>
                        @php $replies = $entity->replies; $replyCount = $replies->count(); @endphp
                        @if($replyCount > 0)
                          <span class="cmt-reply-count" id="reply-count-{{ $entity->id }}" onclick="toggleReplies('replies-{{ $entity->id }}')">{{ $replyCount }} trả lời ▾</span>
                        @else
                          <span class="cmt-reply-count" id="reply-count-{{ $entity->id }}" style="display:none;" onclick="toggleReplies('replies-{{ $entity->id }}')">0 trả lời ▾</span>
                          <span id="reply-empty-{{ $entity->id }}" style="color:#a0aec0;font-size:12px">0</span>
                        @endif
                      </td>
                      <td style="white-space:nowrap">{{ $entity->created_at->format('d/m/Y H:i') }}</td>
                      <td>
                        <div class="cmt-actions">
                          <button type="button" class="btn btn-reply-admin" onclick="toggleReplyForm(this)">
                            <i class="mdi mdi-reply"></i> Reply
                          </button>
                          <button type="button" class="btn btn-cmt-del" onclick="deleteComment(this, {{ $entity->id }}, false)">
                            <i class="mdi mdi-delete"></i> Xóa
                          </button>
                        </div>

                        {{-- Reply form (inline) --}}
                        <div class="reply-form">
                          <form action="{{ backendRoute('comment.reply', ['id' => $entity->id]) }}" method="post">
                            @csrf
                            <textarea name="body" placeholder="Nhập nội dung trả lời..." required></textarea>
                            <div class="reply-form-actions">
                              <button type="button" class="btn btn-sm btn-secondary" onclick="toggleReplyForm(this)">Hủy</button>
                              <button type="submit" class="btn btn-sm btn-primary">Gửi trả lời</button>
                            </div>
                          </form>
                        </div>
                      </td>
                    </tr>
                    {{-- Reply sub-row --}}
                    @if($replyCount > 0)
                    <tr class="cmt-replies-row hidden" id="replies-{{ $entity->id }}">
                      <td colspan="8">
                        <div class="cmt-replies-list">
                          @foreach($replies as $reply)
                            <div class="cmt-reply-item" id="reply-item-{{ $reply->id }}">
                              <div class="cmt-reply-avatar {{ $reply->is_admin ? 'admin-av' : 'user-av' }}">
                                {{ strtoupper(mb_substr($reply->name, 0, 1)) }}
                              </div>
                              <div class="cmt-reply-body">
                                <div class="cmt-reply-meta">
                                  <span class="cmt-reply-author">{{ $reply->name }}</span>
                                  @if($reply->is_admin) <span class="badge-admin">Admin</span> @else <span style="font-size:12px;color:#718096">&lt;{{ $reply->email }}&gt;</span> @endif
                                  <span class="cmt-reply-time">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="cmt-reply-text {{ $reply->is_admin ? 'admin-text' : '' }}">{{ $reply->body }}</p>
                              </div>
                              <button type="button" class="cmt-reply-del-btn" onclick="deleteComment(this, {{ $reply->id }}, true, {{ $entity->id }})">Xóa</button>
                            </div>
                          @endforeach
                        </div>
                      </td>
                    </tr>
                    @endif
                  @endforeach
                  </tbody>
                </table>

                {{ $data->appends(request()->all())->links('backend.layout.structures._pagination')}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop

@push('script')
<script>
  function toggleReplyForm(btn) {
    var row = btn.closest('td');
    var form = row.querySelector('.reply-form');
    if (form) {
      form.classList.toggle('open');
      if (form.classList.contains('open')) {
        form.querySelector('textarea').focus();
      }
    }
  }
  function toggleReplies(id) {
    var el = document.getElementById(id);
    if (el) el.classList.toggle('hidden');
  }

  function deleteComment(btn, id, isReply, parentId = null) {
    if (!confirm(isReply ? 'Xoá trả lời này?' : 'Xoá bình luận này và các trả lời gốc?')) return;
    
    var url = "{{ backendRoute('comment.destroy', ['id' => '__ID__']) }}".replace('__ID__', id);

    var originalText = btn.innerHTML;
    btn.innerHTML = '...';
    btn.disabled = true;

    fetch(url, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        if (!isReply) {
          // Xoá dòng cha báo gồm cả replies row nếu có
          var row = document.getElementById('cmt-row-' + id);
          if (row) row.remove();
          var repliesRow = document.getElementById('replies-' + id);
          if (repliesRow) repliesRow.remove();
        } else {
          // Xóa dòng reply con
          var replyItem = document.getElementById('reply-item-' + id);
          if (replyItem) replyItem.remove();
          
          // Giảm số lượng đếm reply
          if (parentId) {
            var countEl = document.getElementById('reply-count-' + parentId);
            if (countEl) {
              var matches = countEl.innerText.match(/(\d+)/);
              if (matches && matches[1]) {
                var current = parseInt(matches[1]);
                if (current > 1) {
                  countEl.innerText = (current - 1) + ' trả lời ▾';
                } else {
                  // Đã xóa hết reply, ẩn số lượng click đi, hiện "0"
                  countEl.style.display = 'none';
                  var emptyEl = document.getElementById('reply-empty-' + parentId);
                  if (emptyEl) emptyEl.style.display = 'inline';
                  var repliesRow = document.getElementById('replies-' + parentId);
                  if (repliesRow) repliesRow.classList.add('hidden');
                }
              }
            }
          }
        }
        // Hiện thông báo (reuse existing notification logic or simple alert)
        // Toast message here if available, otherwise just remove from DOM
      } else {
        alert(data.message || 'Lỗi xóa bình luận');
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    })
    .catch(e => {
      alert('Đã có lỗi xảy ra');
      btn.innerHTML = originalText;
      btn.disabled = false;
    });
  }
</script>
@endpush
