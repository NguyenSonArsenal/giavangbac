@if (session('notification_success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('notification_success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('notification_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('notification_error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
