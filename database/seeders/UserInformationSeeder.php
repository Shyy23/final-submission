<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\StudyProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Position::Create(['position_name' => 'Wakil Dekan 1']);

        StudyProgram::Create(['study_name' => 'Teknik Informatika']);
        StudyProgram::Create(['study_name' => 'Sistem Informasi']);
        StudyProgram::Create(['study_name' => 'Kimia']);
    }
}
