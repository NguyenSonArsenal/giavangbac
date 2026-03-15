@extends('backend.layout.main')

@push('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

  <script>
    window.TINYMCE_UPLOAD_URL = "{{ backendRoute('tinymce_editor.upload') }}";
    window.TINYMCE_CSRF = "{{ csrf_token() }}";
    window.EDITOR_CONTENT_CSS = "{{ asset('frontend/css/tinymce_editor.css') }}";
  </script>

  <script src="{{ asset('backend/js/tinymce_editor.js') }}"></script>
@endpush

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

              <div class="card-body__head d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Cập nhật bài viết</h5>
                <a href="{{ backendRoute('post.index') }}">
                  <button type="button" class="btn btn-cyan btn-sm">Quay lại</button>
                </a>
              </div>

              <form class="form-horizontal store-update-entity" action="{{ backendRoute('post.update', ['post' => $data->id]) }}" method="post">
                @csrf
                @method('PUT')
                @include('backend.layout.structures._error_validate')

                {{-- Title --}}
                <div class="form-group row">
                  <label class="col-md-2 text-right control-label col-form-label">
                    Tiêu đề <span class="text-danger">(*)</span>
                  </label>
                  <div class="col-md-10">
                    <input type="text"
                           class="form-control"
                           name="title"
                           required
                           value="{{ oldInput(old('title'), $data->title) }}"
                           placeholder="Nhập tiêu đề bài viết">
                  </div>
                </div>

                {{-- Description --}}
                <div class="form-group row">
                  <label class="col-md-2 text-right control-label col-form-label">Mô tả ngắn</label>
                  <div class="col-md-10">
                  <textarea id="des"
                            name="des"
                            class="form-control"
                            rows="3"
                            placeholder="Tóm tắt ngắn hiển thị ở danh sách">{{ oldInput(old('des'), $data->des) }}</textarea>
                  </div>
                </div>

                {{-- Content --}}
                <div class="form-group row">
                  <label class="col-md-2 text-right control-label col-form-label">
                    Nội dung <span class="text-danger">(*)</span>
                  </label>
                  <div class="col-md-10">
                    <div class="news-content">
                      <textarea id="content" name="content" class="form-control" rows="18">{{ oldInput(old('content'), $data->content) }}</textarea>
                      <small id="contentError" class="text-danger d-none">Vui lòng nhập nội dung.</small>
                    </div>
                  </div>
                </div>

                <div class="border-top">
                  <div class="card-body">
                    <button type="submit" class="btn btn-success">Lưu</button>
                    <a href="{{ backendRoute('post.index') }}" class="btn btn-secondary ml-2">Hủy</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
