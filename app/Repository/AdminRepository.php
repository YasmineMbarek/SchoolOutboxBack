<?php

namespace App\Repository;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class AdminRepository
{
    public static function listing($perPage = 10, $page = 1, $search = null, $orderColumn = null, $orderDirection = null)
    {
        $users = User::query()->with('role', 'region');
        if ($search) {
            $users->where('first_name', 'like', '%' . $search . '%')
                ->orwhere('last_name', 'like', '%' . $search . '%');
        }

        if ($orderColumn && $orderDirection) {
            if ($orderColumn === 'region') {
                $users->orderBy('region_id', $orderDirection);
            } elseif ($orderColumn === 'role') {
                $users->orderBy('role_id', $orderDirection);
            } else {
                $users->orderBy($orderColumn, $orderDirection);
            }
        }

        return $users->paginate($perPage, ['*'], 'page', $page);
    }

    public static function register(string $regionId, string $roleId, string $firstName, string $lastName, string $email, string $password)
    {
        $admin = new User();
        $role = Role::query()->find($roleId);
        $admin->role_id = $roleId;
        if ($role->name == Role::ROLE_ADMIN) {
            $admin->region_id = $regionId;
        }
        $admin->first_name = $firstName;
        $admin->last_name = $lastName;
        $admin->email = $email;
        $admin->password = Hash::make($password);
        $admin->save();
        self::mail($password, $admin);
        return $admin;
    }

    public static function update($admin, string $regionId, string $roleId, string $firstName, string $lastName, string $email, string $password): bool
    {
        $role = Role::query()->find($roleId);
        if ($role->name == Role::ROLE_ADMIN) {
            $admin->region_id = $regionId;
        }
        $admin->role_id = $roleId;
        $admin->first_name = $firstName;
        $admin->last_name = $lastName;
        $admin->email = $email;
        $admin->password = Hash::make($password);
        $admin->save();


        return $admin->save();
    }

    public static function mail(string $password, $admin)
    {
        Mail::raw('hello this your password:' . $password, function ($message) use ($admin) {
            $message->to($admin->email);
        });
    }

    public static function updateProfile($admin, string $firstName, string $lastName, string $email)
    {
        $admin->first_name = $firstName;
        $admin->last_name = $lastName;
        $admin->email = $email;
        $admin->save();
        return $admin;
    }

    public static function changePassword($admin, string $password)
    {
        $admin->password = Hash::make($password);
        $admin->save();
        return $admin;
    }


}
