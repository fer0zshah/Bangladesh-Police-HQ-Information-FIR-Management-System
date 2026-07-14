<?php

namespace Database\Seeders;

use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Criminal;
use App\Models\Evidence;
use App\Models\Officer;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PoliceHqSeeder extends Seeder
{
    public function run(): void
    {
        $hq = $this->station('Bangladesh Police Headquarters', [
            'type' => 'hq',
            'parent_id' => null,
            'district' => null,
            'division' => 'Dhaka',
            'head_rank' => 'IGP',
            'address' => 'Police Headquarters, Dhaka',
            'contact_number' => null,
            'jurisdiction' => 'All of Bangladesh',
            'status' => 'Active',
            'is_active' => true,
        ]);

        $metroHqs = [
            'Dhaka Metropolitan Police' => [
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'address' => 'DMP Headquarters, Dhaka',
                'thanas' => [
                    'Ramna Model', 'Shahbag', 'Dhanmondi', 'New Market', 'Gulshan',
                    'Banani', 'Badda', 'Mohammadpur', 'Mirpur Model', 'Pallabi',
                    'Uttara Model', 'Uttara West', 'Airport', 'Tejgaon', 'Motijheel',
                    'Paltan Model', 'Wari', 'Lalbag', 'Kotwali', 'Hazaribag',
                ],
            ],
            'Chattogram Metropolitan Police' => [
                'division' => 'Chattogram',
                'district' => 'Chattogram',
                'address' => 'CMP Headquarters, Chattogram',
                'thanas' => [
                    'Kotwali', 'Chandgaon', 'Panchlaish', 'Double Mooring', 'Pahartali',
                    'Bandar', 'Bayezid Bostami', 'Halishahar', 'Karnaphuli', 'Patenga',
                    'Bakalia', 'Akbar Shah', 'Sadarghat', 'EPZ', 'Chawkbazar', 'Khulshi',
                ],
            ],
            'Khulna Metropolitan Police' => [
                'division' => 'Khulna',
                'district' => 'Khulna',
                'address' => 'KMP Headquarters, Khulna',
                'thanas' => [
                    'Khulna Sadar', 'Sonadanga', 'Khalishpur', 'Daulatpur',
                    'Khan Jahan Ali', 'Labanchara', 'Harintana', 'Aranghata',
                ],
            ],
        ];

        $districtHqs = [
            'Dhaka District Police' => [
                'division' => 'Dhaka',
                'district' => 'Dhaka',
                'address' => 'Dhaka District Police Office',
                'thanas' => [
                    'Dhamrai', 'Savar', 'Ashulia', 'Keraniganj Model',
                    'Keraniganj South', 'Nawabganj', 'Dohar',
                ],
            ],
            'Narail District Police' => [
                'division' => 'Khulna',
                'district' => 'Narail',
                'address' => 'Narail District Police Office',
                'thanas' => ['Narail Sadar', 'Lohagara', 'Kalia', 'Naragati'],
            ],
            'Khulna District Police' => [
                'division' => 'Khulna',
                'district' => 'Khulna',
                'address' => 'Khulna District Police Office',
                'thanas' => [
                    'Rupsha', 'Terokhada', 'Digholia', 'Fultala', 'Dumuria',
                    'Batiaghata', 'Dacope', 'Paikgacha', 'Koyra',
                ],
            ],
            'Bogura District Police' => [
                'division' => 'Rajshahi',
                'district' => 'Bogura',
                'address' => 'Bogura District Police Office',
                'thanas' => [
                    'Bogura Sadar', 'Sherpur', 'Shibganj', 'Gabtali', 'Dhunat',
                    'Shajahanpur', 'Sonatala', 'Adamdighi', 'Dupchanchia',
                    'Kahalu', 'Nandigram', 'Sariakandi',
                ],
            ],
            'Nilphamari District Police' => [
                'division' => 'Rangpur',
                'district' => 'Nilphamari',
                'address' => 'Nilphamari District Police Office',
                'thanas' => ['Nilphamari Sadar', 'Saidpur', 'Jaldhaka', 'Kishoreganj', 'Domar', 'Dimla'],
            ],
        ];

        $dhakaMetro = null;
        $dhakaDistrict = null;
        $dhanmondiThana = null;

        foreach ($metroHqs as $name => $data) {
            $metro = $this->station($name, [
                'type' => 'metropolitanHQ',
                'parent_id' => $hq->station_id,
                'district' => $data['district'],
                'division' => $data['division'],
                'head_rank' => 'Police Commissioner',
                'address' => $data['address'],
                'jurisdiction' => $name,
                'status' => 'Active',
                'is_active' => true,
            ]);

            if ($name === 'Dhaka Metropolitan Police') {
                $dhakaMetro = $metro;
            }

            foreach ($data['thanas'] as $thanaName) {
                $thana = $this->station("{$thanaName} Thana", [
                    'type' => 'thana',
                    'parent_id' => $metro->station_id,
                    'district' => $data['district'],
                    'division' => $data['division'],
                    'head_rank' => 'OC',
                    'address' => "{$thanaName}, {$data['district']}",
                    'jurisdiction' => "{$thanaName} thana area",
                    'status' => 'Active',
                    'is_active' => true,
                ]);

                if ($thanaName === 'Dhanmondi') {
                    $dhanmondiThana = $thana;
                }
            }
        }

        foreach ($districtHqs as $name => $data) {
            $district = $this->station($name, [
                'type' => 'districtHQ',
                'parent_id' => $hq->station_id,
                'district' => $data['district'],
                'division' => $data['division'],
                'head_rank' => 'SP',
                'address' => $data['address'],
                'jurisdiction' => "{$data['district']} district",
                'status' => 'Active',
                'is_active' => true,
            ]);

            if ($name === 'Dhaka District Police') {
                $dhakaDistrict = $district;
            }

            foreach ($data['thanas'] as $thanaName) {
                $this->station("{$thanaName} Thana", [
                    'type' => 'thana',
                    'parent_id' => $district->station_id,
                    'district' => $data['district'],
                    'division' => $data['division'],
                    'head_rank' => 'OC',
                    'address' => "{$thanaName}, {$data['district']}",
                    'jurisdiction' => "{$thanaName} thana area",
                    'status' => 'Active',
                    'is_active' => true,
                ]);
            }
        }

        $commissioner = $this->officer($dhakaMetro, 'Commissioner Md. Farid Uddin', 'HQ-DMP-001', 'Police Commissioner');
        $sp = $this->officer($dhakaDistrict, 'SP Nusrat Jahan', 'HQ-DHK-001', 'Superintendent of Police');
        $oc = $this->officer($dhanmondiThana, 'OC Dhanmondi Inspector Arif Rahman', 'DMP-DHN-001', 'Inspector', true);

        $this->user('igp@police.gov.bd', [
            'name' => 'Inspector General of Police',
            'nid_number' => '1000000001',
            'phone' => '01700000001',
            'role' => 'super_admin',
            'station_id' => $hq->station_id,
            'officer_id' => null,
        ]);

        $this->user('commissioner.dhaka@police.gov.bd', [
            'name' => $commissioner->name,
            'nid_number' => '1000000002',
            'phone' => '01700000002',
            'role' => 'metro_head',
            'station_id' => $dhakaMetro->station_id,
            'officer_id' => $commissioner->officer_id,
        ]);

        $this->user('sp.dhaka@police.gov.bd', [
            'name' => $sp->name,
            'nid_number' => '1000000003',
            'phone' => '01700000003',
            'role' => 'district_head',
            'station_id' => $dhakaDistrict->station_id,
            'officer_id' => $sp->officer_id,
        ]);

        $ocUser = $this->user('oc.dhanmondi@police.gov.bd', [
            'name' => $oc->name,
            'nid_number' => '1000000004',
            'phone' => '01700000004',
            'role' => 'station_oc',
            'station_id' => $dhanmondiThana->station_id,
            'officer_id' => $oc->officer_id,
        ]);

        $oc->update([
            'user_id' => $ocUser->id,
            'is_oc' => true,
        ]);

        $this->user('citizen@test.com', [
            'name' => 'Test Citizen',
            'nid_number' => '1000000005',
            'phone' => '01700000005',
            'role' => 'citizen',
            'station_id' => null,
            'officer_id' => null,
        ]);

        $criminal = Criminal::updateOrCreate(
            ['nid_number' => '1995261728394'],
            [
                'name' => 'Kalam Mia',
                'alias' => 'Kala Jahangir',
                'date_of_birth' => '1985-05-12',
                'wanted_status' => true,
            ]
        );

        $complaint = CitizenComplaint::updateOrCreate(
            ['complainant_nid' => '2001261948576'],
            [
                'station_id' => $dhanmondiThana->station_id,
                'complainant_name' => 'Md. Asif Islam',
                'description' => 'Armed robbery occurred near Dhanmondi Lake at 9:00 PM.',
                'submitted_date' => now()->subDays(2)->toDateString(),
                'status' => 'Escalated',
            ]
        );

        $case = CaseFir::updateOrCreate(
            ['complaint_id' => $complaint->complaint_id],
            [
                'station_id' => $dhanmondiThana->station_id,
                'investigating_officer_id' => $oc->officer_id,
                'case_title' => 'Dhanmondi Lake Armed Robbery',
                'date_filed' => now()->subDay()->toDateString(),
                'status' => 'Under Investigation',
            ]
        );

        DB::table('case_criminals')->updateOrInsert(
            ['case_id' => $case->case_id, 'criminal_id' => $criminal->criminal_id],
            [
                'involvement_type' => 'Prime Suspect',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Evidence::updateOrCreate(
            ['case_id' => $case->case_id, 'type' => 'Weapon'],
            [
                'officer_id' => $oc->officer_id,
                'description' => 'Local knife recovered from the crime scene.',
                'collected_date' => now()->subDay()->toDateString(),
            ]
        );
    }

    private function station(string $name, array $attributes): Station
    {
        return Station::updateOrCreate(['name' => $name], $attributes);
    }

    private function officer(Station $station, string $name, string $badgeNumber, string $rank, bool $isOc = false): Officer
    {
        return Officer::updateOrCreate(
            ['badge_number' => $badgeNumber],
            [
                'station_id' => $station->station_id,
                'name' => $name,
                'rank' => $rank,
                'status' => 'Active',
                'is_oc' => $isOc,
            ]
        );
    }

    private function user(string $email, array $attributes): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                ...$attributes,
                'password' => Hash::make('password'),
            ]
        );
    }
}
