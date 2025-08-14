@php
$badgeMap = [
    'lab_facilities' => 'bg-blue-100 text-blue-800',
    'equipment' => 'bg-green-100 text-green-800',
    'activities' => 'bg-purple-100 text-purple-800',
    'events' => 'bg-orange-100 text-orange-800',
];
$badgeClass = $badgeMap[$category] ?? 'bg-gray-100 text-gray-800';
@endphp

<span {{ $attributes->merge(['class' => "rounded-full font-semibold capitalize {$badgeClass}"]) }}>
    {{ $label }}
</span>