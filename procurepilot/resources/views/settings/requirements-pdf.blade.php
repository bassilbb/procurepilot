<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $org->name }} — Supplier Registration Requirements</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.55;
            padding: 40px 48px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #059669;
            padding-bottom: 18px;
            margin-bottom: 28px;
        }
        .org-name { font-size: 20px; font-weight: 700; color: #064e3b; }
        .org-meta { font-size: 11px; color: #64748b; margin-top: 4px; }
        .doc-title {
            font-size: 15px;
            font-weight: 700;
            color: #064e3b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: right;
        }
        .doc-sub { font-size: 11px; color: #64748b; text-align: right; margin-top: 4px; }
        .intro {
            background: #ecfdf5;
            border-left: 4px solid #059669;
            padding: 14px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 12.5px;
            color: #065f46;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #059669;
            margin-bottom: 12px;
        }
        .req {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .req-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .req-name { font-weight: 600; font-size: 13.5px; color: #0f172a; }
        .req-desc { color: #64748b; font-size: 12px; margin-top: 3px; }
        .badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .badge-required { background: #fee2e2; color: #b91c1c; }
        .badge-optional { background: #f1f5f9; color: #475569; }
        .check-row {
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px dashed #e2e8f0;
            padding: 7px 0;
            font-size: 12.5px;
        }
        .check-row:last-child { border-bottom: none; }
        .num { color: #059669; font-weight: 700; width: 20px; }
        .check { color: #059669; font-weight: 700; }
        .note { font-size: 11px; color: #64748b; margin-top: 6px; }
        .footer {
            margin-top: 30px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            font-size: 10.5px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
        .col { width: 50%; }
        .col.right { text-align: right; }
        @media print {
            body { padding: 30px 40px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="org-name">{{ $org->name }}</div>
            <div class="org-meta">
                {{ $org->address ?? '' }}
                @if ($org->address && $org->email) &nbsp;·&nbsp; @endif
                {{ $org->email ?? '' }}
                @if ($org->email && $org->phone) &nbsp;·&nbsp; @endif
                {{ $org->phone ?? '' }}
            </div>
        </div>
        <div>
            <div class="doc-title">Supplier Registration Requirements</div>
            <div class="doc-sub">Document requirement checklist · {{ now()->format('d M Y') }}</div>
        </div>
    </div>

    <div class="intro">
        Vendors applying to register as suppliers with {{ $org->name }} are required to submit the following
        documents. Items marked <strong>Required</strong> are mandatory before the supplier can be vetted and approved.
        Where an item is marked <strong>Optional</strong>, submission is encouraged to strengthen the application.
        All documents are reviewed by the Tenders Board during supplier vetting.
    </div>

    @foreach (['required' => 'Mandatory Documents', 'optional' => 'Supporting Documents (Encouraged)'] as $group => $title)
        @php $grouped = $requirements->where('is_required', $group === 'required'); @endphp
        @if ($grouped->isNotEmpty())
            <div class="section-title">{{ $title }}</div>
            @foreach ($grouped as $req)
                <div class="req">
                    <div class="req-top">
                        <div>
                            <div class="req-name">{{ $req->name }}</div>
                            @if ($req->description)
                                <div class="req-desc">{{ $req->description }}</div>
                            @endif
                        </div>
                        <span class="badge {{ $req->is_required ? 'badge-required' : 'badge-optional' }}">
                            {{ $req->is_required ? 'Required' : 'Optional' }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach

    <div style="margin-top: 26px;">
        <div class="section-title">Submission Checklist</div>
        <div>
            @foreach ($requirements as $idx => $req)
                <div class="check-row">
                    <span class="num">{{ $idx + 1 }}.</span>
                    <span style="flex:1;">{{ $req->name }}</span>
                    <span class="check">☐</span>
                </div>
            @endforeach
        </div>
        <div class="note">Tick each box as the corresponding document is uploaded during registration. Incomplete
            submissions may delay vetting and approval.</div>
    </div>

    <div class="footer">
        <div class="col">
            Generated by <strong>ProcurePilot</strong> · {{ $org->name }}<br>
            Prepared on {{ now()->format('j F Y, g:i A') }}
        </div>
        <div class="col right">
            @if ($org->tax_id) TIN: {{ $org->tax_id }}<br>@endif
            This is a system-generated document.
        </div>
    </div>
</body>
</html>
