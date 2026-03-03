<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light dark">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta - {{ $proposal->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #333; background: #fff; }
        .container { max-width: 210mm; margin: 0 auto; padding: 20px; }

        /* Print button */
        .no-print { margin-bottom: 20px; }
        .btn-print { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn-print:hover { background: #1d4ed8; }

        /* Whitelabel header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; border-bottom: 3px solid #2563eb; padding-bottom: 20px; gap: 20px; }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .header-logo { max-height: 56px; max-width: 160px; object-fit: contain; }
        .header-partner-name { font-size: 16px; font-weight: 700; color: #1e293b; }
        .header-partner-sub { font-size: 11px; color: #64748b; margin-top: 2px; }
        .header-right { text-align: right; }
        .header-right h1 { color: #2563eb; font-size: 22px; font-weight: 800; letter-spacing: 1px; }
        .header-right p { color: #64748b; font-size: 12px; margin-top: 4px; }

        .section { margin-bottom: 25px; }
        .section-title { background: #2563eb; color: #fff; padding: 10px 15px; font-size: 15px; font-weight: bold; margin-bottom: 15px; border-radius: 4px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
        .info-item { padding: 10px 12px; background: transparent; border: 1px solid #e2e8f0; border-left: 3px solid #2563eb; border-radius: 4px; }
        .info-item label { font-weight: 700; color: #2563eb; display: block; margin-bottom: 4px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-item p { color: inherit; font-size: 13px; }

        /* Cloud presentation */
        .cloud-section { border: 1px solid #e2e8f0; border-left: 4px solid #2563eb; border-radius: 6px; padding: 16px 20px; margin-bottom: 25px; }
        .cloud-section h3 { font-size: 14px; font-weight: 700; color: #1e40af; margin-bottom: 10px; }
        .cloud-section p { font-size: 12px; line-height: 1.7; color: inherit; margin-bottom: 8px; }
        .cloud-benefits { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 10px; }
        .cloud-benefit { display: flex; align-items: center; gap: 6px; font-size: 11px; color: inherit; }
        .cloud-benefit::before { content: '✔'; color: #16a34a; font-weight: 700; margin-right: 6px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background: transparent; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { font-weight: 700; color: #2563eb; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; border-bottom: 2px solid #2563eb; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .badge { display: inline-block; padding: 3px 7px; border-radius: 4px; font-size: 10px; font-weight: 700; }
        .badge-network { background: #ede9fe; color: #5b21b6; }
        .badge-vm { background: #dbeafe; color: #1e40af; }
        .badge-s3 { background: #d1fae5; color: #065f46; }
        .badge-backup { background: #fed7aa; color: #92400e; }
        .badge-item { background: #f1f5f9; color: #475569; }

        .summary { padding: 20px; border-radius: 8px; border: 2px solid #2563eb; }
        .summary-row { display: flex; justify-content: space-between; padding: 7px 0; font-size: 13px; border-bottom: 1px solid #e2e8f0; }
        .summary-row:last-child { border-bottom: none; }
        .summary-row.total { font-size: 18px; font-weight: 800; color: #2563eb; border-top: 2px solid #2563eb; border-bottom: none; margin-top: 8px; padding-top: 14px; }
        .summary-row.discount { color: #dc2626; }

        .notes-box { line-height: 1.7; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; }

        .footer { margin-top: 40px; padding-top: 18px; border-top: 2px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-start; font-size: 11px; color: #64748b; }
        .footer-left p { margin-bottom: 3px; }
        .footer-right { text-align: right; }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            body { background: #0f172a; color: #e2e8f0; }
            .container { background: #0f172a; }
            .header-partner-name { color: #f1f5f9; }
            .header-partner-sub, .header-right p { color: #94a3b8; }
            .info-item { border-color: #334155; border-left-color: #3b82f6; }
            .info-item label { color: #60a5fa; }
            .cloud-section { border-color: #334155; border-left-color: #3b82f6; }
            .cloud-section h3 { color: #93c5fd; }
            th { color: #60a5fa; border-bottom-color: #3b82f6; }
            td { border-bottom-color: #1e293b; }
            tr:nth-child(even) td { background: #1e293b; }
            .summary { border-color: #3b82f6; }
            .summary-row { border-bottom-color: #1e293b; }
            .summary-row.total { border-top-color: #3b82f6; color: #93c5fd; }
            .notes-box { border-color: #334155; }
            .footer { border-top-color: #1e293b; color: #94a3b8; }
            .badge-item { background: #1e293b; color: #94a3b8; }
        }

        /* Print overrides */
        @media print {
            body { background: #fff !important; color: #333 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .container { max-width: 100%; }
            .no-print { display: none !important; }
            .info-item { border-color: #e2e8f0 !important; border-left-color: #2563eb !important; }
            .info-item label { color: #2563eb !important; }
            th { color: #2563eb !important; border-bottom-color: #2563eb !important; }
            td { border-bottom-color: #e2e8f0 !important; color: #333 !important; }
            tr:nth-child(even) td { background: #f8fafc !important; }
            .summary { border-color: #2563eb !important; }
            .summary-row { border-bottom-color: #e2e8f0 !important; color: #333 !important; }
            .summary-row.total { color: #2563eb !important; border-top-color: #2563eb !important; }
            .footer { border-top-color: #e2e8f0 !important; color: #64748b !important; }
        }
    </style>
</head>
<body>
    <div class="container">

        {{-- Print button (hidden when printing) --}}
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Salvar PDF</button>
        </div>

        {{-- Whitelabel Header --}}
        <div class="header">
            <div class="header-left">
                @if(!empty($partner->logo_url))
                    <img src="{{ $partner->logo_url }}" alt="{{ $partner->company_name }}" class="header-logo">
                @endif
                <div>
                    <p class="header-partner-name">{{ $partner->company_name ?? 'N/A' }}</p>
                    @if(!empty($partner->cnpj))
                        <p class="header-partner-sub">CNPJ: {{ $partner->cnpj }}</p>
                    @endif
                    @if(!empty($partner->phone))
                        <p class="header-partner-sub">{{ $partner->phone }}</p>
                    @endif
                    @if(!empty($partner->email))
                        <p class="header-partner-sub">{{ $partner->email }}</p>
                    @endif
                </div>
            </div>
            <div class="header-right">
                <h1>PROPOSTA COMERCIAL</h1>
                @if(!empty($proposal->number))
                    <p><strong>Nº:</strong> {{ $proposal->number }}</p>
                @endif
                <p><strong>Data:</strong> {{ now()->format('d/m/Y') }}</p>
                @if(!empty($proposal->valid_until))
                    <p><strong>Válida até:</strong> {{ \Carbon\Carbon::parse($proposal->valid_until)->format('d/m/Y') }}</p>
                @endif
            </div>
        </div>

        {{-- Dados do Cliente --}}
        <div class="section">
            <div class="section-title">📋 Dados do Cliente</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Empresa</label>
                    <p>{{ $customer->company_name }}</p>
                </div>
                <div class="info-item">
                    <label>CNPJ</label>
                    <p>{{ $customer->cnpj ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <label>Contato</label>
                    <p>{{ $customer->contact_name ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p>{{ $customer->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Apresentação Cloud --}}
        <div class="section">
            <div class="cloud-section">
                <h3>☁️ Infraestrutura Cloud — Confiabilidade e Performance para o seu Negócio</h3>
                <p>Nossa plataforma de cloud oferece infraestrutura de alto desempenho com recursos dedicados, gerenciamento simplificado e suporte especializado. Abaixo apresentamos a proposta customizada para as necessidades da sua empresa.</p>
                <div class="cloud-benefits">
                    <span class="cloud-benefit">SLA 99,9% de disponibilidade</span>
                    <span class="cloud-benefit">Alta disponibilidade e redundância</span>
                    <span class="cloud-benefit">Suporte técnico especializado</span>
                    <span class="cloud-benefit">Backup e recuperação de dados</span>
                    <span class="cloud-benefit">Escalabilidade sob demanda</span>
                    <span class="cloud-benefit">Segurança e monitoramento 24/7</span>
                </div>
            </div>
        </div>

        {{-- Detalhamento dos Serviços --}}
        <div class="section">
            <div class="section-title">💰 Detalhamento de Serviços</div>
            <table>
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Serviço</th>
                        <th>Especificações</th>
                        <th class="text-right">Valor/mês</th>
                        <th class="text-right">Desconto</th>
                        <th class="text-right">Total/mês</th>
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
                            <td class="text-right" style="color:#dc2626;">
                                @if($item['discount'] > 0)
                                    -{{ $pricingData['summary']['currency'] }} {{ number_format($item['discount'], 2, ',', '.') }}
                                @else
                                    —
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
                    <div class="summary-row" style="color:#2563eb; margin-top:10px; padding-top:10px; border-top:1px solid #e2e8f0;">
                        <span>Comissão do Parceiro ({{ number_format($pricingData['summary']['partner_commission'], 2, ',', '.') }}%):</span>
                        <span>{{ $pricingData['summary']['currency'] }} {{ number_format(($pricingData['summary']['total'] * $pricingData['summary']['partner_commission']) / 100, 2, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Observações --}}
        <div class="section">
            <div class="section-title">📝 Observações</div>
            <div class="notes-box">
                @if(!empty($proposal->notes))
                    <p style="margin-bottom:8px;">{{ $proposal->notes }}</p>
                @endif
                <p>• Os valores apresentados são mensais e em {{ $pricingData['summary']['currency'] }}.</p>
                <p>• Proposta válida por
                    @if(!empty($proposal->valid_until))
                        até {{ \Carbon\Carbon::parse($proposal->valid_until)->format('d/m/Y') }}.
                    @else
                        30 dias a partir da data de emissão.
                    @endif
                </p>
                <p>• Os preços estão sujeitos a alteração sem aviso prévio.</p>
                @if($pricingData['summary']['item_discounts'] > 0 || $pricingData['summary']['global_discount'] > 0)
                    <p>• Descontos especiais já aplicados nesta proposta.</p>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-left">
                <p><strong>{{ $partner->company_name ?? 'N/A' }}</strong></p>
                @if(!empty($partner->cnpj))<p>CNPJ: {{ $partner->cnpj }}</p>@endif
                @if(!empty($partner->phone))<p>{{ $partner->phone }}</p>@endif
                @if(!empty($partner->email))<p>{{ $partner->email }}</p>@endif
            </div>
            <div class="footer-right">
                <p>Documento gerado em {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

    </div>
</body>
</html>
