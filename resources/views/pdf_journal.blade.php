<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Journal Security Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1e3a8a; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: black; font-size: 11px; }
        .section-title { font-size: 14px; color: #2563eb; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-top: 20px; margin-bottom: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { padding: 8px; border: 1px solid #cbd5e1; text-align: left; }
        table th { background-color: #f8fafc; color: #475569; width: 30%; font-weight: bold; }
        .content-box { border: 1px solid #e2e8f0; background-color: #f8fafc; padding: 10px; margin-bottom: 15px; min-height: 50px; border-radius: 4px; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: black; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .attachment-img { max-width: 100%; max-height: 400px; margin-bottom: 15px; display: block; }
        .attachment-name { font-size: 10px; color: black; margin-bottom: 5px; }
        .attachment-doc { border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 4px; background: #f8fafc; font-size: 11px; color: #475569; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('logo-aica.png') }}" style="width: 150px; margin-bottom: 10px;">
        <h1>Laporan Jurnal Keamanan Operasional PT AICA INDRIA</h1>
        <p>Dibuat Pada: {{ \Carbon\Carbon::parse($journal->created_at)->locale('id')->isoFormat('D MMMM Y HH:mm') }} | Status: <strong>{{ strtoupper($journal->status) }}</strong></p>
    </div>

    <div class="section-title">Informasi Dasar</div>
    <table>
        <tr>
            <th>Tanggal</th>
            {{-- Indonesian locale date: "Senin, 03 Maret 2025" --}}
            <td>{{ \Carbon\Carbon::parse($journal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $journal->location->nama_lokasi ?? '-' }} ({{ $journal->location->alamat_lokasi ?? '-' }})</td>
        </tr>
        <tr>
            <th>Shift</th>
            <td>{{ $journal->shift->nama_shift ?? '-' }} ({{ \Carbon\Carbon::parse($journal->shift->mulai_shift)->format('H:i') }} - {{ \Carbon\Carbon::parse($journal->shift->selesai_shift)->format('H:i') }})</td>        </tr>
    </table>

    <div class="section-title">Informasi Personil</div>
    <table>
        <tr>
            <th>Disubmit Oleh</th>
            <td>{{ $journal->user->nama ?? '-' }}</td>
        </tr>
        <tr>
            <th>Grup Pengisi</th>
            <td>{{ $journal->group->nama_grup ?? '-' }} <br> <span style="font-size:10px; color:black;">(Anggota: {{ $currentGroupMembers }})</span></td>
        </tr>
        <tr>
            <th>Grup Next Shift</th>
            <td>{{ $journal->nextShift->nama_grup ?? '-' }}</td> 
        </tr>
    </table>

    <div class="section-title">Detail Laporan</div>
    
    <strong>Laporan Kegiatan</strong>
    <div class="content-box">
        {!! nl2br(e($journal->laporan_kegiatan)) !!}
    </div>

    <strong>Kejadian / Temuan Khusus</strong>
    <div class="content-box">
        {!! nl2br(e($journal->kejadian_temuan)) !!}
    </div>

    <strong>Informasi Lembur</strong>
    <div class="content-box">
        {{ $journal->lembur }}
    </div>

    <strong>Proyek / Vendor Masuk</strong>
    <div class="content-box">
        {!! nl2br(e($journal->proyek_vendor)) !!}
    </div>

    <strong>Status Barang Inventaris</strong>
    <div class="content-box">
        {!! nl2br(e($journal->barang_inven)) !!}
    </div>

    @if($journal->info_tambahan)
    <strong>Informasi Tambahan</strong>
    <div class="content-box">
        {!! nl2br(e($journal->info_tambahan)) !!}
    </div>
    @endif

    @if($journal->catatan)
    <div class="section-title" style="color: #dc2626; border-bottom-color: #fecaca;">Catatan Revisi / Alasan Penolakan</div>
    <div class="content-box" style="background-color: #fef2f2; border-color: #fecaca; color: #991b1b;">
        {!! nl2br(e($journal->catatan)) !!}
    </div>
    @endif

    <div class="section-title">Riwayat Persetujuan</div>
    <table>
        <tr>
            <th>Diperbarui oleh</th>
            <td>{{ $journal->updater?->nama ?? 'Tidak Ada' }} - {{ $journal->updated_at ? \Carbon\Carbon::parse($journal->updated_at)->locale('id')->isoFormat('D MMMM Y HH:mm') : 'Belum Pernah' }}</td>
        </tr>
        <tr>
            <th>Serah Terima oleh</th>
            <td>{{ $journal->handover?->nama ?? 'Tidak Ada' }} - {{ $journal->handover_at ? \Carbon\Carbon::parse($journal->handover_at)->locale('id')->isoFormat('D MMMM Y HH:mm') : 'Belum Pernah' }}</td>
        </tr>
        <tr>
            <th>Persetujuan Akhir oleh</th>
            <td>{{ $journal->approver?->nama ?? 'Tidak Ada' }} - {{ $journal->approved_at ? \Carbon\Carbon::parse($journal->approved_at)->locale('id')->isoFormat('D MMMM Y HH:mm') : 'Belum Pernah' }}</td>
        </tr>
    </table>

    {{-- File Lampiran --}}
    @if(isset($uploads) && $uploads->count() > 0)
    <div class="section-title">File Lampiran</div>
    @foreach($uploads as $upload)
        @php
            $ext       = strtolower(pathinfo($upload->file_name, PATHINFO_EXTENSION));
            $cleanPath = str_replace('public/', '', $upload->file_path);
            $absPath   = storage_path('app/public/' . $cleanPath);
            $isImage   = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
        @endphp
        <!-- <p class="attachment-name">{{ $upload->file_name }}</p> -->
        @if($isImage && file_exists($absPath))
            <img src="{{ $absPath }}" class="attachment-img" alt="{{ $upload->file_name }}">
        @else
            <div class="attachment-doc">
                {{ $upload->file_name }} <em style="color:#94a3b8;">({{ strtoupper($ext) }} — tidak dapat ditampilkan secara inline)</em>
            </div>
        @endif
    @endforeach
    @endif

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh Sistem Jurnal Keamanan Operasional PT AICA INDRIA.
    </div>

</body>
</html>
