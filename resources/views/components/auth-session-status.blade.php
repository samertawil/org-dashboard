@props([
    'status',
])

{{-- @if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif --}}



@if (session()->has('message'))
    @if (session('type') == 'error')
       
            <div {{ $attributes->merge(['class' => 'font-medium text-sm text-red-600']) }}>
                {{ $status }}
        </div>
    @elseif(session('type') == 'warning')
      
            <div {{ $attributes->merge(['class' => 'font-medium text-sm text-yellow-500']) }}>
                {{ $status }}
        </div>
    @else
         
            <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-500']) }}>
                {{ $status }}
        </div>
    @endif
@endif



     {{-- Success Message --}}
    {{-- @if (session()->has('message'))
        @if(session('type') == 'error')
            <div class="text-center font-medium text-sm text-red-500">
                {{ session('message') }}
            </div>
        @elseif(session('type') == 'warning')
            <div class="text-center font-medium text-sm text-yellow-500">
                {{ session('message') }}
            </div>
        @else
            <div class="text-center font-medium text-sm text-green-500">
                {{ session('message') }}
            </div>
        @endif
    @endif --}}