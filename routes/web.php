<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProposalController;


// Página principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Redirect raiz do admin para o painel
Route::get('/erp', function () {
    return redirect('/erp/login');
});

// Redirect raiz do partner para o painel
Route::get('/portal', function () {
    return redirect('/portal/login');
});

// Impressão de proposta
Route::get('/imprimir/proposta/{id}', function ($id) {
    $proposal = \App\Models\Proposal::with([
        'items', 'partner', 'customer', 'createdBy',
    ])->findOrFail($id);
    $customer = $proposal->customer;
    $partner  = $proposal->partner;

    // Construir $pricingData a partir dos itens da proposta
    $items = $proposal->items->map(function ($item) {
        return [
            'type'        => $item->type ?? 'item',
            'name'        => $item->name,
            'description' => $item->description ?? '',
            'subtotal'    => (float) $item->unit_price,
            'discount'    => (float) ($item->discount ?? 0),
            'total'       => (float) $item->total,
        ];
    })->values()->toArray();

    $subtotal       = collect($items)->sum('subtotal');
    $itemDiscounts  = collect($items)->sum('discount');
    $globalDiscount = (float) ($proposal->discount_amount ?? 0);
    $totalBeforeGD  = $subtotal - $itemDiscounts;
    $total          = (float) $proposal->total;

    $pricingData = [
        'items'   => $items,
        'summary' => [
            'currency'                   => 'R$',
            'subtotal'                   => $subtotal,
            'item_discounts'             => $itemDiscounts,
            'total_before_global_discount' => $totalBeforeGD,
            'global_discount'            => $globalDiscount,
            'total'                      => $total,
            'partner_commission'         => $proposal->partner_commission_percentage ?? null,
        ],
    ];

    return view('proposals.print', compact('proposal', 'customer', 'partner', 'pricingData'));
})->name('proposta.imprimir');


Route::get('/proposals/{project}/print', [ProposalController::class, 'print'])
    ->name('proposals.print')
    ->middleware('auth');
