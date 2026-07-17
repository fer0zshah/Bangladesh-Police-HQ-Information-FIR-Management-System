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

        $this->seedFirDictionaryCases();
        $this->seedComplaintDictionaryData();
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

    private function seedFirDictionaryCases(): void
    {
        $casesByStation = [
            'Ramna Model Thana' => [
                ['Ramna Park Mobile Snatching Ring', 'Under Investigation', 8],
                ['Hotel Intercontinental Fraud Complaint', 'Pending', 17],
            ],
            'Shahbag Thana' => [
                ['Shahbag Intersection Assault Case', 'Under Investigation', 12],
                ['DU Campus Bicycle Theft Series', 'Closed', 35],
            ],
            'Dhanmondi Thana' => [
                ['Dhanmondi 27 Apartment Burglary', 'Pending', 5],
                ['Rapa Plaza Extortion Attempt', 'Under Investigation', 22],
            ],
            'Gulshan Thana' => [
                ['Gulshan Avenue Cyber Fraud', 'Under Investigation', 9],
                ['Diplomatic Zone Vehicle Theft', 'Transferred', 41],
            ],
            'Kotwali Thana' => [
                ['Kotwali Market Gold Shop Theft', 'Under Investigation', 14],
                ['Laldighi Area Assault FIR', 'Closed', 52],
            ],
            'Panchlaish Thana' => [
                ['Panchlaish Clinic Forgery Case', 'Pending', 18],
                ['Nasirabad Road Robbery FIR', 'Under Investigation', 27],
            ],
            'Khulna Sadar Thana' => [
                ['Khulna Sadar River Port Smuggling', 'Under Investigation', 11],
                ['Picture Palace Area Mugging Case', 'Closed', 48],
            ],
            'Sonadanga Thana' => [
                ['Sonadanga Bus Terminal Pickpocket Gang', 'Pending', 7],
                ['KDA Avenue Shop Vandalism', 'Under Investigation', 26],
            ],
            'Savar Thana' => [
                ['Savar EPZ Wage Fraud Complaint', 'Under Investigation', 16],
                ['Hemayetpur Highway Robbery', 'Pending', 31],
            ],
            'Keraniganj Model Thana' => [
                ['Keraniganj River Dock Cargo Theft', 'Under Investigation', 13],
                ['Ati Bazar Land Dispute Assault', 'Closed', 39],
            ],
            'Narail Sadar Thana' => [
                ['Narail Sadar Motorcycle Theft FIR', 'Pending', 10],
                ['Rupganj Bazaar Clash Case', 'Closed', 44],
            ],
            'Lohagara Thana' => [
                ['Lohagara Rural Road Robbery', 'Under Investigation', 21],
            ],
            'Rupsha Thana' => [
                ['Rupsha Bridge Toll Extortion', 'Under Investigation', 15],
            ],
            'Dumuria Thana' => [
                ['Dumuria Shrimp Enclosure Arson', 'Pending', 24],
            ],
            'Bogura Sadar Thana' => [
                ['Bogura Sadar Jewellery Shop Robbery', 'Under Investigation', 6],
                ['Satmatha Area Cyber Blackmail', 'Pending', 19],
            ],
            'Sherpur Thana' => [
                ['Sherpur Highway Bus Robbery', 'Under Investigation', 28],
            ],
            'Nilphamari Sadar Thana' => [
                ['Nilphamari Sadar Cattle Theft FIR', 'Pending', 20],
            ],
            'Saidpur Thana' => [
                ['Saidpur Railway Colony Assault', 'Closed', 55],
            ],
        ];

        foreach ($casesByStation as $stationName => $caseRows) {
            $station = Station::where('name', $stationName)->first();

            if (! $station) {
                continue;
            }

            $investigator = Officer::firstOrCreate(
                ['badge_number' => 'INV-'.str_pad((string) $station->station_id, 4, '0', STR_PAD_LEFT)],
                [
                    'station_id' => $station->station_id,
                    'name' => 'Inspector '.$station->name,
                    'rank' => 'Inspector',
                    'status' => 'Active',
                    'is_oc' => false,
                ]
            );

            foreach ($caseRows as [$title, $status, $daysAgo]) {
                CaseFir::updateOrCreate(
                    [
                        'station_id' => $station->station_id,
                        'case_title' => $title,
                    ],
                    [
                        'investigating_officer_id' => $investigator->officer_id,
                        'complaint_id' => null,
                        'date_filed' => now()->subDays($daysAgo)->toDateString(),
                        'status' => $status,
                    ]
                );
            }
        }
    }

    private function seedComplaintDictionaryData(): void
    {
        $complaintsByStation = [
            'Ramna Model Thana' => [
                ['Farzana Akter', '3001000000011', 'Phone snatching near Ramna Park south gate.', 'Pending', 3],
                ['Md. Rashed Karim', '3001000000012', 'Noise and intimidation complaint from a local business.', 'Under Review', 9],
            ],
            'Shahbag Thana' => [
                ['Nusrat Tabassum', '3001000000021', 'Lost bag and suspected theft near museum gate.', 'Pending', 4],
                ['Abdul Malek', '3001000000022', 'Assault complaint after a roadside dispute.', 'Escalated', 13],
            ],
            'Dhanmondi Thana' => [
                ['Sadia Rahman', '3001000000031', 'Harassment complaint near Dhanmondi Lake walkway.', 'Under Review', 6],
                ['Tanvir Hasan', '3001000000032', 'Shop burglary attempt reported from Road 27.', 'Escalated', 15],
            ],
            'Gulshan Thana' => [
                ['Maliha Chowdhury', '3001000000041', 'Online financial fraud complaint linked to a courier payment.', 'Under Review', 5],
                ['Imran Hossain', '3001000000042', 'Vehicle vandalism complaint near Gulshan Avenue.', 'Dismissed', 20],
            ],
            'Kotwali Thana' => [
                ['Sanjida Islam', '3001000000051', 'Market pickpocket complaint near Kotwali crossing.', 'Pending', 7],
                ['Mohammad Yusuf', '3001000000052', 'Gold shop employee intimidation complaint.', 'Escalated', 16],
            ],
            'Panchlaish Thana' => [
                ['Jannatul Ferdous', '3001000000061', 'Clinic document forgery complaint.', 'Under Review', 8],
                ['Arman Siddique', '3001000000062', 'Apartment parking assault complaint.', 'Pending', 18],
            ],
            'Khulna Sadar Thana' => [
                ['Rafiq Ahmed', '3001000000071', 'River port cargo missing complaint.', 'Escalated', 11],
                ['Mst. Salma Begum', '3001000000072', 'Neighborhood disturbance complaint.', 'Pending', 22],
            ],
            'Sonadanga Thana' => [
                ['Parvez Alam', '3001000000081', 'Bus terminal wallet theft complaint.', 'Under Review', 10],
                ['Tahmina Sultana', '3001000000082', 'Shop damage complaint after local dispute.', 'Pending', 19],
            ],
            'Savar Thana' => [
                ['Rubel Mia', '3001000000091', 'Factory wage fraud complaint from EPZ worker group.', 'Under Review', 12],
                ['Nadia Islam', '3001000000092', 'Highway robbery complaint near Hemayetpur.', 'Escalated', 21],
            ],
            'Keraniganj Model Thana' => [
                ['Shakil Ahmed', '3001000000101', 'Cargo theft complaint from river dock.', 'Escalated', 14],
                ['Rokeya Begum', '3001000000102', 'Land dispute threat complaint.', 'Pending', 25],
            ],
            'Narail Sadar Thana' => [
                ['Biplob Biswas', '3001000000111', 'Motorcycle theft complaint from sadar bazar.', 'Under Review', 6],
                ['Aklima Khatun', '3001000000112', 'Local clash complaint near union road.', 'Dismissed', 34],
            ],
            'Lohagara Thana' => [
                ['Monirul Islam', '3001000000121', 'Rural road robbery complaint.', 'Escalated', 17],
            ],
            'Rupsha Thana' => [
                ['Hasib Khan', '3001000000131', 'Extortion complaint near Rupsha Bridge approach road.', 'Under Review', 9],
            ],
            'Dumuria Thana' => [
                ['Mahmuda Akter', '3001000000141', 'Shrimp enclosure arson threat complaint.', 'Pending', 23],
            ],
            'Bogura Sadar Thana' => [
                ['Sabbir Rahman', '3001000000151', 'Jewellery shop robbery witness complaint.', 'Escalated', 7],
                ['Fahmida Yeasmin', '3001000000152', 'Cyber blackmail complaint from Satmatha area.', 'Under Review', 18],
            ],
            'Sherpur Thana' => [
                ['Delwar Hossain', '3001000000161', 'Bus robbery complaint on highway route.', 'Pending', 12],
            ],
            'Nilphamari Sadar Thana' => [
                ['Mizanur Rahman', '3001000000171', 'Cattle theft complaint from village market.', 'Pending', 13],
            ],
            'Saidpur Thana' => [
                ['Anika Saha', '3001000000181', 'Railway colony assault complaint.', 'Under Review', 28],
            ],
        ];

        foreach ($complaintsByStation as $stationName => $complaintRows) {
            $station = Station::where('name', $stationName)->first();

            if (! $station) {
                continue;
            }

            foreach ($complaintRows as [$name, $nid, $description, $status, $daysAgo]) {
                CitizenComplaint::updateOrCreate(
                    ['complainant_nid' => $nid],
                    [
                        'station_id' => $station->station_id,
                        'complainant_name' => $name,
                        'description' => $description,
                        'submitted_date' => now()->subDays($daysAgo)->toDateString(),
                        'status' => $status,
                    ]
                );
            }
        }
    }
}
