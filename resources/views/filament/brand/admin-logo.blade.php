@php
    $company = \App\Models\Company::first();
@endphp

@if($company?->logo)
    <img
        src="{{ \Illuminate\Support\Facades\Storage::url($company->logo) }}"
        alt="{{ $company->trade_name ?? 'IPV ERP' }}"
        style="height: 2.5rem; object-fit: contain;"
    />
@else
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:2rem;height:2rem;color:#3b82f6;">
            <path d="M11.644 1.59a.75.75 0 01.712 0l9.75 5.25a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25a.75.75 0 010-1.32l9.75-5.25z" />
            <path d="M3.265 10.602l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13 1.37.739a.75.75 0 010 1.32l-9.75 5.25a.75.75 0 01-.71 0l-9.75-5.25a.75.75 0 010-1.32l1.37-.738z" />
            <path d="M10.933 19.231l-7.668-4.13-1.37.739a.75.75 0 000 1.32l9.75 5.25c.221.12.489.12.71 0l9.75-5.25a.75.75 0 000-1.32l-1.37-.738-7.668 4.13a2.25 2.25 0 01-2.134-.001z" />
        </svg>
        <span style="font-size:1.25rem;font-weight:700;color:white;">IPV ERP</span>
    </div>
@endif
