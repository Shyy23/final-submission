@props(['type' => 'info', 'colors' => ''])

@php
$message = null;
if (session('success')) {
$type = 'success';
$message = session('success');
$colors = 'bg-emerald-50 text-emerald-800 border-emerald-500';
$icon = 'fa-circle-check';
} elseif (session('warning')) {
$type = 'warning';
$message = session('warning');
$colors = 'bg-amber-50 text-amber-800 border-amber-500';
$icon = 'fa-triangle-exclamation';
} elseif (session('error') || session('danger')) {
$type = 'error';
$message = session('error') ?? session('danger');
$colors = 'bg-red-50 text-red-800 border-red-500';
$icon = 'fa-circle-exclamation';
} elseif (session('info')) {
$type = 'info';
$message = session('info');
$colors = 'bg-blue-50 text-blue-800 border-blue-500';
$icon = 'fa-circle-info';
} else {
$icon = 'fa-circle-info'; // Default icon
}
@endphp

@if ($message)
<div {{ $attributes->merge(['class' => "border-l-4 p-4 my-4 rounded-r-lg $colors shadow-sm"]) }} role="alert">
    <div class="flex items-start">
        <div class="flex-shrink-0 mt-0.5">
            <i class="fa-solid {{ $icon }} text-xl text-inherit"></i>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-medium leading-relaxed">{{ $message }}</p>
        </div>
    </div>
</div>
@endif