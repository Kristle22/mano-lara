<div class="row justify-content-center">
    <div class="col-md-9">
        @if (isset($successMsg))
            <div class="alert alert-success" role="alert">
                {{ $successMsg }}
            </div>
        @endif

        @if (isset($infoMsg))
            <div class="alert alert-info" role="alert">
                {{ $infoMsg }}
            </div>
        @endif
    </div>
</div>
