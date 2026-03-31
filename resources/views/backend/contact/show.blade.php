@extends('backend.layout.main')

@push('style')
<style>
  .ct-detail-card {
    border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden;
  }
  .ct-detail-card .card-body { padding: 28px 32px; }

  .ct-detail-header {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;
  }
  .ct-detail-header h5 { font-weight: 700; font-size: 17px; color: #2d3748; margin: 0; }

  .ct-detail-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;
  }
  .ct-detail-item label {
    display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;
    color: #a0aec0; font-weight: 600; margin-bottom: 4px;
  }
  .ct-detail-item p {
    font-size: 15px; color: #2d3748; font-weight: 500; margin: 0;
    word-break: break-word;
  }

  .ct-msg-box {
    background: #f7f8fc; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px;
  }
  .ct-msg-box label {
    display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;
    color: #a0aec0; font-weight: 600; margin-bottom: 8px;
  }
  .ct-msg-box p {
    font-size: 14.5px; color: #4a5568; line-height: 1.7; margin: 0;
    white-space: pre-wrap; word-break: break-word;
  }

  .ct-detail-meta {
    display: flex; gap: 24px; align-items: center; padding-top: 16px;
    border-top: 1px solid #edf0f7;
  }
  .ct-detail-meta span { font-size: 13px; color: #a0aec0; }

  .badge-read-lg {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: #fff; padding: 4px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
  }
  .badge-unread-lg {
    background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
    color: #fff; padding: 4px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
  }

  .btn-back-list {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff; border: none; border-radius: 8px; padding: 7px 20px;
    font-size: 13px; font-weight: 600; transition: transform .15s, box-shadow .15s;
  }
  .btn-back-list:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(102,126,234,.35); }

  @media (max-width: 768px) {
    .ct-detail-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
  <div class="content-page">
    <div class="page-breadcrumb">
      <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
          <h4 class="page-title">Chi tiết liên hệ</h4>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card ct-detail-card">
            <div class="card-body">
              <div class="ct-detail-header">
                <h5>📧 Thông tin liên hệ #{{ $data->id }}</h5>
                <a href="{{ backendRoute('contact.index') }}" class="btn-back-list">
                  <i class="mdi mdi-arrow-left"></i> Quay lại
                </a>
              </div>

              <div class="ct-detail-grid">
                <div class="ct-detail-item">
                  <label>Họ và tên</label>
                  <p>{{ $data->name }}</p>
                </div>
                <div class="ct-detail-item">
                  <label>Email</label>
                  <p>{{ $data->email }}</p>
                </div>
                <div class="ct-detail-item">
                  <label>Số điện thoại</label>
                  <p>{{ $data->phone ?: '—' }}</p>
                </div>
                <div class="ct-detail-item">
                  <label>Trạng thái</label>
                  <p>
                    @if($data->isRead())
                      <span class="badge-read-lg">Đã đọc</span>
                    @else
                      <span class="badge-unread-lg">Chưa đọc</span>
                    @endif
                  </p>
                </div>
              </div>

              <div class="ct-msg-box">
                <label>Nội dung tin nhắn</label>
                <p>{{ $data->message }}</p>
              </div>

              <div class="ct-detail-meta">
                <span><i class="mdi mdi-clock-outline"></i> Gửi lúc: {{ $data->created_at->format('d/m/Y H:i:s') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
