<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Database\Seeds\AddDefaultSuperAdminUser;

class RunAllSeed extends Seeder
{
    public function run()
    {
        $this->call(AddDefaultSuperAdminUser::class);
    }
}
