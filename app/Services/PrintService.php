<?php

namespace App\Services;

use App\Models\Queue;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;

class PrintService
{
    public function printTicket(Queue $queue)
    {
        try {
            $printerPath = env('PRINTER_PATH', 'LPT1');

            // For Windows, WindowsPrintConnector is usually better if the printer is installed as a driver
            // If it's a raw port, FilePrintConnector might work.
            $connector = new WindowsPrintConnector($printerPath);
            $printer = new Printer($connector);

            /* Initialize */
            $printer->initialize();

            /* Header */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("KANTOR PAJAK WATES\n");
            $printer->setEmphasis(false);
            $printer->text("NOMOR ANTRIAN\n");
            $printer->feed();

            /* Service Name */
            $printer->text($queue->service->name . "\n");
            $printer->feed(2); // Menambah jarak antara layanan dan nomor

            /* Queue Number */
            $printer->setTextSize(4, 6); // Tinggi diperbesar menjadi 6
            $printer->setEmphasis(true);
            $printer->text($queue->queue_number . "\n");
            $printer->setEmphasis(false);
            $printer->setTextSize(1, 1);
            $printer->feed(1);

            /* Footer */
            $printer->text("Terima kasih atas kunjungan Anda\n");
            $printer->text("Silakan tunggu panggilan\n");

            /* Timestamp */
            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $date = $queue->created_at;
            $dayName = $days[$date->dayOfWeek];
            $monthName = $months[$date->month];
            $formattedDate = "$dayName, {$date->day} $monthName {$date->year}";
            $formattedTime = $date->format('H:i');

            $printer->text($formattedDate . "\n");
            $printer->text("Pukul " . $formattedTime . " WIB\n");

            $printer->feed(3);

            /* Auto Cut */
            $printer->cut();

            /* Close */
            $printer->close();

            return true;
        } catch (\Exception $e) {
            \Log::error("Print Error: " . $e->getMessage());
            return false;
        }
    }
}
