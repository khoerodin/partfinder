<?php

namespace App\Policies;

use App\User;
use App\PartNumber;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartNumberPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isSuper()) return true;
    }

    public function partNumberView(User $user)
    {
        return $user->hasPermission('partnumber.view');
    }

    public function partNumberSearch(User $user)
    {
        return $user->hasPermission('partnumber.search');
    }

    public function partNumberDownload(User $user)
    {
        return $user->hasPermission('partnumber.download');
    }

    public function importView(User $user)
    {
        return $user->hasPermission('import.view');
    }

    public function importImport(User $user)
    {
        return $user->hasPermission('import.import');
    }

    public function userView(User $user)
    {
        return $user->hasPermission('user.view');
    }

    public function roleView(User $user)
    {
        return $user->hasPermission('role.view');
    }
}
