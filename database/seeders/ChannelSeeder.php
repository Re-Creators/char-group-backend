<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chat_channels')->insert([
            'name' => 'Welcome',
            'description' => 'Welcome everyone to this channel'
        ]);

        DB::table('chat_channels')->insert([
            'name' => 'Random',
            'description' => 'Channel just to chill and talk random things, feel free to talk with other people'
        ]);
    }
}
