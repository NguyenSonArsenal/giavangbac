@extends('backend.layout.main')

@section('content')
  <div class="content-page">
    <div class="page-breadcrumb">
      <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
          <h4 class="page-title">Bài viết</h4>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              @include('backend.layout.structures._notification')

              <div class="card-body__head d-flex">
                <h5 class="card-title">Danh sách bài viết</h5>
                <a href="{{ backendRoute('post.create') }}">
                  <button type="button" class="btn btn-cyan btn-sm">Thêm mới</button>
                </a>
              </div>

              <div id="zero_config_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <table class="table table-striped table-bordered dataTable" role="grid">
                  <thead>
                  <tr>
                    <th scope="col">STT</th>
                    <th scope="col">ID</th>
                    <th scope="col">Tiêu đề</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Thời gian tạo</th>
                    <th scope="col">Hành động</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($data as $key => $entity)
                    <tr>
                      <td>{{ getSTTBackend($data, $key) }}</td>
                      <td>{{ $entity->id }}</td>
                      <td>{{ $entity->title }}</td>
                      <td><small class="text-muted">{{ $entity->slug }}</small></td>
                      <td>{{ $entity->created_at->format('d/m/Y H:i') }}</td>
                      <td>
                        <div class="comment-footer d-flex">
                          <a href="{{ route('fe.post.show', $entity->slug) }}" target="_blank" title="Xem bài viết">
                            <button type="button" class="btn btn-info btn-xs"><i class="mdi mdi-eye"></i> Xem</button>
                          </a>
                          <a href="{{ backendRoute('post.edit', ['post' => $entity->id]) }}">
                            <button type="button" class="btn btn-cyan btn-xs">Sửa</button>
                          </a>
                          <form action="{{ backendRoute('post.destroy', ['post' => $entity->id]) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger btn btn-xs rounded"
                                    onclick="return confirm('Xoá bài viết này?')"
                            >
                              Xóa
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
