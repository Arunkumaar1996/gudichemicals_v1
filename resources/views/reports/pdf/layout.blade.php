<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title', 'Report') — Gudi Chemicals</title>
    <style>
        @page {
            margin: 12mm 10mm 15mm 10mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #005a9c;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #005a9c;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }
        .company-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .company-meta {
            font-size: 8.5px;
            color: #475569;
        }
        .report-title-box {
            text-align: right;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0 0 3px 0;
        }
        .report-period {
            font-size: 9px;
            font-weight: bold;
            color: #005a9c;
        }
        .generated-on {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Summary Badges / Boxes */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .summary-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            background-color: #f8fafc;
            vertical-align: middle;
        }
        .summary-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            display: block;
        }
        .summary-val {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #005a9c;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 6px;
            border: 1px solid #00487c;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4.5px 6px;
            font-size: 8.5px;
            color: #334155;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .data-table tr.total-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #94a3b8;
        }

        /* Utility Classes */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-primary { color: #005a9c; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-muted { color: #64748b; }
        .badge-status {
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-paid { background-color: #dcfce7; color: #15803d; }
        .badge-partial { background-color: #fef3c7; color: #b45309; }
        .badge-unpaid { background-color: #fee2e2; color: #b91c1c; }
        .badge-low { background-color: #fee2e2; color: #b91c1c; }
        .badge-optimal { background-color: #dcfce7; color: #15803d; }

        /* Footer */
        .page-footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            height: 7mm;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
            font-size: 7.5px;
            color: #64748b;
        }
        .page-footer table {
            width: 100%;
            border-collapse: collapse;
        }
        .page-footer td {
            border: none;
            padding: 0;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Top Branding & Report Meta Header -->
    <table class="header-table">
        <tr>
            <td style="width: 58%;">
                <div class="company-name">{{ $company->company_name ?? 'GUDI CHEMICALS' }}</div>
                <div class="company-subtitle">{{ $company->trade_name ?? 'Chemical Manufacturing, Industrial Solutions & Reselling' }}</div>
                <div class="company-meta">
                    {{ $company->address ?? 'MIDC Chemical Zone, Pune, Maharashtra' }}<br>
                    <strong>GSTIN:</strong> {{ $company->gstin ?? '27AAACG1234D1Z5' }} | 
                    <strong>State:</strong> {{ $company->state_name ?? 'Maharashtra' }} ({{ $company->state_code ?? '27' }})
                </div>
            </td>
            <td style="width: 42%;" class="report-title-box">
                <div class="report-title">@yield('report_title')</div>
                <div class="report-period">@yield('report_period')</div>
                <div class="generated-on">Generated: {{ now()->format('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    <!-- Main Report Body Content -->
    @yield('content')

    <!-- Footer -->
    <div class="page-footer">
        <table>
            <tr>
                <td style="text-align: left;">Gudi Chemicals ERP &mdash; Official Confidential Report</td>
                <td style="text-align: right;">Generated from Live Database</td>
            </tr>
        </table>
    </div>
</body>
</html>
