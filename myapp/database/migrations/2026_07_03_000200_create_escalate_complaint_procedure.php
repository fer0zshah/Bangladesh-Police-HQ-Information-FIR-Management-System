<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS escalate_complaint_to_fir');
        DB::unprepared(<<<'SQL'
CREATE PROCEDURE escalate_complaint_to_fir(
    IN p_complaint_id BIGINT UNSIGNED,
    IN p_station_id BIGINT UNSIGNED,
    IN p_officer_id BIGINT UNSIGNED,
    IN p_case_title VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM citizen_complaints
        WHERE complaint_id = p_complaint_id
          AND station_id = p_station_id
          AND LOWER(status) = 'under review'
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Complaint must be under review and belong to the OC station';
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM officers
        WHERE officer_id = p_officer_id
          AND station_id = p_station_id
          AND LOWER(status) = 'active'
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Investigating officer must be active and assigned to the OC station';
    END IF;

    IF EXISTS (SELECT 1 FROM case_firs WHERE complaint_id = p_complaint_id) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'An FIR already exists for this complaint';
    END IF;

    INSERT INTO case_firs (
        station_id, investigating_officer_id, complaint_id,
        case_title, date_filed, status, created_at, updated_at
    ) VALUES (
        p_station_id, p_officer_id, p_complaint_id,
        p_case_title, CURRENT_DATE, 'Pending', NOW(), NOW()
    );

    UPDATE citizen_complaints
    SET status = 'Escalated', updated_at = NOW()
    WHERE complaint_id = p_complaint_id;
END
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP PROCEDURE IF EXISTS escalate_complaint_to_fir');
        }
    }
};
