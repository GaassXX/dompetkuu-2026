<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected Collection $transactions,
        protected string $title = "Laporan Transaksi"
    ) {}

    public function collection(): Collection
    {
        return $this->transactions->map(function($t) {
            $t      = (object) $t;
            $status = $t->status ?? "-";
            $label  = $status === "approved" ? "Disetujui" : ($status === "pending" ? "Pending" : ($status === "rejected" ? "Ditolak" : $status));
            return [
                "Tipe"       => $t->type ?? "-",
                "Nama"       => $t->user_name ?? "-",
                "Kategori"   => $t->category_name ?? "-",
                "Jumlah"     => (float) ($t->amount ?? 0),
                "Tanggal"    => Carbon::parse($t->date ?? now())->format("d/m/Y"),
                "Status"     => $label,
                "Keterangan" => $t->description ?? "-",
            ];
        });
    }

    public function headings(): array
    {
        return ["Tipe", "Nama", "Kategori", "Jumlah (Rp)", "Tanggal", "Status", "Keterangan"];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                "font"      => ["bold" => true, "color" => ["rgb" => "FFFFFF"]],
                "fill"      => ["fillType" => "solid", "startColor" => ["rgb" => "059669"]],
                "alignment" => ["horizontal" => "center"],
            ],
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}