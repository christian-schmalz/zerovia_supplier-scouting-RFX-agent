<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    @if($shortlist && $shortlist->isNotEmpty())
        <x-filament::section heading="Lieferanten-Shortlist">
            <div class="space-y-3">
                @foreach($shortlist as $i => $item)
                    @php $supplier = $item['supplier']; @endphp
                    <div class="flex items-center gap-4 p-4 bg-white border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-bold text-sm">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900">{{ $supplier->name }}</div>
                            <div class="text-sm text-gray-500">{{ $supplier->city }}, {{ $supplier->country }} · {{ $item['distance'] }} km</div>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full font-medium">
                                ESG {{ $supplier->esg_score }}
                            </span>
                            <span class="px-2 py-1 {{ $supplier->risk_level === 'low' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }} rounded-full font-medium">
                                {{ $supplier->risk_label }}
                            </span>
                            <span class="font-bold text-primary-600">Score {{ $item['score'] }}/100</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <x-filament::button wire:click="generateRfq" color="primary">
                    RFQ generieren
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    @if($rfqText)
        <x-filament::section heading="Generiertes RFQ — {{ $rfqReferenceNr }}">
            <textarea
                class="w-full font-mono text-xs p-4 bg-gray-50 border border-gray-200 rounded-lg resize-y min-h-96"
                rows="30"
            >{{ $rfqText }}</textarea>
            <div class="mt-3 flex gap-2">
                <x-filament::button
                    x-on:click="navigator.clipboard.writeText($el.closest('section').querySelector('textarea').value).then(() => $el.textContent = '✓ Kopiert')"
                    color="gray"
                >
                    In Zwischenablage kopieren
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
