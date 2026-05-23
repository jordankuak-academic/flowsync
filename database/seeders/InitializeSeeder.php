<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class InitializeSeeder extends Seeder {
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $now = now();

        DB::table("roles")->insert([
            [
                "name" => "supervisor",
                "description" => "The supervisor role of a department that handle a team of staff",
                "is_active" => true,
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "name" => "staff",
                "description" => "The staff role of a department that work in a team",
                "is_active" => true,
                "created_at" => $now,
                "updated_at" => $now,
            ]
        ]); 
        
        DB::table("departments")->insert([
            [
                "name" => "Information Technology Department",
                "description" => "The department that handle the information technology process",
                "code" => "ITD",
                "is_active" => true,
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "name" => "Marketing Department",
                "description" => "The department that handle the marketing process",
                "code" => "MKD",
                "is_active" => true,
                "created_at" => $now,
                "updated_at" => $now,
            ],
            [
                "name" => "Service Department",
                "description" => "The department that handle the service process",
                "code" => "SCD",
                "is_active" => true,
                "created_at" => $now,
                "updated_at" => $now,
            ]
        ]);
        
        DB::table("users")->insert([
            "role_id" => 1,
            "staff_id" => "000001",
            "name" => "Developer 01",
            "username" => "developer01",
            "email" => "developer01@example.com",
            "password" => Hash::make("raffles@123"),
            "job_title" => "Software Developer",
            "ic_number" => "900001000000",
            "contact" => "01234567890",
            "is_active" => true,
            "created_at" => $now,
            "updated_at" => $now,
        ]);
        
        DB::table("teams")->insert([
            "department_id" => 1,
            "leader_id" => 1,
            "member_id" => null,
            "is_active" => true,
            "created_at" => $now,
            "updated_at" => $now,
        ]);
    }
}
