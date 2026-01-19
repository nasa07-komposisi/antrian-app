<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Antrian - {{ $queue->queue_number }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0;
            /* Adjust padding for EPOS proportional margins */
            padding: 2mm 4mm 20mm 4mm;
            text-align: center;
            font-size: 11pt;
            /* Prevent unwanted overflow */
            overflow: hidden;
        }

        .header {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 5px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }

        .app-name {
            font-size: 10pt;
            margin-bottom: 10px;
        }

        .service-name {
            font-size: 11pt;
            margin-top: 5px;
            font-weight: bold;
        }

        .queue-number {
            font-size: 32pt;
            font-weight: bold;
            margin: 10px 0;
        }

        .footer {
            margin-top: 15px;
            font-size: 9pt;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }

        .timestamp {
            font-size: 8pt;
            color: #555;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="app-name">KANTOR PAJAK WATES</div>
    <div class="header">NOMOR ANTRIAN</div>

    <div class="service-name">{{ $queue->service->name }}</div>
    <div class="queue-number">{{ $queue->queue_number }}</div>

    <div class="footer">
        <div>Terima kasih atas kunjungan Anda</div>
        <div>Silakan tunggu panggilan</div>
        <div class="timestamp">{{ $queue->created_at->format('d/m/Y H:i:s') }}</div>
    </div>

    <script>
        window.onload = function () {
            window.print();
            setTimeout(function () {
                window.close();
            }, 500);
        };
    </script>
</body>

</html>