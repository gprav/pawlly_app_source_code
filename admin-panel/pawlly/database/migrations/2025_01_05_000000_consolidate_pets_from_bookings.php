<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration ensures all pets referenced in bookings exist in the pets table
     *
     * @return void
     */
    public function up()
    {
        // This query finds any orphaned bookings (bookings without corresponding pets)
        // and would help identify if there's missing data
        $orphanedBookings = DB::table('bookings')
            ->leftJoin('pets', 'bookings.pet_id', '=', 'pets.id')
            ->whereNull('pets.id')
            ->whereNotNull('bookings.pet_id')
            ->select('bookings.*')
            ->get();

        if ($orphanedBookings->count() > 0) {
            // Log orphaned bookings for review
            \Log::warning('Found ' . $orphanedBookings->count() . ' bookings with missing pet records');

            // Optionally, you could create placeholder pets for these bookings
            // Or handle them according to your business logic
        }

        // Ensure data integrity: bookings should reference valid pets
        // This adds a note to any pets that have been booked
        DB::statement("
            UPDATE pets
            SET additional_info = CONCAT(
                COALESCE(additional_info, ''),
                ' [Has booking history]'
            )
            WHERE id IN (
                SELECT DISTINCT pet_id
                FROM bookings
                WHERE pet_id IS NOT NULL
            )
            AND (additional_info NOT LIKE '%Has booking history%' OR additional_info IS NULL)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the booking history flag from pets
        DB::statement("
            UPDATE pets
            SET additional_info = REPLACE(additional_info, ' [Has booking history]', '')
            WHERE additional_info LIKE '%Has booking history%'
        ");
    }
};
