@if ($errors->any())
<div class="text-end">
    @foreach ($errors->all(':message') as $error)
        <flux:error message="{{ $error }}" class="text-red-500 text-sm" />
    @endforeach
</div>
@endif