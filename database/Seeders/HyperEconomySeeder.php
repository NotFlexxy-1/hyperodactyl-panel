<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Hyperodactyl\Models\HyperAchievement;
use Hyperodactyl\Models\HyperStoreItem;

class HyperEconomySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Extra 1GB Memory', 'description' => 'Adds 1024MB of memory to a server you own.', 'category' => HyperStoreItem::CATEGORY_RESOURCE, 'icon' => 'memory', 'price' => 500, 'effect' => ['resource' => 'memory', 'amount' => 1024], 'enabled' => true, 'stock' => null],
            ['name' => 'Extra 5GB Disk', 'description' => 'Adds 5120MB of disk space to a server you own.', 'category' => HyperStoreItem::CATEGORY_RESOURCE, 'icon' => 'disk', 'price' => 400, 'effect' => ['resource' => 'disk', 'amount' => 5120], 'enabled' => true, 'stock' => null],
            ['name' => 'Extra CPU Core', 'description' => 'Adds 100% CPU allocation to a server you own.', 'category' => HyperStoreItem::CATEGORY_RESOURCE, 'icon' => 'cpu', 'price' => 600, 'effect' => ['resource' => 'cpu', 'amount' => 100], 'enabled' => true, 'stock' => null],
            ['name' => 'Extra Backup Slot', 'description' => 'Adds one additional backup slot to a server you own.', 'category' => HyperStoreItem::CATEGORY_SERVER_SLOT, 'icon' => 'backup', 'price' => 250, 'effect' => ['resource' => 'backup_limit', 'amount' => 1], 'enabled' => true, 'stock' => null],
            ['name' => 'Extra Database Slot', 'description' => 'Adds one additional database slot to a server you own.', 'category' => HyperStoreItem::CATEGORY_SERVER_SLOT, 'icon' => 'database', 'price' => 250, 'effect' => ['resource' => 'database_limit', 'amount' => 1], 'enabled' => true, 'stock' => null],
            ['name' => 'Extra Allocation Slot', 'description' => 'Adds one additional network allocation slot to a server you own.', 'category' => HyperStoreItem::CATEGORY_SERVER_SLOT, 'icon' => 'network', 'price' => 250, 'effect' => ['resource' => 'allocation_limit', 'amount' => 1], 'enabled' => true, 'stock' => null],
            ['name' => 'Supporter Badge', 'description' => 'A cosmetic badge shown on your profile.', 'category' => HyperStoreItem::CATEGORY_COSMETIC, 'icon' => 'badge', 'price' => 150, 'effect' => [], 'enabled' => true, 'stock' => null],
        ];

        foreach ($items as $item) {
            HyperStoreItem::query()->firstOrCreate(['name' => $item['name']], $item);
        }

        $achievements = [
            ['key' => 'first_server', 'name' => 'First Steps', 'description' => 'Own your first server.', 'icon' => 'server', 'coin_reward' => 100, 'criteria' => ['type' => 'servers_owned', 'min' => 1]],
            ['key' => 'server_collector', 'name' => 'Server Collector', 'description' => 'Own 3 or more servers.', 'icon' => 'servers', 'coin_reward' => 300, 'criteria' => ['type' => 'servers_owned', 'min' => 3]],
            ['key' => 'first_backup', 'name' => 'Better Safe Than Sorry', 'description' => 'Create your first backup.', 'icon' => 'backup', 'coin_reward' => 50, 'criteria' => ['type' => 'backups_count', 'min' => 1]],
            ['key' => 'backup_master', 'name' => 'Backup Master', 'description' => 'Have 10 or more backups across your servers.', 'icon' => 'backup', 'coin_reward' => 250, 'criteria' => ['type' => 'backups_count', 'min' => 10]],
            ['key' => 'veteran', 'name' => 'Veteran', 'description' => 'Have an account that is at least 30 days old.', 'icon' => 'clock', 'coin_reward' => 200, 'criteria' => ['type' => 'account_age_days', 'min' => 30]],
            ['key' => 'big_spender', 'name' => 'Big Spender', 'description' => 'Spend 1000 Hyper Coins in the store.', 'icon' => 'coin', 'coin_reward' => 150, 'criteria' => ['type' => 'coins_spent', 'min' => 1000]],
        ];

        foreach ($achievements as $achievement) {
            HyperAchievement::query()->firstOrCreate(['key' => $achievement['key']], $achievement);
        }
    }
}
