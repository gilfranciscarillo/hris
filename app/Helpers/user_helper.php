<?php

use CodeIgniter\Exceptions\PageNotFoundException;

if (!function_exists('getCurrentUser')) {
    function getCurrentUser(): \App\Entities\UserEntity
    {
        return auth()->user();
    }
}

if (!function_exists('pageExists')) {
    function pageExists(int|bool $userId): bool | PageNotFoundException
    {   
        if (!$userId || getCurrentUser()->cannotAccessOtherRecord($userId)) {
            throw new PageNotFoundException('Page not found');
        }

        return true;
    }
}