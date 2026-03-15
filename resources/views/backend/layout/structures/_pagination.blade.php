<div class="row" id="pagination">
    <div class="col-sm-12 col-md-5">
        <div class="dataTables_info" id="zero_config_info" role="status">
            @if(method_exists($paginator, 'total'))
                Hiển thị {{ number_format($paginator->firstItem()) }} tới {{ number_format($paginator->lastItem()) }} của {{ number_format($paginator->total()) }} bản ghi
            @else
                Hiển thị {{ number_format($paginator->firstItem()) }} tới {{ number_format($paginator->firstItem() + $paginator->count() - 1) }} bản ghi
            @endif
        </div>
    </div>
    <div class="col-sm-12 col-md-7">
        <div class="dataTables_paginate paging_simple_numbers float-right"
             id="zero_config_paginate">
            {{ $paginator->appends($_GET)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
