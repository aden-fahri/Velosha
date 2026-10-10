<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DemoUserSeeder::class);
        $this->call(DemoPesertaSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(ExtraEventSeeder::class);
        $this->call(AboutContentSeeder::class);
        $this->call(RuleContentSeeder::class);
        $this->call(HelpContentSeeder::class);
        $this->call(RegistrationSeeder::class);
    }
}
