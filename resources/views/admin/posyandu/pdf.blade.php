<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Posyandu</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .badge {
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            color: white;
            border-radius: 3px;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Posyandu</h2>
        <h3>Sistem Informasi Geografis Posyandu</h3>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>
    <div class="line"></div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Nama Posyandu</th>
                <th style="width: 12%;">Desa</th>
                <th style="width: 33%;">Alamat</th>
                <th style="width: 10%;">Latitude</th>
                <th style="width: 10%;">Longitude</th>
                <th class="text-center" style="width: 8%;">Status</th>
                <th style="width: 17%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posyandu as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->nama_posyandu }}</strong></td>
                    <td>{{ $item->desa->nama_desa }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->latitude }}</td>
                    <td>{{ $item->longitude }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->status === 'aktif' ? 'badge-success' : 'badge-danger' }}">
                            {{ $item->status === 'aktif' ? 'Aktif' : 'Tidak' }}
                        </span>
                    </td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
