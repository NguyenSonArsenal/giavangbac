@extends('backend.layout.main')

@push('style')
<style>
  .ct-filter-bar {
    background: linear-gradient(135deg, #f8f9ff 0%, #eef1f8 100%);
    border: 1px solid #e2e6f0;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .ct-filter-bar label {
    font-weight: 600; color: #4a5568; margin: 0; font-size: 13px; letter-spacing: 0.3px;
  }
  .ct-filter-bar select {
    border-radius: 8px; border: 1px solid #d1d9e6; padding: 6px 32px 6px 12px;
    font-size: 13px; background-color: #fff; width: auto; transition: border-color .2s;
  }
  .ct-filter-bar select:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.15); }
  .ct-filter-bar .btn { font-size: 13px; border-radius: 8px; padding: 6px 16px; }

  .ct-page-header {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;
  }
  .ct-page-header h5 { font-weight: 700; font-size: 17px; color: #2d3748; margin: 0; }

  .ct-card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; }
  .ct-card .card-body { padding: 24px; }

  .ct-table { border-collapse: separate; border-spacing: 0; }
  .ct-table thead th {
    background: #f7f8fc; color: #4a5568; font-weight: 600; font-size: 12.5px;
    text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 14px;
    border: none; border-bottom: 2px solid #e2e6f0; white-space: nowrap;
  }
  .ct-table tbody td {
    padding: 12px 14px; vertical-align: middle; font-size: 13.5px;
    border-bottom: 1px solid #f0f2f5; color: #4a5568;
  }
  .ct-table tbody tr { transition: background .15s; }
  .ct-table tbody tr:hover { background: #f7f8fc; }
  .ct-table tbody tr.unread { background: #fff8ed; }
  .ct-table tbody tr.unread:hover { background: #fff3db; }

  .badge-read {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: #fff; padding: 4px 12px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600; letter-spacing: 0.3px;
  }
  .badge-unread {
    background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
    color: #fff; padding: 4px 12px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600; letter-spacing: 0.3px;
  }

  .ct-actions { display: flex; gap: 6px; }
  .ct-actions .btn {
    border-radius: 7px; font-size: 12px; padding: 5px 12px;
    font-weight: 500; border: none; transition: transform .15s, box-shadow .15s;
  }
  .ct-actions .btn:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.12); }
  .btn-view { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
  .btn-view:hover { color: #fff; }
  .btn-del { background: linear-gradient(135deg, #fc8181 0%, #e53e3e 100%); color: #fff; }
  .btn-del:hover { color: #fff; }
  .btn-toggle { background: linear-gradient(135deg, #f6ad55, #ed8936); color: #fff; }
  .btn-toggle:hover { color: #fff; }

  .ct-msg-preview {
    max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #718096; font-size: 13px;
  }
</style>
@endpush

@section('content')
  <div class="content-page">
    <div class="page-breadcrumb">
      <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
          <h4 class="page-title">Liên hệ</h4>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card ct-card">
            <div class="card-body">
              @include('backend.layout.structures._notification')

              <div class="ct-page-header">
                <h5>📬 Danh sách liên hệ</h5>
              </div>

              {{-- Bộ lọc --}}
              <form method="GET" action="{{ backendRoute('contact.index') }}" class="ct-filter-bar">
                <label><i class="mdi mdi-filter-outline"></i> Trạng thái:</label>
                <select name="status" class="form-control form-control-sm">
                  <option value="">Tất cả</option>
                  <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đã đọc</option>
                  <option value="-1" {{ request('status') === '-1' ? 'selected' : '' }}>Chưa đọc</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="mdi mdi-magnify"></i> Lọc</button>
                <a href="{{ backendRoute('contact.index') }}" class="btn btn-outline-secondary btn-sm"><i class="mdi mdi-refresh"></i> Reset</a>
              </form>

              <div class="dataTables_wrapper dt-bootstrap4">
                <table class="table ct-table" role="grid">
                  <thead>
                  <tr>
                    <th>STT</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Tin nhắn</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($data as $key => $entity)
                    <tr class="{{ $entity->isUnread() ? 'unread' : '' }}">
                      <td>{{ getSTTBackend($data, $key) }}</td>
                      <td><strong>{{ $entity->name }}</strong></td>
                      <td>{{ $entity->email }}</td>
                      <td>{{ $entity->phone ?: '—' }}</td>
                      <td><div class="ct-msg-preview">{{ $entity->message }}</div></td>
                      <td>
                        @if($entity->isRead())
                          <span class="badge-read">Đã đọc</span>
                        @else
                          <span class="badge-unread">Chưa đọc</span>
                        @endif
                      </td>
                      <td>{{ $entity->created_at->format('d/m/Y H:i') }}</td>
                      <td>
                        <div class="ct-actions">
                          <a href="{{ backendRoute('contact.show', ['id' => $entity->id]) }}">
                            <button type="button" class="btn btn-view"><i class="mdi mdi-eye"></i> Xem</button>
                          </a>
                          <form action="{{ backendRoute('contact.toggle-read', ['id' => $entity->id]) }}" method="post" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-toggle" title="Toggle đọc/chưa đọc">
                              <i class="mdi {{ $entity->isRead() ? 'mdi-email-outline' : 'mdi-email-open-outline' }}"></i>
                            </button>
                          </form>
                          <form action="{{ backendRoute('contact.destroy', ['id' => $entity->id]) }}" method="post" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-del" onclick="return confirm('Xoá liên hệ này?')">
                              <i class="mdi mdi-delete"></i> Xóa
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
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
