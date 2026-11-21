<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\MemberCardService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Create the admin and demo member accounts.
     */
    public function run(MemberCardService $memberCards): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Perpusa',
                'password' => 'Perpusa123!',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'phone' => '081200000001',
                'address' => 'Ruang Admin Perpustakaan',
            ],
        );

        $members = [
            ['username' => 'budi', 'name' => 'Budi Santoso', 'phone' => '081200000002', 'address' => 'Jl. Melati No. 10'],
            ['username' => 'sinta', 'name' => 'Sinta Maharani', 'phone' => '081200000003', 'address' => 'Jl. Kenanga No. 25'],
            ['username' => 'rina', 'name' => 'Rina Kartika', 'phone' => '081200000004', 'address' => 'Jl. Anggrek No. 7'],
        ];

        foreach ($members as $member) {
            $user = User::updateOrCreate(
                ['username' => $member['username']],
                [
                    'name' => $member['name'],
                    'password' => 'Perpusa123!',
                    'role' => User::ROLE_USER,
                    'is_active' => true,
                    'phone' => $member['phone'],
                    'address' => $member['address'],
                ],
            );

            if (! $user->memberCard()->exists()) {
                $memberCards->issue($user);
            }
        }

        // Admin accounts intentionally do not receive a member card.
    }
}
