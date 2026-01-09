<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Status Pembayaran - BPABS</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f9fafb;
            color: #111827;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        h1 {
            font-size: 2rem;
            color: #7c3aed;
        }
        .status-message {
            font-size: 1.2rem;
            margin: 20px 0;
            text-align: center;
        }
        .icon {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .button {
            background: #7c3aed;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #6b21a8;
        }
        .success { color: #10B981; }
        .pending { color: #FBBF24; }
        .failed { color: #EF4444; }
    </style>
</head>
<body>

    <h1>Status Pembayaran</h1>
    <div class="status-message">
        @if($status === 'settlement' || $status === 'capture')
            <span class="material-icons icon success">check_circle</span>
            <p>Pembayaran Anda berhasil! Terima kasih telah bertransaksi.</p>
        @elseif($status === 'pending')
            <span class="material-icons icon pending">hourglass_empty</span>
            <p>Pembayaran Anda sedang diproses. Mohon tunggu konfirmasi.</p>
        @else
            <span class="material-icons icon failed">cancel</span>
            <p>Pembayaran Anda gagal. Silakan coba lagi.</p>
        @endif
    </div>

    <a href="{{ route('pelanggan.tagihan.index') }}">
        <button class="button">Kembali ke Dashboard</button>
    </a>

</body>
</html>