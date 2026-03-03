<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta - {{ $proposal->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #333; }
        .container { max-width: 210mm; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #2563eb; padding-bottom: 20px; }
        .header h1 { color: #2563eb; font-size: 28px; margin-bottom: 10px; }
        .header p { color: #666; font-size: 14px; }
        
        .section { margin-bottom: 25px; }
        .section-title { background: #2563eb; color: white; padding: 10px 15px; font-size: 16px; font-weight: bold; margin-bottom: 15px; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .info-item { padding: 10px; background: #f3f4f6; border-left: 3px solid #2563eb; }
        .info-item label { font-weight: bold; color: #2563eb; display: block; margin-bottom: 5px; }
        .info-item value { color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background: #f3f4f6; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: bold; color: #2563eb; text-transform: uppercase; font-size: 11px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-network { background: #ddd6fe; color: #5b21b6; }
        .badge-vm { background: #dbeafe; color: #1e40af; }
        .badge-s3 { background: #d1fae5; color: #065f46; }
        .badge-backup { background: #fed7aa; color: #92400e; }
        
        .summary { background: #f9fafb; padding: 20px; border-radius: 8px; border: 2px solid #2563eb; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .summary-row.total { font-size: 18px; font-weight: bold; color: #2563eb; border-top: 2px solid #2563eb; margin-top: 10px; padding-top: 15px; }
        .summary-row.discount { color: #dc2626; }
        
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #666; font-size: 11px; }
        
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .container { max-width: 100%; }
        }
    
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body { background: #0f172a !important; color: #e2e8f0 !important; }
            .container { background: #0f172a !important; }
            .info-item { background: #1e293b !important; border-color: #3b82f6 !important; }
            .info-item label { color: #60a5fa !important; }
            .info-item value, .info-item p { color: #e2e8f0 !important; }
            table { border-color: #334155 !important; }
            th { background: #1e40af !important; color: white !important; }
            td { border-color: #334155 !important; color: #e2e8f0 !important; }
            tr:nth-child(even) td { background: #1e293b !important; }
            .summary { background: #1e293b !important; border-color: #334155 !important; }
            .summary-row { border-color: #334155 !important; color: #e2e8f0 !important; }
            .summary-row.total { background: #1e3a5f !important; }
            .footer { background: #1e293b !important; border-color: #334155 !important; color: #94a3b8 !important; }
            h2, h3 { color: #e2e8f0 !important; }
            input, textarea, select { background: #1f2937 !important; color: white !important; border-color: #4b5563 !important; }
        }
        /* Force dark when .dark class present (Filament) */
        .dark body, html.dark body { background: #0f172a !important; color: #e2e8f0 !important; }

    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>💼 PROPOSTA COMERCIAL</h1>
            <p>{{ $customer->company_name }}</p>
            <p><strong>Projeto:</strong> {{ $proposal->name }}</p>
            <p><small>Gerado em: {{ now()->format('d/m/Y H:i') }}</small></p>
        </div>

        {{-- Informações do Cliente --}}
        <div class="section">
            <div class="section-title">📋 Informações do Cliente</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Empresa:</label>
                    <value>{{ $customer->company_name }}</value>
                </div>
                <div class="info-item">
                    <label>CNPJ:</label>
                    <value>{{ $customer->cnpj ?? 'N/A' }}</value>
                </div>
                <div class="info-item">
                    <label>Contato:</label>
                    <value>{{ $customer->contact_name ?? 'N/A' }}</value>
                </div>
                <div class="info-item">
                    <label>Email:</label>
                    <value>{{ $customer->email ?? 'N/A' }}</value>
                </div>
            </div>
        </div>

        {{-- Detalhamento dos Itens --}}
        <div class="section">
            <div class="section-title">💰 Detalhamento de Custos Mensais</div>
            
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Item</th>
                        <th>Descrição</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Desconto</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pricingData['items'] as $item)
                        <tr>
                            <td>
                                <span class="badge badge-{{ $item['type'] }}">
                                    {{ strtoupper($item['type']) }}
                                </span>
                            </td>
                            <td><strong>{{ $item['name'] }}</strong></td>
                            <td>{{ $item['description'] }}</td>
                            <td class="text-right">{{ $pricingData['summary']['currency'] }} {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                            <td class="text-right" style="color: #dc2626;">
                                @if($item['discount'] > 0)
                                    -{{ $pricingData['summary']['currency'] }} {{ number_format($item['discount'], 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right"><strong>{{ $pricingData['summary']['currency'] }} {{ number_format($item['total'], 2, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Resumo Financeiro --}}
        <div class="section">
            <div class="section-title">📊 Resumo Financeiro</div>
            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal de Itens:</span>
                    <span>{{ $pricingData['summary']['currency'] }} {{ number_format($pricingData['summary']['subtotal'], 2, ',', '.') }}</span>
                </div>
                
                @if($pricingData['summary']['item_discounts'] > 0)
                    <div class="summary-row discount">
                        <span>(-) Descontos em Itens:</span>
                        <span>-{{ $pricingData['summary']['currency'] }} {{ number_format($pricingData['summary']['item_discounts'], 2, ',', '.') }}</span>
                    </div>
                @endif
                
                <div class="summary-row">
                    <span>Total antes do Desconto Global:</span>
                    <span>{{ $pricingData['summary']['currency'] }} {{ number_format($pricingData['summary']['total_before_global_discount'], 2, ',', '.') }}</span>
                </div>
                
                @if($pricingData['summary']['global_discount'] > 0)
                    <div class="summary-row discount">
                        <span>(-) Desconto Global:</span>
                        <span>-{{ $pricingData['summary']['currency'] }} {{ number_format($pricingData['summary']['global_discount'], 2, ',', '.') }}</span>
                    </div>
                @endif
                
                <div class="summary-row total">
                    <span>💰 VALOR MENSAL TOTAL:</span>
                    <span>{{ $pricingData['summary']['currency'] }} {{ number_format($pricingData['summary']['total'], 2, ',', '.') }}</span>
                </div>

                @if($pricingData['summary']['partner_commission'])
                    <div class="summary-row" style="color: #2563eb; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                        <span>Comissão do Parceiro ({{ number_format($pricingData['summary']['partner_commission'], 2, ',', '.') }}%):</span>
                        <span>{{ $pricingData['summary']['currency'] }} {{ number_format(($pricingData['summary']['total'] * $pricingData['summary']['partner_commission']) / 100, 2, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Observações --}}
        <div class="section">
            <div class="section-title">📝 Observações</div>
            <p style="line-height: 1.6; padding: 15px; background: #f9fafb; border-radius: 8px;">
                • Os valores apresentados são mensais e em {{ $pricingData['summary']['currency'] }}.<br>
                • Esta proposta tem validade de 30 dias a partir da data de emissão.<br>
                • Os preços estão sujeitos a alteração sem aviso prévio.<br>
                @if($pricingData['summary']['item_discounts'] > 0 || $pricingData['summary']['global_discount'] > 0)
                • Descontos especiais já aplicados nesta proposta.<br>
                @endif
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p><strong>Parceiro:</strong> {{ $proposal->partner->company_name ?? 'N/A' }}</p>
            <p>Documento gerado automaticamente pelo sistema • {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
