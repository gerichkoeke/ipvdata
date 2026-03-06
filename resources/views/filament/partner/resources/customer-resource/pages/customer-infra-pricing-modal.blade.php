@if($showPricingModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0" style="background-color:rgba(0,0,0,0.8);backdrop-filter:blur(4px);" wire:click="closePricingModal"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-2xl w-full max-w-6xl shadow-2xl flex flex-col overflow-hidden" style="max-height:90vh;">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between shrink-0">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <x-heroicon-o-currency-dollar class="w-5 h-5 text-primary-400"/>
                Detalhamento de Preços - {{ $selectedProject?->name ?? '' }}
            </h2>
            <button wire:click="closePricingModal" class="text-gray-400 hover:text-white transition-colors">
                <x-heroicon-m-x-mark class="w-5 h-5"/>
            </button>
        </div>
        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
            @if($pricingData && $selectedProject)
                <div class="space-y-6">

                    {{-- Seção de Comissão Variável --}}
                    @if($selectedProject->hasVariableCommission())
                        <div class="bg-blue-900/20 p-4 rounded-lg border border-blue-800">
                            <h3 class="text-lg font-semibold mb-3 text-blue-100">
                                💼 Comissão do Parceiro
                            </h3>
                            <div class="flex items-end gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium mb-1">
                                        Porcentagem de Comissão (%)
                                    </label>
                                    <input
                                        type="number"
                                        wire:model="partnerCommission"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        class="w-full rounded-lg border-gray-600 bg-gray-800 text-white"
                                        placeholder="Ex: 15.00"
                                    />
                                </div>
                                <x-filament::button
                                    wire:click="applyCommission"
                                    color="primary"
                                >
                                    Aplicar Comissão
                                </x-filament::button>
                            </div>
                        </div>
                    @endif

                    {{-- Seção de Desconto Global --}}
                    <div class="bg-amber-900/20 p-4 rounded-lg border border-amber-800">
                        <h3 class="text-lg font-semibold mb-3 text-amber-100">
                            🏷️ Desconto Global no Projeto
                        </h3>
                        {{-- Informação sobre limite máximo de desconto --}}
                        @php
                            $__subtotalPricing = $pricingData['summary']['subtotal'];
                            $__maxDiscountPct = \App\Filament\Partner\Resources\CustomerResource\Pages\CustomerInfra::MAX_DISCOUNT_PERCENT / 100;
                            $__maxDiscount = $__subtotalPricing * $__maxDiscountPct;
                            $__currentItemDiscounts = $pricingData['summary']['item_discounts'];
                            $__allowedGlobal = max(0, $__maxDiscount - $__currentItemDiscounts);
                        @endphp
                        <div class="mb-3 p-3 rounded-lg bg-amber-900/30 border border-amber-700 text-sm">
                            <div class="flex items-center gap-2 text-amber-200 font-semibold mb-1">
                                <span>⚠️ Limite máximo de desconto: {{ \App\Filament\Partner\Resources\CustomerResource\Pages\CustomerInfra::MAX_DISCOUNT_PERCENT }}% do projeto</span>
                            </div>
                            <div class="text-amber-300 space-y-0.5">
                                <div>Valor total do projeto: <strong>{{ $pricingData['summary']['currency'] }} {{ number_format($__subtotalPricing, 2, ',', '.') }}</strong></div>
                                <div>Desconto máximo permitido ({{ \App\Filament\Partner\Resources\CustomerResource\Pages\CustomerInfra::MAX_DISCOUNT_PERCENT }}%): <strong>{{ $pricingData['summary']['currency'] }} {{ number_format($__maxDiscount, 2, ',', '.') }}</strong></div>
                                @if($__currentItemDiscounts > 0)
                                <div>Descontos de itens aplicados: <strong class="text-red-400">- {{ $pricingData['summary']['currency'] }} {{ number_format($__currentItemDiscounts, 2, ',', '.') }}</strong></div>
                                @endif
                                <div>Disponível para desconto global: <strong class="text-green-400">{{ $pricingData['summary']['currency'] }} {{ number_format($__allowedGlobal, 2, ',', '.') }}</strong></div>
                            </div>
                        </div>
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium mb-1">
                                    Valor do Desconto ({{ $pricingData['summary']['currency'] }})
                                </label>
                                <input
                                    type="number"
                                    wire:model="globalDiscount"
                                    step="0.01"
                                    min="0"
                                    max="{{ number_format($__allowedGlobal, 2, '.', '') }}"
                                    class="w-full rounded-lg border-gray-600 bg-gray-800 text-white"
                                    placeholder="Ex: 500.00"
                                />
                            </div>
                            <x-filament::button
                                wire:click="applyGlobalDiscount"
                                color="warning"
                            >
                                Aplicar Desconto Global
                            </x-filament::button>
                        </div>
                    </div>

                    {{-- Grid de Itens --}}
                    @php
                        $__maxDiscountPctItems = \App\Filament\Partner\Resources\CustomerResource\Pages\CustomerInfra::MAX_DISCOUNT_PERCENT / 100;
                        $__maxDiscountGlobal = $pricingData['summary']['subtotal'] * $__maxDiscountPctItems;
                        $__globalDiscountAmt = $pricingData['summary']['global_discount'];
                        $__maxForItems = max(0, $__maxDiscountGlobal - $__globalDiscountAmt);
                    @endphp
                    <div class="bg-gray-900 rounded-lg border border-gray-700">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold">📋 Itens do Projeto</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Tipo
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Nome / Descrição
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Subtotal
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Desconto
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    @foreach($pricingData['items'] as $item)
                                        <tr class="hover:bg-gray-800/60">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($item['type'] === 'network') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                    @elseif($item['type'] === 'vm') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @elseif($item['type'] === 's3') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                    @endif">
                                                    {{ strtoupper($item['type']) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $item['name'] }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $item['description'] }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium">
                                                {{ number_format($item['subtotal'], 2, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <input
                                                    type="number"
                                                    wire:model="discounts.{{ $item['type'] === 'network' ? 'network' : $item['type'] . '_' . $item['id'] }}"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ $item['subtotal'] }}"
                                                    class="w-28 text-right rounded border-gray-600 bg-gray-800 text-sm text-white"
                                                    placeholder="0,00"
                                                />
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-green-400">
                                                {{ number_format($item['total'], 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-900 font-bold">
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-right">
                                            Subtotal Geral:
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            {{ number_format($pricingData['summary']['subtotal'], 2, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-red-400">
                                            -{{ number_format($pricingData['summary']['item_discounts'], 2, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            {{ number_format($pricingData['summary']['total_before_global_discount'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @if($pricingData['summary']['global_discount'] > 0)
                                        <tr class="text-amber-600 dark:text-amber-400">
                                            <td colspan="4" class="px-4 py-3 text-right">
                                                Desconto Global:
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                -{{ number_format($pricingData['summary']['global_discount'], 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="text-lg bg-green-900/20">
                                        <td colspan="4" class="px-4 py-4 text-right text-green-100">
                                            💰 TOTAL MENSAL:
                                        </td>
                                        <td class="px-4 py-4 text-right text-green-400">
                                            {{ $pricingData['summary']['currency'] }} {{ number_format($pricingData['summary']['total'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            {{-- Nota sobre limite de desconto por itens --}}
                            <div class="px-4 py-2 text-xs text-amber-400 bg-amber-900/10 border-t border-amber-800">
                                ⚠️ A soma de todos os descontos (itens + global) não pode ultrapassar {{ \App\Filament\Partner\Resources\CustomerResource\Pages\CustomerInfra::MAX_DISCOUNT_PERCENT }}% do projeto
                                (<strong>{{ $pricingData['summary']['currency'] }} {{ number_format($pricingData['summary']['subtotal'] * (\App\Filament\Partner\Resources\CustomerResource\Pages\CustomerInfra::MAX_DISCOUNT_PERCENT / 100), 2, ',', '.') }}</strong>).
                            </div>
                        </div>
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="flex justify-between items-center pt-4">
                        <x-filament::button
                            wire:click="applyItemDiscounts"
                            color="success"
                            size="lg"
                        >
                            ✅ Aplicar Descontos nos Itens
                        </x-filament::button>

                        <div class="flex gap-3">
                            <x-filament::button
                                wire:click="generatePdf"
                                color="info"
                                icon="heroicon-o-document-text"
                            >
                                📄 Gerar PDF
                            </x-filament::button>

                            <x-filament::button
                                wire:click="closePricingModal"
                                color="gray"
                            >
                                Fechar
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

