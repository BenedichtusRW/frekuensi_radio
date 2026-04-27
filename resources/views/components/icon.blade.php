@php
    $iconPath = resource_path('icons/' . $icon . '.svg');
    $content = '';
    if (file_exists($iconPath)) {
        $content = file_get_contents($iconPath);
        
        // If it's a full SVG, we inject the desired width, height and class
        if (str_contains($content, '<svg')) {
            $width = $width ?? '24';
            $height = $height ?? '24';
            $class = $class ?? '';
            
            // Remove existing width/height if present to avoid conflicts
            $content = preg_replace('/width="[^"]*"/', '', $content);
            $content = preg_replace('/height="[^"]*"/', '', $content);
            
            // Inject new attributes
            $content = str_replace('<svg', "<svg width=\"$width\" height=\"$height\" class=\"$class\"", $content);
        }
    }
@endphp

@if ($content)
    @if (str_contains($content, '<svg'))
        {!! $content !!}
    @else
        <svg width="{{ $width ?? '24' }}" height="{{ $height ?? '24' }}" viewBox="{{ $viewBox ?? '0 0 512 512' }}" fill="none" stroke="{{ $stroke ?? 'currentColor' }}" stroke-width="{{ $strokeWidth ?? '1.5' }}" stroke-linecap="round" stroke-linejoin="round" class="{{ $class ?? '' }}" vector-effect="non-scaling-stroke">
            {!! $content !!}
        </svg>
    @endif
@endif
