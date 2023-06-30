<div  class="col-md-{{ $size ?? 12 }} @if (isset($disabled)) d-none @endif {{ $xtrasclass ?? '' }} ">
    <div class="card @if(isset($titlecolor)) border-{{ $titlecolor }} @endif ">
        @if (isset($header))
            <h5 class="card-header text-bg-{{ $titlecolor ?? '' }}">
                {{ $header }}
            </h5>
        @endif
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>
