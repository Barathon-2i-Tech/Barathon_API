<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Status;

use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ownerValid = Status::where('comment->code', 'OWNER_VALID')->first();

        $datas = [
            [
                'siren' => fake()->siren(),
                'kbis' => 'chemin/kbis.pdf',
                'status_id' => $ownerValid->status_id
            ]
        ];
        Owner::create($datas[0]);
    }
}
