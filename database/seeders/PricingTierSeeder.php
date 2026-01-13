<?php

namespace Database\Seeders;

use App\Models\PricingTier;
use Illuminate\Database\Seeder;

class PricingTierSeeder extends Seeder
{
    public function run(): void
    {
        // 早鳥價（1月）
        PricingTier::create([
            'name' => 'Early Bird',
            'one_time_price' => 150000,
            'installment_price' => 13000,
            'starts_at' => '2026-01-01 00:00:00',
            'ends_at' => '2026-01-31 23:59:59',
            'is_active' => true,
        ]);

        // 正常價（2-3月）
        PricingTier::create([
            'name' => 'Regular',
            'one_time_price' => 180000,
            'installment_price' => 15000,
            'starts_at' => '2026-02-01 00:00:00',
            'ends_at' => '2026-03-31 23:59:59',
            'is_active' => true,
        ]);

        // 最後機會（4月）
        PricingTier::create([
            'name' => 'Last Chance',
            'one_time_price' => 200000,
            'installment_price' => 17000,
            'starts_at' => '2026-04-01 00:00:00',
            'ends_at' => '2026-04-30 23:59:59',
            'is_active' => true,
        ]);
    }
}
