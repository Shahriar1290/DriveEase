<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $hashedPassword = Hash::make('password');

        // Admin
        DB::insert("
            INSERT INTO users (name, email, password, role, phone, address, email_verified_at, is_active, created_at, updated_at)
            VALUES (?, ?, ?, 'admin', ?, ?, NOW(), 1, NOW(), NOW())
        ", [
            'Admin User', 'admin@vrms.com', $hashedPassword, '01700000000', '123 Admin Street, Dhaka',
        ]);

        // 20 Customers
        $customers = [
            ['name'=>'Alice Johnson',   'email'=>'alice@example.com'],
            ['name'=>'Bob Smith',       'email'=>'bob@example.com'],
            ['name'=>'Carol White',     'email'=>'carol@example.com'],
            ['name'=>'David Brown',     'email'=>'david@example.com'],
            ['name'=>'Eve Davis',       'email'=>'eve@example.com'],
            ['name'=>'Frank Wilson',    'email'=>'frank@example.com'],
            ['name'=>'Grace Taylor',    'email'=>'grace@example.com'],
            ['name'=>'Henry Anderson',  'email'=>'henry@example.com'],
            ['name'=>'Iris Thomas',     'email'=>'iris@example.com'],
            ['name'=>'Jack Martinez',   'email'=>'jack@example.com'],
            ['name'=>'Karen Lee',       'email'=>'karen@example.com'],
            ['name'=>'Leo Harris',      'email'=>'leo@example.com'],
            ['name'=>'Mia Clark',       'email'=>'mia@example.com'],
            ['name'=>'Noah Lewis',      'email'=>'noah@example.com'],
            ['name'=>'Olivia Walker',   'email'=>'olivia@example.com'],
            ['name'=>'Paul Hall',       'email'=>'paul@example.com'],
            ['name'=>'Quinn Allen',     'email'=>'quinn@example.com'],
            ['name'=>'Rachel Young',    'email'=>'rachel@example.com'],
            ['name'=>'Sam King',        'email'=>'sam@example.com'],
            ['name'=>'Tina Wright',     'email'=>'tina@example.com'],
        ];

        $phones = ['01711', '01811', '01911', '01711', '01611'];

        foreach ($customers as $i => $c) {
            DB::insert("
                INSERT INTO users (name, email, password, role, phone, address, email_verified_at, is_active, created_at, updated_at)
                VALUES (?, ?, ?, 'customer', ?, ?, NOW(), 1, NOW(), NOW())
            ", [
                $c['name'],
                $c['email'],
                $hashedPassword,
                $phones[$i % 5] . str_pad($i * 111111, 6, '0', STR_PAD_LEFT),
                ($i + 1) . ' Customer Lane, City',
            ]);
        }
    }
}
