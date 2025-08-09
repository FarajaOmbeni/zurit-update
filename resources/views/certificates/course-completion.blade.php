<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Certificate of Completion</title>
    @php
        // Auto-shrink display sizes if text is long (helps keep one page)
        $nameSize   = mb_strlen($user_name ?? '')   > 26 ? '10mm' : '12mm';
        $courseSize = mb_strlen($course_title ?? '')> 32 ? '6mm'  : '7mm';
    @endphp
    <style>
        /* ===== Dompdf single-page & compatibility rules =====
           - Use @page for paper & margins
           - Prefer tables & simple blocks; avoid complex layout features
           - Prevent internal page breaks on key blocks
        */
        @page { size: A4 landscape; margin: 12mm; } /* 12mm for a bit more vertical room */
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #ffffff; font-family: 'DejaVu Sans', sans-serif; color: #1f2937; }
        body { font-size: 3.6mm; line-height: 1.3; }

        .page,
        .frame-outer,
        .frame-inner,
        .header,
        .stats,
        .footer { page-break-inside: avoid; break-inside: avoid; } /* Dompdf respects page-break-inside */

        .page { width: 100%; }

        /* Modern dual frame */
        .frame-outer { border: 1mm solid #eef2f7; padding: 5mm; }
        .frame-inner { border: 0.6mm solid #e5e7eb; padding: 8mm 9mm; }

        /* Simple top accent */
        .accent { height: 2.5mm; background: linear-gradient(90deg, #667eea, #764ba2); margin-bottom: 8mm; }

        /* Header (table is robust in Dompdf) */
        .header { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 8mm; }
        .header td { width: 33.33%; vertical-align: middle; font-size: 4mm; color: #6b7280; }
        .brand { font-weight: 600; color: #1f2937; text-align: left; }
        .title { font-family: 'DejaVu Serif', serif; font-weight: 700; font-size: 9mm; color: #1f2937; text-align: center; }
        .program { text-align: right; }

        /* Recipient */
        .presented { text-align: center; color: #6b7280; font-size: 4mm; margin-bottom: 2mm; }
        .name { text-align: center; font-family: 'DejaVu Serif', serif; font-weight: 700; color: #1f2937; line-height: 1.05; word-wrap: break-word; }
        .underline { height: 0.6mm; background: #667eea; width: 90%; margin: 2.5mm auto 6mm; }
        .statement { text-align: center; color: #6b7280; font-size: 4mm; }
        .course { text-align: center; font-family: 'DejaVu Serif', serif; font-weight: 700; color: #1f2937; margin-top: 2mm; line-height: 1.1; word-wrap: break-word; }

        /* Stats */
        .stats { width: 100%; border-collapse: separate; border-spacing: 0 3mm; margin: 8mm 0 8mm; table-layout: fixed; }
        .stat { background: #fafafa; border: 0.4mm solid #eef2f7; border-radius: 3mm; padding: 5mm 0; text-align: center; }
        .value { color: #667eea; font-weight: 700; font-size: 5.5mm; display: block; line-height: 1.1; }
        .label { color: #6b7280; font-size: 3.2mm; letter-spacing: 0.25mm; text-transform: uppercase; display: block; }

        /* Footer */
        .footer { border-top: 0.6mm solid #eef2f7; padding-top: 6mm; }
        .footer-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .footer-table td { width: 33.33%; vertical-align: bottom; }
        .meta-cap { color: #6b7280; font-size: 3.1mm; }
        .meta-val { color: #1f2937; font-size: 3.8mm; font-weight: 600; }
        .sig { text-align: center; }
        .sig-line { width: 68mm; height: 0.5mm; background: #e5e7eb; margin: 0 auto 2.5mm; }
        .sig-name { font-family: 'DejaVu Serif', serif; font-size: 5.5mm; color: #1f2937; }
        .sig-title { color: #6b7280; font-size: 3mm; }
    </style>
</head>
<body>
    <div class="page">
        <div class="frame-outer">
            <div class="frame-inner">
                <div class="accent"></div>

                <table class="header">
                    <tr>
                        <td class="brand">Zurit</td>
                        <td class="title">Certificate of Completion</td>
                        <td class="program">Financial Literacy Program</td>
                    </tr>
                </table>

                <div class="presented">Presented to</div>
                <div class="name" style="font-size: {{ $nameSize }};">{{ $user_name }}</div>
                <div class="underline"></div>
                <div class="statement">for successfully completing</div>
                <div class="course" style="font-size: {{ $courseSize }};">{{ $course_title }}</div>

                <table class="stats">
                    <tr>
                        <td class="stat">
                            <span class="value">{{ $overall_score }}%</span>
                            <span class="label">Overall Score</span>
                        </td>
                        <td style="width:3mm"></td>
                        <td class="stat">
                            <span class="value">{{ $total_subcourses }}</span>
                            <span class="label">Modules Completed</span>
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    <table class="footer-table">
                        <tr>
                            <td>
                                <div class="meta-cap">Issued on</div>
                                <div class="meta-val">{{ $completion_date }}</div>
                            </td>
                            <td class="sig">
                                <div class="sig-line"></div>
                                <div class="sig-name">Zurit</div>
                                <div class="sig-title">Program Director</div>
                            </td>
                            <td style="text-align:right;">
                                <div class="meta-cap">Certificate ID</div>
                                <div class="meta-val">{{ $certificate_id }}</div>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
