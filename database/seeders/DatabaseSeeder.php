<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(DataSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(CreateAdminUserSeeder::class);

        \DB::insert("INSERT INTO canales(name) VALUES ('AGENTES SC'),  ('AGENTES CD'), ('AGENTES PP'), ('EMAIL') ,('IVR'), ('SMS'), ('VOICE BOT'), ('WHATSAPP');");

        
         //
    }
}
