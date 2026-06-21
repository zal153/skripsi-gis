<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keluhan & Feedback Pengguna</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0 0 0;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: normal;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 10px;
        }
        .line {
            border-top: 2px solid #000;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .report-item {
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 10px;
            page-break-inside: avoid;
        }
        .report-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 5px 10px;
            margin: -10px -10px 10px -10px;
        }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            color: #0056b3;
            margin: 0;
        }
        .report-meta {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
        .reply-box {
            background-color: #f1f3f5;
            border: 1px solid #e9ecef;
            padding: 8px;
            margin-top: 10px;
        }
        .reply-title {
            font-weight: bold;
            font-size: 10px;
            color: #2b8a3e;
            margin-bottom: 5px;
        }
        .reply-item {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            padding: 5px;
            margin-bottom: 5px;
            font-size: 10px;
        }
        .reply-meta {
            font-size: 8px;
            color: #868e96;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Keluhan & Feedback Pengguna</h2>
        <h3>Sistem Informasi Geografis Posyandu</h3>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>
    <div class="line"></div>

    @if ($laporans->isEmpty())
        <div style="text-align: center; padding: 30px; color: #868e96;">
            Belum ada laporan posyandu dari pengguna.
        </div>
    @else
        @foreach ($laporans as $item)
            <div class="report-item">
                <div class="report-header">
                    <h4 class="report-title">{{ $item->nama_posyandu }}</h4>
                    <div class="report-meta">Dilaporkan pada: {{ $item->created_at->format('d M Y, H:i') }}</div>
                </div>
                <p style="margin: 5px 0;"><strong>Alamat:</strong> {{ $item->alamat }}</p>
                @if ($item->keterangan)
                    <p style="margin: 5px 0; color: #495057;"><strong>Keterangan:</strong> {{ $item->keterangan }}</p>
                @endif

                @if ($item->balasans->isNotEmpty())
                    <div class="reply-box">
                        <div class="reply-title">Balasan Petugas ({{ $item->balasans->count() }})</div>
                        @foreach ($item->balasans as $balasan)
                            <div class="reply-item">
                                <div class="reply-meta">
                                    <strong>{{ $balasan->user ? $balasan->user->name : 'Admin' }}</strong> 
                                    - {{ $balasan->created_at->format('d M Y, H:i') }}
                                </div>
                                <div style="color: #495057;">{{ $balasan->pesan }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</body>
</html>
