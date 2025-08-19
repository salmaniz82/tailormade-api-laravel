<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock;

class StockSeeder extends Seeder
{
    
    public function run(): void
    {
        // Clear old data
        Stock::truncate();
         $stocks = [
            [ "name" => "All", "url" => "all", "active" => true, "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"]) ],
            [ "name" => "Fox Brothers", "url" => "foxflannel.com", "active" => true, "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"]) ],
            [ "name" => "Loro Piana", "url" => "loropiana.com", "active" => true, "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"]) ],
            [ "name" => "Dugdale Bros.", "url" => "shop.dugdalebros.com", "active" => true, "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"]) ],
            [ "name" => "Harrisons", "url" => "harrisons1863.com", "active" => true , "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"])],
            [ "name" => "Maison Hellard", "url" => "maisonhellard.com", "active" => true , "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"])],
            [ "name" => "Dormeuil", "url" => "dormeuil.com", "active" => true , "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"])],
            [ "name" => "Tailormade House Collection", "url" => "tailormadelondon.com", "active" => true, "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"]) ],
            [ "name" => "Caccioppoli", "url" => "caccioppoli.com", "active" => true, "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"]) ],
            [ "name" => "Abraham Moon", "url" => "moons.co.uk", "active" => true, "metaFields" => json_encode(["Mill","Bunch","Description","Composition","Weight","Season","Garment Type"]) ],
        ];

        Stock::insert($stocks);


    }
}
