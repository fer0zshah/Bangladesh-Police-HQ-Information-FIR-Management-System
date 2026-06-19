<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Station;
use App\Models\Officer;
use App\Models\CitizenComplaint;
use App\Models\Criminal;
use App\Models\CaseFir;
use App\Models\Evidence;
use Illuminate\Support\Facades\DB;

class PoliceHqSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Stations
        $dhakaCentral = Station::create([
            'name' => 'Dhaka Central Police Station',
            'district' => 'Dhaka',
            'address' => 'Ramna, Dhaka 1000',
            'contact_number' => '+8801711111111'
        ]);

        $ctgPort = Station::create([
            'name' => 'Chittagong Port Police Station',
            'district' => 'Chittagong',
            'address' => 'Port Area, Chittagong 4100',
            'contact_number' => '+8801722222222'
        ]);

        // 2. Seed Officers
        $officer1 = Officer::create([
            'station_id' => $dhakaCentral->station_id,
            'name' => 'Inspector Arif Rahman',
            'badge_number' => 'BP-98745',
            'rank' => 'Inspector',
            'status' => 'Active'
        ]);

        $officer2 = Officer::create([
            'station_id' => $ctgPort->station_id,
            'name' => 'Sub-Inspector Fahmida Akter',
            'badge_number' => 'BP-65412',
            'rank' => 'Sub-Inspector',
            'status' => 'Active'
        ]);

        // 3. Seed Criminals
        $criminal1 = Criminal::create([
            'nid_number' => '1995261728394',
            'name' => 'Kalam Mia',
            'alias' => 'Kala Jahangir',
            'date_of_birth' => '1985-05-12',
            'wanted_status' => true
        ]);

        // 4. Seed a Citizen Complaint
        $complaint = CitizenComplaint::create([
            'station_id' => $dhakaCentral->station_id,
            'complainant_name' => 'Md. Asif Islam',
            'complainant_nid' => '2001261948576',
            'description' => 'Armed robbery occurred near Ramna Park at 9:00 PM.',
            'submitted_date' => now()->subDays(2),
            'status' => 'Escalated'
        ]);

        // 5. Seed a Case/FIR linked to the complaint and officer
        $case = CaseFir::create([
            'station_id' => $dhakaCentral->station_id,
            'investigating_officer_id' => $officer1->officer_id,
            'complaint_id' => $complaint->complaint_id,
            'case_title' => 'Ramna Park Armed Robbery',
            'date_filed' => now()->subDay(),
            'status' => 'Under Investigation'
        ]);

        // 6. Link Criminal to Case via Many-to-Many Bridging Table
        DB::table('case_criminals')->insert([
            'case_id' => $case->case_id,
            'criminal_id' => $criminal1->criminal_id,
            'involvement_type' => 'Prime Suspect',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 7. Seed Evidence linked to the case
        Evidence::create([
            'case_id' => $case->case_id,
            'officer_id' => $officer1->officer_id,
            'type' => 'Weapon',
            'description' => 'Local knife recovered from the crime scene.',
            'collected_date' => now()->subDay()
        ]);
    }
}