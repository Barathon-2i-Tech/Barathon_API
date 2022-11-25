<?php

namespace Database\Seeders;


use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'comment' => [
                    'code' => 'OWNER_VALID',
                    'description' => 'Le propriétaire a été validé'
                ]
            ],
            [
                'comment' => [
                    'code' => 'OWNER_REFUSE',
                    'description' => 'Le propriétaire a été refusé'
                ]
            ],
            [
                'comment' => [
                    'code' => 'OWNER_PENDING',
                    'description' => 'Le propriétaire est en attente de verification'
                ]
            ],
            [
                'comment' => [
                    'code' => 'ESTABL_VALID',
                    'description' => 'L\'établissement a été validé'
                ]
            ],
            [
                'comment' => [
                    'code' => 'ESTABL_REFUSE',
                    'description' => 'L\'établissement a été refusé'
                ]
            ],
            [
                'comment' => [
                    'code' => 'ESTABL_PENDING',
                    'description' => 'L\'établissement est en attente de verification'
                ]
            ],
            [
                'comment' => [
                    'code' => 'EVENT_VALID',
                    'description' => 'L\'évenement a été validé'
                ]
            ],
            [
                'comment' => [
                    'code' => 'EVENT_REFUSE',
                    'description' => 'L\'évenement  a été refusé'
                ]
            ],
            [
                'comment' => [
                    'code' => 'EVENT_PENDING',
                    'description' => 'L\'évenement  est en attente de verification'
                ]
            ],
        ];
        foreach ($datas as $data) {
            Status::create($data);
        }
    }
}
