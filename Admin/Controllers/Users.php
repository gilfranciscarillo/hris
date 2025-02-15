<?php

namespace Admin\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

use CodeIgniter\HTTP\ResponseInterface;

class Users extends BaseController
{
    protected UserModel $userModel;
    public function __construct()
    {
        $this->userModel = auth()->getProvider();    
    }

    public function index()
    {
        return view('Admin\Views\Users\index', [
            'users' => $this->userModel->findAll()
        ]);        
    }

    public function addToGroup(int $userId, string $group)
    {
        pageExists($userId);

        /** @var \App\Entities\UserEntity $user */
        $user = $this->userModel->find($userId);
        if (!$user->inGroup($group)) $user->addGroup($group);

        return redirect()->back()
            ->with('message', 'User successfully added to the group');
    }

    public function removeFromGroup(int $userId, string $group)
    {
        pageExists($userId);

        /** @var \App\Entities\UserEntity $user */
        $user = $this->userModel->find($userId);
        if ($user->inGroup($group)) $user->removeGroup($group);

        return redirect()->back()
            ->with('message', 'User successfully removed from the group');
    }
}
