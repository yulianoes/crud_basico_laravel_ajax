<?php

namespace Database\Seeders;

use App\Models\Contrato;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria 5000 contratos aleatórios
        $quantity = 5000;
        Contrato::factory()->count($quantity)->create();
    }
}
