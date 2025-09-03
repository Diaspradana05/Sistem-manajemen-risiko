<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($stats as $stat)
        <div
            class="rounded-xl border bg-white p-4"
            x-data
            x-transition.opacity
        >
            <div class="flex items-center gap-2 mb-2">
                <x-dynamic-component :component="$stat['icon']" class="w-5 h-5 text-primary-600" />
                <span class="text-sm font-semibold">{{ $stat['label'] }}</span>
            </div>

            <span
                x-data="{ c: 0, v: {{ $stat['value'] }} }"
                x-init="
                    let i = setInterval(() => {
                        if (c < v) { c++ } else { clearInterval(i) }
                    }, 18);
                "
                x-text="c"
                class="text-3xl font-bold"
            ></span>
        </div>
    @endforeach
</div>
