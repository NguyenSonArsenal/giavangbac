@extends('backend.layout.auth')

@section('content')
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark">
        <div class="auth-box bg-dark border-top border-secondary">
            <div id="loginform">
                <div class="text-center pt-3 pb-3">
                    <h1><strong class="db" style="color: #f5c518">GiáVàng<small style="color:#b0bec5">.vn</small></strong></h1>
                    <p class="text-muted mt-2">Đăng nhập quản trị</p>
                </div>

                <form class="form-horizontal mt-3" action="{{ backendRoute('auth.login.post') }}" method="POST">
                    @csrf

                    @include('backend.layout.structures._notification')
                    @include('backend.layout.structures._error_validate')

                    <div class="row pb-4">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-success text-white h-100"><i class="ti-user"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control form-control-lg"
                                       placeholder="Nhập địa chỉ email" required value="{{ old('email') }}">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-warning text-white h-100"><i class="ti-pencil"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control form-control-lg"
                                       placeholder="Nhập mật khẩu" required>
                            </div>
                        </div>
                    </div>
                    <div class="row border-top border-secondary">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="pt-3">
                                    <button class="btn btn-success float-end text-white" type="submit">Đăng nhập</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
