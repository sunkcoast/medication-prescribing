<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resep Digital #{{ $prescription->id }}</title>
    <style>
        /* CSS tetap sama dengan milik Anda */
        body { font-family: sans-serif; line-height: 1.4; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; font-size: 13px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th { background: #f4f4f4; border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 13px; }
        .items-table td { border: 1px solid #ddd; padding: 8px; font-size: 13px; }
        .total-section { text-align: right; margin-top: 20px; font-weight: bold; font-size: 16px; }
        .footer { margin-top: 50px; font-size: 11px; text-align: center; color: #777; }
        .signature { margin-top: 40px; text-align: right; margin-right: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">RS DELTA SURYA</h2> 
        <p style="margin:0; font-size: 12px;">Jl. Pahlawan No.9, Jati, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 61211 | Telp: (031) 8961272</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>DATA PASIEN:</strong><br>
                {{-- Mengakses langsung via relasi prescription --}}
                Nama: {{ $prescription->examination->patient->name }}<br>
                Gender: {{ ucfirst($prescription->examination->patient->gender) }}<br>
                Alamat: {{ $prescription->examination->patient->address }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>DETAIL RESEP:</strong><br>
                No. Resep: #{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}<br>
                Tanggal: {{ $prescription->created_at->format('d M Y H:i') }}<br>
                {{-- Mengakses dokter via relasi --}}
                Dokter: {{ $prescription->doctor->name }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th width="20%">Harga Satuan</th>
                <th width="10%">Jumlah</th>
                <th width="20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop langsung dari relasi items --}}
            @foreach($prescription->items as $item)
            <tr>
                <td>{{ $item->medicine_name }}</td>
                <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        {{-- Menghitung total otomatis dari koleksi items --}}
        TOTAL PEMBAYARAN: Rp {{ number_format($prescription->items->sum('total_price'), 0, ',', '.') }}
    </div>

    <div class="signature">
        <p>Surabaya, {{ $prescription->created_at->format('d M Y') }}</p>
        <br><br>
        <p><strong>( {{ $prescription->doctor->name }} )</strong><br>SIP. {{ rand(100000, 999999) }}</p>
    </div>

    <div class="footer">
        * Ini adalah dokumen resmi digital dan tidak memerlukan tanda tangan basah.
    </div>
</body>
</html>