<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxStatus = [
            'Pegawai tetap',
            'Pegawai tidak tetap',
            'Bukan pegawai yang bersifat berkesinambungan',
            'Bukan pegawai yang tidak bersifat berkesinambungan',
            'Ekspatriat',
            'Ekspatriat dalam negeri',
            'Tenaga ahli yang bersifat berkesinambungan',
            'Tenaga ahli yang tidak bersifat berkesinambungan',
            'Dewan komisaris',
            'Tenaga ahli yang bersifat berkesinambungan > 1 PK',
            'Tenaga kerja lepas',
            'Bukan pegawai yang bersifat berkesinambungan > 1 PK'
        ];

        foreach ($taxStatus as $data)
        {
            DB::table('employeeTax_status')->insert([
                'employeeTaxStatus_name' => $data
            ]);
        }
    }
}
