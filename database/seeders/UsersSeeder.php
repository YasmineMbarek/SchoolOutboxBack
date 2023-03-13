<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Provider\Base;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::query()->where('name', '=', Role::ROLE_SUPERADMIN)->first();

        $u = new User();
        $u->role_id = $role->id;
        $u->first_name = 'Super';
        $u->last_name = 'Admin';
        $u->email = 'superAdmin@gmail.com';
        $u->password = Hash::make('password');
        $u->save();
    }
}
