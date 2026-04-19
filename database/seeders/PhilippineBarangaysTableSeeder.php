<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class PhilippineBarangaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!\DB::table('philippine_barangays')->count()) {
            $sql = file_get_contents(__DIR__ . '/sql/philippine_barangays.sql');
            $queries = array_filter(array_map('trim', explode(";", $sql)));

            foreach ($queries as $query) {
                if (!empty($query)) {
                    \DB::unprepared($query . ";");
                }
            }
        }
    }
}