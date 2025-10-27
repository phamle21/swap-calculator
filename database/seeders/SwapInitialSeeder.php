<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SwapInitialSeeder extends Seeder
{
    public function run(): void
    {
        // ========== 1. Currency Pairs ==========
        $pairs = [
            ['symbol' => 'EURUSD', 'base' => 'EUR', 'quote' => 'USD', 'digits' => 5],
            ['symbol' => 'GBPUSD', 'base' => 'GBP', 'quote' => 'USD', 'digits' => 5],
            ['symbol' => 'USDJPY', 'base' => 'USD', 'quote' => 'JPY', 'digits' => 3],
            ['symbol' => 'XAUUSD', 'base' => 'XAU', 'quote' => 'USD', 'digits' => 2],
            ['symbol' => 'AUDUSD', 'base' => 'AUD', 'quote' => 'USD', 'digits' => 5],
        ];

        foreach ($pairs as $p) {
            DB::table('currency_pairs')->updateOrInsert(
                ['symbol' => $p['symbol']],
                [
                    'base' => $p['base'],
                    'quote' => $p['quote'],
                    'digits' => $p['digits'],
                    'is_active' => true,
                    'meta' => json_encode([]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ========== 2. Swap Profiles ==========
        DB::table('swap_profiles')->updateOrInsert(
            ['name' => 'Default'],
            [
                'note' => 'Default broker profile',
                'wednesday_multiplier' => 3.00,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('swap_profiles')->updateOrInsert(
            ['name' => 'BrokerA'],
            [
                'note' => 'Broker A with special rates',
                'wednesday_multiplier' => 3.00,
                'settings' => json_encode(['commission' => 0.0]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $defaultProfileId = DB::table('swap_profiles')->where('name', 'Default')->value('id');
        $brokerAProfileId = DB::table('swap_profiles')->where('name', 'BrokerA')->value('id');

        // ========== 3. Swap Rates ==========
        $today = Carbon::today()->toDateString();
        $rates = [
            // symbol, profile, long, short
            ['EURUSD', $defaultProfileId,  0.50, -0.20],
            ['GBPUSD', $defaultProfileId,  0.40, -0.30],
            ['USDJPY', $defaultProfileId,  0.25, -0.15],
            ['XAUUSD', $defaultProfileId, -3.20,  1.10],
            ['AUDUSD', $defaultProfileId,  0.35, -0.25],

            ['EURUSD', $brokerAProfileId,  0.55, -0.22],
            ['GBPUSD', $brokerAProfileId,  0.45, -0.35],
            ['XAUUSD', $brokerAProfileId, -3.10,  1.05],
        ];

        foreach ($rates as [$symbol, $profileId, $long, $short]) {
            $pairId = DB::table('currency_pairs')->where('symbol', $symbol)->value('id');
            if (!$pairId || !$profileId) continue;

            DB::table('swap_rates')->updateOrInsert(
                [
                    'currency_pair_id' => $pairId,
                    'profile_id'       => $profileId,
                    'effective_from'   => $today,
                ],
                [
                    'swap_long'   => $long,
                    'swap_short'  => $short,
                    'effective_to' => null,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }

        // ========== 4. Sample Calculations ==========
        $eurusdId = DB::table('currency_pairs')->where('symbol', 'EURUSD')->value('id');
        DB::table('swap_calculations')->insert([
            [
                'currency_pair_id' => $eurusdId,
                'profile_id' => $defaultProfileId,
                'lot_size' => 1.0,
                'position_type' => 'Long',
                'swap_rate' => 0.50,
                'days' => 3,
                'cross_wednesday' => false,
                'total_swap' => 1.5,
                'note' => 'Example seed',
                'inputs' => json_encode(['pair' => 'EURUSD', 'lot' => 1, 'days' => 3]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
