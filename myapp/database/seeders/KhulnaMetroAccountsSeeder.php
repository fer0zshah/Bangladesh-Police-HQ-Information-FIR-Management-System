<?php

namespace Database\Seeders;

use App\Models\Officer;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KhulnaMetroAccountsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $hq = Station::updateOrCreate(
                ['name' => 'Bangladesh Police Headquarters'],
                [
                    'type' => 'hq', 'parent_id' => null, 'district' => null,
                    'division' => 'Dhaka', 'head_rank' => 'IGP',
                    'address' => 'Police Headquarters, Dhaka',
                    'jurisdiction' => 'All of Bangladesh', 'status' => 'Active', 'is_active' => true,
                ]
            );

            $metro = Station::updateOrCreate(
                ['name' => 'Khulna Metropolitan Police'],
                [
                    'type' => 'metropolitanHQ', 'parent_id' => $hq->station_id,
                    'district' => 'Khulna', 'division' => 'Khulna',
                    'head_rank' => 'Police Commissioner', 'address' => 'KMP Headquarters, Khulna',
                    'jurisdiction' => 'Khulna Metropolitan Area', 'status' => 'Active', 'is_active' => true,
                ]
            );

            $commissioner = Officer::updateOrCreate(
                ['badge_number' => 'HQ-KMP-001'],
                ['station_id' => $metro->station_id, 'name' => 'Commissioner Md. Tariqul Islam', 'rank' => 'Police Commissioner', 'status' => 'Active', 'is_oc' => false]
            );
            $commissionerUser = $this->account('commissioner.khulna@police.gov.bd', $commissioner, 'metro_head', '1100000001', '01711000001');
            $commissioner->update(['user_id' => $commissionerUser->id]);

            $teams = [
                'Khulna Sadar' => ['khulnasadar', 'OC Khulna Sadar Inspector Mizanur Rahman', ['Inspector Biplob Kumar', 'Inspector Ayesha Siddika']],
                'Sonadanga' => ['sonadanga', 'OC Sonadanga Inspector Shahidul Islam', ['Inspector Tanvir Hossain', 'Inspector Ruma Khatun']],
                'Khalishpur' => ['khalishpur', 'OC Khalishpur Inspector Mahfuzur Rahman', ['Inspector Sadia Islam', 'Inspector Rezaul Kabir']],
                'Daulatpur' => ['daulatpur', 'OC Daulatpur Inspector Humayun Kabir', ['Inspector Moinul Hasan', 'Inspector Farzana Akter']],
                'Khan Jahan Ali' => ['khanjahanali', 'OC Khan Jahan Ali Inspector Saiful Islam', ['Inspector Nusrat Jahan', 'Inspector Tanvir Ahmed']],
                'Labanchara' => ['labanchara', 'OC Labanchara Inspector Abdul Mannan', ['Inspector Sharmeen Sultana', 'Inspector Rakib Hossain']],
                'Harintana' => ['harintana', 'OC Harintana Inspector Jahangir Alam', ['Inspector Masud Rana', 'Inspector Tania Rahman']],
                'Aranghata' => ['aranghata', 'OC Aranghata Inspector Kamal Hossain', ['Inspector Imran Hossain', 'Inspector Jannatul Ferdous']],
            ];

            $position = 0;
            foreach ($teams as $stationName => $data) {
                $position++;
                [$slug, $ocName, $investigators] = $data;
                $station = Station::updateOrCreate(
                    ['name' => "{$stationName} Thana"],
                    [
                        'type' => 'thana', 'parent_id' => $metro->station_id,
                        'district' => 'Khulna', 'division' => 'Khulna', 'head_rank' => 'OC',
                        'address' => "{$stationName}, Khulna", 'jurisdiction' => "{$stationName} thana area",
                        'status' => 'Active', 'is_active' => true,
                    ]
                );

                $oc = Officer::updateOrCreate(
                    ['badge_number' => 'KMP-OC-'.str_pad((string) $station->station_id, 4, '0', STR_PAD_LEFT)],
                    ['station_id' => $station->station_id, 'name' => $ocName, 'rank' => 'Inspector', 'status' => 'Active', 'is_oc' => true]
                );
                $ocUser = $this->account(
                    "oc.{$slug}@police.gov.bd", $oc, 'station_oc',
                    '11000001'.str_pad((string) $position, 2, '0', STR_PAD_LEFT),
                    '017120000'.str_pad((string) $position, 2, '0', STR_PAD_LEFT)
                );
                $oc->update(['user_id' => $ocUser->id]);

                foreach ($investigators as $investigatorIndex => $name) {
                    Officer::updateOrCreate(
                        ['badge_number' => 'KMP-INV-'.str_pad((string) $station->station_id, 4, '0', STR_PAD_LEFT).'-'.($investigatorIndex + 1)],
                        ['station_id' => $station->station_id, 'name' => $name, 'rank' => 'Inspector', 'status' => 'Active', 'is_oc' => false]
                    );
                }
            }
        });
    }

    private function account(string $email, Officer $officer, string $role, string $nid, string $phone): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $officer->name, 'nid_number' => $nid, 'phone' => $phone,
                'role' => $role, 'station_id' => $officer->station_id,
                'officer_id' => $officer->officer_id, 'password' => Hash::make('password'),
            ]
        );
    }
}
