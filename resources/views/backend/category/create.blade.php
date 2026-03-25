@extends('backend.layout.main')

@push('style')
<style>
  .cat-form-card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    overflow: hidden;
  }
  .cat-form-card .card-body { padding: 28px; }
  .cat-form-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f2f5;
  }
  .cat-form-header h5 {
    font-weight: 700; font-size: 17px; color: #2d3748; margin: 0;
  }
  .cat-form-header .btn-back {
    background: #edf2f7; color: #4a5568; border: none; border-radius: 8px;
    padding: 7px 18px; font-size: 13px; font-weight: 600;
    transition: all .2s;
  }
  .cat-form-header .btn-back:hover { background: #e2e8f0; }

  .cat-section {
    background: linear-gradient(135deg, #f8f9ff 0%, #eef1f8 100%);
    border: 1px solid #e2e6f0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
  }
  .cat-section-title {
    font-weight: 700; font-size: 13px; color: #667eea;
    text-transform: uppercase; letter-spacing: 0.8px;
    margin-bottom: 16px; display: flex; align-items: center; gap: 6px;
  }

  .cat-form .form-group { margin-bottom: 18px; }
  .cat-form label.col-form-label {
    font-weight: 600; font-size: 13px; color: #4a5568;
  }
  .cat-form .form-control {
    border-radius: 8px; border: 1px solid #d1d9e6;
    font-size: 13.5px; padding: 8px 14px;
    transition: border-color .2s, box-shadow .2s;
  }
  .cat-form .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102,126,234,.15);
  }
  .cat-form .form-control[readonly] {
    background: #f7fafc; opacity: 0.7; cursor: not-allowed;
  }

  .cat-form .btn-save {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px 28px; font-weight: 600; font-size: 14px;
    transition: transform .15s, box-shadow .15s;
  }
  .cat-form .btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(102,126,234,.35); color: #fff; }
  .cat-form .btn-cancel {
    background: #edf2f7; color: #4a5568; border: none; border-radius: 8px;
    padding: 9px 24px; font-weight: 600; font-size: 14px;
    transition: all .2s;
  }
  .cat-form .btn-cancel:hover { background: #e2e8f0; color: #2d3748; }

  .thumb-preview-box {
    margin-top: 10px;
    display: inline-block;
    position: relative;
  }
  .thumb-preview-box img {
    max-width: 220px; border-radius: 10px;
    border: 2px solid #e2e6f0;
  }
  .thumb-remove {
    position: absolute; top: -8px; right: -8px;
    width: 24px; height: 24px;
    background: #e53e3e; color: #fff;
    border: 2px solid #fff; border-radius: 50%;
    font-size: 14px; line-height: 20px; text-align: center;
    cursor: pointer; display: none;
    box-shadow: 0 2px 6px rgba(0,0,0,.2);
    transition: transform .15s;
  }
  .thumb-remove:hover { transform: scale(1.15); }

  .char-counter { font-size: 12px; margin-top: 4px; }
</style>
@endpush

@section('content')
  <div class="content-page">
    <div class="page-breadcrumb">
      <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
          <h4 class="page-title">Danh mục</h4>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card cat-form-card">
            <div class="card-body">
              @include('backend.layout.structures._notification')

              <div class="cat-form-header">
                <h5>✨ Thêm danh mục mới</h5>
                <a href="{{ backendRoute('category.index') }}" class="btn btn-back">
                  <i class="mdi mdi-arrow-left"></i> Quay lại
                </a>
              </div>

              <form class="cat-form" action="{{ backendRoute('category.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @include('backend.layout.structures._error_validate')

                {{-- Thông tin chính --}}
                <div class="cat-section">
                  <div class="cat-section-title"><i class="mdi mdi-information-outline"></i> Thông tin chính</div>
                  <div style="max-width: 50%;">
                    <div class="form-group">
                      <label class="col-form-label">Tên danh mục <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="name" id="name" required
                             value="{{ old('name') }}" placeholder="Nhập tên danh mục">
                    </div>

                    <div class="form-group">
                      <label class="col-form-label">Slug (URL SEO)</label>
                      <input type="text" class="form-control" name="slug" id="slug" readonly
                             value="{{ old('slug') }}" placeholder="Tự tạo từ tên danh mục">
                    </div>

                    <div class="form-group">
                      <label class="col-form-label">Mô tả</label>
                      <textarea name="description" class="form-control" rows="3"
                                placeholder="Mô tả hiển thị trang category">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                      <label class="col-form-label">Trạng thái</label>
                      <select name="status" class="form-control" style="max-width: 200px;">
                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="-1" {{ old('status') == -1 ? 'selected' : '' }}>Inactive</option>
                      </select>
                    </div>
                  </div>
                </div>

                {{-- SEO + Ảnh cạnh nhau --}}
                <div class="row">
                  <div class="col-md-6">
                    <div class="cat-section">
                      <div class="cat-section-title"><i class="mdi mdi-google"></i> Cấu hình SEO</div>

                      <div class="form-group">
                        <label class="col-form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" id="meta_title" maxlength="65"
                               value="{{ old('meta_title') }}"
                               placeholder="Nhập title Google (khuyến nghị 50–60 ký tự, tối đa 65)">
                        <small id="meta_title_counter" class="char-counter text-muted">0 / 65 ký tự</small>
                      </div>

                      <div class="form-group">
                        <label class="col-form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" id="meta_description" maxlength="170" rows="2"
                                  placeholder="Nhập description Google (khuyến nghị 140–160 ký tự, tối đa 170)">{{ old('meta_description') }}</textarea>
                        <small id="meta_description_counter" class="char-counter text-muted">0 / 170 ký tự</small>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="cat-section">
                      <div class="cat-section-title"><i class="mdi mdi-image"></i> Ảnh banner</div>

                      <div class="thumb-preview-box" style="margin-bottom: 10px;">
                        <span class="thumb-remove" id="thumb_remove" title="Xóa ảnh">&times;</span>
                        <img id="thumbnail_preview" src="" alt="Preview" style="display:none;">
                      </div>

                      <div class="form-group">
                        <input type="file" class="form-control-file" name="thumbnail" id="thumbnail_input" accept="image/*">
                        <small class="text-muted">Chấp nhận JPG, PNG, tối đa 2MB</small>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex align-items-center mt-3" style="gap: 10px;">
                  <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save"></i> Lưu danh mục</button>
                  <a href="{{ backendRoute('category.index') }}" class="btn btn-cancel">Hủy</a>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop

@push('script')
<script>
  // Auto slug
  document.getElementById('name').addEventListener('input', function () {
    var slug = this.value
      .toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .replace(/đ/g, 'd').replace(/Đ/g, 'd')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/[\s]+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
    document.getElementById('slug').value = slug;
  });

  // Char counter
  function setupCharCounter(inputId, counterId, max) {
    var input = document.getElementById(inputId);
    var counter = document.getElementById(counterId);
    function update() {
      var len = input.value.length;
      counter.textContent = len + ' / ' + max + ' ký tự';
      if (len > max) {
        input.classList.add('is-invalid');
        counter.classList.remove('text-muted');
        counter.classList.add('text-danger');
      } else {
        input.classList.remove('is-invalid');
        counter.classList.remove('text-danger');
        counter.classList.add('text-muted');
      }
    }
    input.addEventListener('input', update);
    update();
  }
  setupCharCounter('meta_title', 'meta_title_counter', 65);
  setupCharCounter('meta_description', 'meta_description_counter', 170);

  // Thumb preview
  var thumbInput = document.getElementById('thumbnail_input');
  var thumbPreview = document.getElementById('thumbnail_preview');
  var thumbRemove = document.getElementById('thumb_remove');

  thumbInput.addEventListener('change', function (e) {
    if (e.target.files && e.target.files[0]) {
      var reader = new FileReader();
      reader.onload = function (ev) {
        thumbPreview.src = ev.target.result;
        thumbPreview.style.display = 'block';
        thumbRemove.style.display = 'block';
      };
      reader.readAsDataURL(e.target.files[0]);
    } else {
      thumbPreview.src = '';
      thumbPreview.style.display = 'none';
      thumbRemove.style.display = 'none';
    }
  });

  thumbRemove.addEventListener('click', function () {
    thumbInput.value = '';
    thumbPreview.src = '';
    thumbPreview.style.display = 'none';
    thumbRemove.style.display = 'none';
  });
</script>
@endpush
