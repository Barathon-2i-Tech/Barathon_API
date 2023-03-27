<?php

namespace Database\Seeders;

use App\Models\Category_Event;
use Illuminate\Database\Seeder;

class CategoryEventSeeder extends Seeder
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
                'category_id' => 1,
                'event_id' => 1,
            ],
        ];
        Category_Event::create($datas[0]);

        $datas = [
            [
                'category_id' => 1,
                'event_id' => 2,
            ],
        ];
        Category_Event::create($datas[0]);

        $datas = [
            [
                'category_id' => 1,
                'event_id' => 3,
            ],
        ];
        Category_Event::create($datas[0]);

        $datas = [
            [
                'category_id' => 2,
                'event_id' => 1,
            ],
        ];
        Category_Event::create($datas[0]);

        $datas = [
            [
                'category_id' => 2,
                'event_id' => 2,
            ],
        ];
        Category_Event::create($datas[0]);

        $datas = [
            [
                'category_id' => 3,
                'event_id' => 3,
            ],
        ];
        Category_Event::create($datas[0]);
    }
}
