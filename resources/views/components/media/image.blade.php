@props([
    'src' => null,
    'alt' => 'Gambar',
    'variant' => 'default', // default, card, avatar, hero
    'class' => '',
    'width' => null,
    'height' => null
])

@php
$variantClasses = [
    'default' => 'object-cover',
    'card' => 'w-full h-48 object-cover',
    'avatar' => 'w-16 h-16 rounded-full object-cover',
    'hero' => 'w-full h-full object-cover'
];

$baseClass = $variantClasses[$variant] ?? $variantClasses['default'];
$finalClass = $baseClass . ($class ? ' ' . $class : '');

// Check if image exists
$imagePath = null;
$fallbackSrc = asset('assets/images/placeholder.svg');

if ($src) {
    // If src starts with storage/, it's from Laravel storage
    if (str_starts_with($src, 'storage/')) {
        $publicPath = public_path($src);
        if (file_exists($publicPath)) {
            $imagePath = asset($src);
        }
    }
    // If src starts with assets/, it's from assets folder
    elseif (str_starts_with($src, 'assets/')) {
        $publicPath = public_path($src);
        if (file_exists($publicPath)) {
            $imagePath = asset($src);
        }
    }
    // If src is a full URL or external path
    elseif (filter_var($src, FILTER_VALIDATE_URL)) {
        $imagePath = $src;
    }
    // Otherwise try to construct storage path
    else {
        $storagePath = 'storage/' . ltrim($src, '/');
        $publicPath = public_path($storagePath);
        if (file_exists($publicPath)) {
            $imagePath = asset($storagePath);
        }
    }
}

$finalSrc = $imagePath ?: $fallbackSrc;
@endphp

<img 
    src="{{ $finalSrc }}" 
    alt="{{ $alt }}"
    class="{{ $finalClass }}"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    {{ $attributes->except(['src', 'alt', 'variant', 'class', 'width', 'height']) }}
    onerror="this.src='{{ $fallbackSrc }}'"
    loading="lazy"
>