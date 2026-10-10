<?php

namespace Database\Seeders;

use App\Models\RuleContent;
use Illuminate\Database\Seeder;

class RuleContentSeeder extends Seeder
{
    public function run(): void
    {
        RuleContent::query()->firstOrCreate([], RuleContent::defaults());
    }
}
