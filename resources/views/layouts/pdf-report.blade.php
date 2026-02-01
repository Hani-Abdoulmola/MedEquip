<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle ?? 'تقرير' }}</title>
    <style>
        * {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-size: 12px;
            color: #1f2937;
            line-height: 1.6;
            direction: rtl;
            unicode-bidi: embed;
            background-color: #f0f0f0;
        }
        .container {
            width: 210mm;
            margin: 20px auto;
            padding: 25px 30px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .pdf-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #282E4D;
        }
        .pdf-header .side-text {
            display: table-cell;
            width: 30%;
            font-size: 12px;
            line-height: 1.6;
            text-align: center;
            vertical-align: middle;
        }
        .pdf-header .logo-wrap {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: middle;
        }
        .pdf-header .logo-wrap img {
            max-height: 90px;
            max-width: 140px;
        }
        .info {
            text-align: center;
            margin-bottom: 10px;
        }
        .info span {
            font-size: 18px;
            font-weight: bold;
            color: #282E4D;
        }
        .dates {
            margin-bottom: 20px;
            font-size: 13px;
            color: #333;
            text-align: left;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .details th {
            background-color: #282E4D;
            color: #fff;
            padding: 8px;
            border: 1px solid #ccc;
            text-align: right;
        }
        .details td {
            padding: 6px;
            text-align: right;
            border: 1px solid #ddd;
        }
        .details tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .summary {
            background-color: #e2e2e2;
            font-weight: bold;
        }
        .pdf-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            padding-top: 10px;
            border-top: 2px solid #000;
            color: #444;
        }
        @media print {
            body {
                background: none;
            }
            .container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }
            .pdf-footer {
                border-top: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header: Arabic text | Logo | English text --}}
        <div class="pdf-header">
            <div class="side-text">
                <p>
                    منصة MedEquip<br>
                    معدات طبية
                </p>
            </div>
            <div class="logo-wrap">
                @if(!empty($logoPath) && file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo">
                @else
                    <strong>MedEquip</strong>
                @endif
            </div>
            <div class="side-text">
                <p>
                    MedEquip Platform<br>
                    Medical Equipment
                </p>
            </div>
        </div>

        {{-- Report title --}}
        <div class="info">
            <span>{{ $reportTitle ?? 'تقرير' }}</span>
        </div>

        {{-- Print date --}}
        <div class="dates">
            تاريخ الطباعة: <strong>{{ $printDate ?? now()->format('Y-m-d') }}</strong>
        </div>

        {{-- Content (invoice body, payments table, etc.) --}}
        <div class="details">
            @yield('content')
        </div>

        {{-- Footer --}}
        <div class="pdf-footer">
            جميع الحقوق محفوظة © {{ $footerYear ?? now()->year }}
        </div>
    </div>
</body>
</html>
