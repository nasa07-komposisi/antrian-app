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
            /* Adjust padding for EPOS - increased bottom padding to trigger auto-cut at 15cm */
            padding: 2mm 4mm 120mm 4mm;
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
            font-size: 48pt;
            font-weight: bold;
            margin: 25px 0 15px 0;
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
        <div class="timestamp" style="margin-top: 5px;">
            @php
                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $date = $queue->created_at;
                $dayName = $days[$date->dayOfWeek];
                $monthName = $months[$date->month];
                $formattedDate = "$dayName, {$date->day} $monthName {$date->year}";
                $formattedTime = $date->format('H:i');
            @endphp
            {{ $formattedDate }} <br>
            Pukul {{ $formattedTime }} WIB
        </div>
    </div>

    <script>
        window.onload = function () {
            window.print();

            // If the template is opened in a new window/popup (legacy), close it.
            // If in an iframe, the session will be cleared on next dashboard load.
            setTimeout(function () {
                if (window.self === window.top) {
                    window.close();
                }
            }, 500);
        };
    </script>
</body>

</html>