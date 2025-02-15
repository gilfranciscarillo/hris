<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use App\Models\UserModel;

class AddDefaultSuperAdminUser extends Seeder
{
    public function run()
    {
        /** @var UserModel $userModel */
        $userModel = model(UserModel::class);
        $adminUser = $userModel
                        ->select('*')
                        ->join('auth_identities', 'auth_identities.user_id = users.id')
                        ->where('auth_identities.secret', 'hris_admin@example.com')
                        ->get()->getRow();
        
        if ($adminUser) {
            return;
        }

        $user = new User([
            'email' => 'admin@example.com',
            'password' => 'hris@admin',
            'first_name' => 'Administrator'
        ]);   
        
        $userModel->save($user);

        $user = $userModel->findById($userModel->getInsertID());
        $user->activate();
        $user->addGroup('superadmin');
    }
}
