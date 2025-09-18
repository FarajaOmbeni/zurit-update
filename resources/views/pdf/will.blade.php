@php
  function kes($n) {
    $n = (float)($n ?? 0);
    return 'KES ' . number_format($n, 0);
  }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Last Will and Testament — {{ $user->name }}</title>
  <style>
    @page { margin: 28mm 20mm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; color:#111; font-size:12px; line-height:1.45; }
    h1,h2,h3 { margin:0 0 8px; }
    h1 { font-size:20px; text-align:center; letter-spacing:0.5px; }
    h2 { font-size:16px; margin-top:18px; border-bottom:1px solid #bbb; padding-bottom:4px; }
    .muted { color:#666; }
    .section { margin-top:16px; }
    .row { margin:6px 0; }
    .list { margin:6px 0 0 16px; padding:0; }
    .list li { margin:3px 0; }
    table { width:100%; border-collapse: collapse; margin-top:6px; }
    th, td { padding:6px 8px; border:1px solid #ddd; vertical-align:top; }
    th { background:#f7f7f7; text-align:left; }
    .right { text-align:right; }
    .small { font-size:11px; }
    .signature { margin-top:48px; }
    .signature .line { border-top:1px solid #000; width:60%; }
    .two-col { display:table; width:100%; }
    .col { display:table-cell; vertical-align:top; width:50%; }
  </style>
</head>
<body>

  <h1>LAST WILL AND TESTAMENT</h1>
  <p class="muted" style="text-align:center">Dated: {{ $today }}</p>

  <div class="section">
    <h2>Article 1 — Declaration</h2>
    <p>I, <strong>{{ $user->name }}</strong>, of {{ $user->email ?? '—' }}, being of sound mind and disposing memory, do hereby declare this to be my Last Will and Testament, and I revoke all prior wills and codicils made by me.</p>
  </div>

  <div class="section">
    <h2>Article 2 — Beneficiaries</h2>
    @if($beneficiaries->isEmpty())
      <p class="muted">No beneficiaries recorded.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Relationship</th>
            <th>Minor?</th>
            <th>Contact</th>
          </tr>
        </thead>
        <tbody>
          @foreach($beneficiaries as $b)
            <tr>
              <td>{{ $b->full_name }}</td>
              <td>{{ $b->relationship ?? '—' }}</td>
              <td>{{ $b->is_minor ? 'Yes' : 'No' }}</td>
              <td>
                @if($b->email) Email: {{ $b->email }}<br>@endif
                @if($b->phone_number) Phone: {{ $b->phone_number }} @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="section">
    <h2>Article 3 — Fiduciary Appointments</h2>
    @if($fiduciaries->isEmpty())
      <p class="muted">No fiduciary companies recorded. You may appoint a corporate trustee, executor, or other fiduciary.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Institution Name</th>
            <th>Type</th>
            <th>Contact</th>
            <th>Email / Phone</th>
          </tr>
        </thead>
        <tbody>
          @foreach($fiduciaries as $f)
            <tr>
              <td>{{ $f->institution_name }}</td>
              <td>{{ $f->institution_type ?? '—' }}</td>
              <td>{{ $f->contact_name ?? '—' }}</td>
              <td>
                @if($f->email) {{ $f->email }}<br>@endif
                @if($f->phone) {{ $f->phone }} @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="section">
    <h2>Article 4 — Estate Assets & Allocations</h2>
    <p>Total Estimated Value: <strong>{{ kes($totalAssetValue) }}</strong></p>

    @if($assets->isEmpty())
      <p class="muted">No assets recorded.</p>
    @else
      @foreach($assets as $a)
        @php
          $allocs = $a->beneficiaryAllocations ?? collect();
          $totalPct = $allocs->sum('percentage');
        @endphp
        <div class="row">
          <strong>{{ $a->name }}</strong>
          <span class="muted"> (Value: {{ kes($a->value) }})</span>
          @if($a->description)
            <div class="small">{{ $a->description }}</div>
          @endif

          @if($allocs->isEmpty())
            <div class="small muted">No allocations recorded for this asset.</div>
          @else
            <table class="small">
              <thead>
                <tr>
                  <th>Beneficiary</th>
                  <th class="right">Allocation (%)</th>
                </tr>
              </thead>
              <tbody>
                @foreach($allocs as $al)
                  <tr>
                    <td>{{ optional($al->beneficiary)->full_name ?? '—' }}</td>
                    <td class="right">{{ rtrim(rtrim(number_format((float)$al->percentage, 2, '.', ''), '0'), '.') }}%</td>
                  </tr>
                @endforeach
                <tr>
                  <td class="right"><strong>Total</strong></td>
                  <td class="right"><strong>{{ rtrim(rtrim(number_format((float)$totalPct, 2, '.', ''), '0'), '.') }}%</strong></td>
                </tr>
              </tbody>
            </table>
          @endif
        </div>
      @endforeach
    @endif
  </div>

  <div class="section">
    <h2>Article 5 — Insurance & Pension Policies</h2>
    @if($insurances->isEmpty())
      <p class="muted">No insurance or pension policies recorded.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Provider</th>
            <th>Type</th>
            <th>Policy #</th>
            <th>Coverage</th>
            <th>Premium</th>
            <th>Renewal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($insurances as $p)
            <tr>
              <td>{{ $p->provider_name }}</td>
              <td>{{ ucfirst($p->type) }}</td>
              <td>{{ $p->policy_number ?? '—' }}</td>
              <td>{{ $p->coverage_amount ? kes($p->coverage_amount) : '—' }}</td>
              <td>{{ $p->premium_amount ? kes($p->premium_amount) : '—' }}</td>
              <td>{{ $p->renewal_date ? \Illuminate\Support\Carbon::parse($p->renewal_date)->toFormattedDateString() : '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="section">
    <h2>Article 6 — General Provisions</h2>
    <p class="small">
      This document is a generated summary intended to reflect your current estate, fiduciary appointments, and intended allocations.
      It may not satisfy formal legal execution requirements for a will in your jurisdiction (e.g., witnessing, notarization).
      You should consult a qualified attorney to ensure validity and compliance with local law.
    </p>
  </div>

  <div class="two-col signature">
    <div class="col">
      <div class="line">&nbsp;</div>
      <div class="small">Testator Signature: {{ $user->name }}</div>
      <div class="small muted">Date: {{ $today }}</div>
    </div>
    <div class="col">
      <div class="line">&nbsp;</div>
      <div class="small">Witness Signature</div>
      <div class="small muted">Date: __________</div>
    </div>
  </div>

</body>
</html>
