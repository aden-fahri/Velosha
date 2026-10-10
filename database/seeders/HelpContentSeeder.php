<?php

namespace Database\Seeders;

use App\Models\HelpContent;
use Illuminate\Database\Seeder;

class HelpContentSeeder extends Seeder
{
    public function run(): void
    {
        HelpContent::query()->firstOrCreate([], HelpContent::defaults());
    }
}
