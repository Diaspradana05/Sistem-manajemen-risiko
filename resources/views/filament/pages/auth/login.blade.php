<x-filament-panels::page>
    @section('form.after')
        <div class="w-full text-right mt-2 -mb-2">
            <a href="{{ route('password.request') }}" class="text-sm underline text-primary-600 hover:text-primary-700">
                Lupa Password?
            </a>
        </div>
    @endsection
</x-filament-panels::page>
