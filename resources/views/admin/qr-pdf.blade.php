<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px 50px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-align: center;
            color: #1e293b;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            margin-top: 60px;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .subtitle {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 60px;
        }
        .qr-container {
            text-align: center;
            margin: 0 auto;
        }
        .qr-container img {
            width: 350px;
            height: 350px;
        }
        .url {
            margin-top: 40px;
            font-size: 14px;
            color: #64748b;
            word-break: break-all;
        }
        .footer {
            margin-top: 80px;
            font-size: 12px;
            color: #94a3b8;
        }
        .instruction {
            margin-top: 30px;
            font-size: 18px;
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="title">{{ $election->title }}</div>
    <div class="subtitle">Parti Keadilan Rakyat</div>

    <div class="qr-container">
        <img src="{{ $qrBase64 }}" alt="QR Code">
    </div>

    <div class="instruction">Imbas QR Code di atas untuk mengakses borang</div>
    <div class="url">{{ $url }}</div>

    <div class="footer">PARTI KEADILAN RAKYAT &copy; {{ date('Y') }}</div>
</body>
</html>
