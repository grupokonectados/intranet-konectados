@if (isset($type))
    @if ($type === 'a')
        <a title="{{ $title ?? '' }}" @if (isset($href)) href="{{ $href }}" @endif
            @if (isset($onclick)) onclick="{{ $onclick }}" @endif
            @if (isset($id)) id="{{ $id }}" @endif
            class="btn btn-{{ $color ?? 'info' }} btn-{{ $sm ?? '' }} {{ $extraclass ?? '' }}">

            @if (isset($icon))
                <i class="fas fa-{{ $icon }}"></i>
            @endif
            {{ $name ?? '' }}
        </a>
    @elseif ($type === 'submit')
    <button type="submit" class="btn btn-{{ $color ?? 'info' }} btn-{{ $sm ?? '' }} {{ $extraclass ?? '' }}">
        @if (isset($icon))
            <i class="fas fa-{{ $icon }}"></i>
        @endif
        {{ $name ?? '' }}
    </button>
    @else
        <button @if (isset($onclick)) onclick="{{ $onclick }}" @endif class="btn btn-{{ $color ?? 'info' }} btn-{{ $sm ?? '' }} {{ $extraclass ?? '' }}">
            @if (isset($icon))
                <i class="fas fa-{{ $icon }}"></i>
            @endif
            {{ $name }}
        </button>
    @endif
@else
    <button @if (isset($onclick)) onclick="{{ $onclick }}" @endif class="btn btn-{{ $color ?? 'info' }} btn-{{ $sm ?? '' }} {{ $extraclass ?? '' }}">
        @if (isset($icon))
            <i class="fas fa-{{ $icon }}"></i>
        @endif
        {{ $name }}
    </button>
@endif


