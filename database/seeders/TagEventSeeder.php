<?php

namespace Database\Seeders;


use App\Models\Tag;
use App\Models\Tag_Event;
use App\Models\Event;

use Illuminate\Database\Seeder;

class TagEventSeeder extends Seeder
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
                'tag_id' => 1,
                'event_id' => 1,
            ]
        ];
        Tag_Event::create($datas[0]);

        $datas = [
            [
                'tag_id' => 1,
                'event_id' => 2,
            ]
        ];
        Tag_Event::create($datas[0]);

        $datas = [
            [
                'tag_id' => 1,
                'event_id' => 3,
            ]
        ];
        Tag_Event::create($datas[0]);

        $datas = [
            [
                'tag_id' => 2,
                'event_id' => 1,
            ]
        ];
        Tag_Event::create($datas[0]);

        $datas = [
            [
                'tag_id' => 2,
                'event_id' => 2,
            ]
        ];
        Tag_Event::create($datas[0]);

        $datas = [
            [
                'tag_id' => 3,
                'event_id' => 3,
            ]
        ];
        Tag_Event::create($datas[0]);

    }
}
