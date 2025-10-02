@props([
    'title',
    'count',
    'icon' => 'fas fa-chart-bar',
    'iconClass' => 'icon-active',
    'route' => null,
    'color' => 'primary'
])

<div class="card">
    <i class="{{ $icon }} {{ $iconClass }}"></i>
    <div class="card-content">
        <h3>{{ $title }}</h3>
        <p>
            @if($route)
                <a href="{{ $route }}">{{ number_format($count) }}</a>
            @else
                {{ number_format($count) }}
            @endif
        </p>
    </div>
</div>
