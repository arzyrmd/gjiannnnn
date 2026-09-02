<?php

namespace Database\Seeders;

use App\Models\Tarif;
use Illuminate\Database\Seeder;

class TarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tarifs = [
            [
                'kategori' => 'Kirim Faktur',
                'tarif_berhasil' => 15000,
                'tarif_gagal' => 10000,
            ],
            [
                'kategori' => 'Kunjungan',
                'tarif_berhasil' => 15000,
                'tarif_gagal' => 10000,
            ],
            [
                'kategori' => 'Pasang Baru QRIS',
                'tarif_berhasil' => 15000,
                'tarif_gagal' => 10000,
            ],
            [
                'kategori' => 'Pemasangan EDC',
                'tarif_berhasil' => 15000,
                'tarif_gagal' => 10000,
            ],
            [
                'kategori' => 'Init',
                'tarif_berhasil' => 15000,
                'tarif_gagal' => 10000,
            ],
            [
                'kategori' => 'Penarikan EDC',
                'tarif_berhasil' => 15000,
                'tarif_gagal' => 15000,
            ],
            [
                'kategori' => 'Proaktif Maintenance (dalam mall)',
                'tarif_berhasil' => 10000,
                'tarif_gagal' => null, // Tidak dibayar
            ],
            [
                'kategori' => 'Proaktif Maintenance (luar mall)',
                'tarif_berhasil' => 10000,
                'tarif_gagal' => 5000,
            ],
            [
                'kategori' => 'Piket Mall (Diluar JO)',
                'tarif_berhasil' => 50000,
                'tarif_gagal' => 50000,
            ],
            [
                'kategori' => 'Piket Event',
                'tarif_berhasil' => 0, // Fee custom diisi sendiri saat input
                'tarif_gagal' => 0,
            ],
        ];

        foreach ($tarifs as $data) {
            Tarif::updateOrCreate(
                ['kategori' => $data['kategori']],
                $data
            );
        }
    }
}
