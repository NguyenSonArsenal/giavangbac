@switch(true)
    @case(session()->has('notification_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('notification_success') }}</strong>
        </div>
    @break

    @case(session()->has('notification_warning'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('notification_warning') }}</strong>
        </div>
    @break

    @case(session()->has('notification_error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('notification_error') }}</strong>
        </div>
    @break

    @case(session()->has('notification_warning_login_multi_device'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('notification_warning_login_multi_device') }}</strong>
        </div>
    @break
@endswitch
