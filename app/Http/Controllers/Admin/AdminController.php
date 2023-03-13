<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Repository\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function response;


class AdminController extends Controller

{
    public function listing(Request $request)
    {
        if ($request->ajax()) {

            $start = $request['start'];
            $perPage = $request['length'];
            $page = ($start / $perPage) + 1;
            $search = $request['search']['value'];
            $orderDirection = $request['order'][0]['dir'];
            $orderColumn = $request['columns'][$request['order'][0]['column']]['data'];

            $users = AdminRepository::listing($perPage, $page, $search, $orderColumn, $orderDirection);

            return response()->json(['draw' => null,
                'recordsTotal' => $users->total(),
                'recordsFiltered' => $users->total(),
                'data' => $users->items(),
            ]);
        }

        $regions = Region::all();

        return view('admins', compact('regions'));
    }

    public function store(CreateAdminRequest $request)
    {
        $regionId = $request->input('region_id');
        $roleId = Role::query()->where('name', '=', 'admin')->value('id');
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $password = Str::random(10);

        $role = Role::query()->firstWhere('id', '=', $roleId);
        if (!$role) {
            return response()->json(['message' => 'role do not exist'], 404);
        }

        $region = Region::query()->firstWhere('id', '=', $regionId);
        if (!$region) {
            return response()->json(['message' => 'region do not exist'], 404);
        }

        $admin = AdminRepository::register($regionId, $roleId, $firstName, $lastName, $email, $password);

        if (!$admin) {
            return response()->json(['message' => 'error creating admin'], 400);
        }

        return response()->json(['message' => 'admin created'], 201);
    }

    public function update(CreateAdminRequest $request, $adminId)
    {
        $admin = User::query()->find($adminId);
        if (!$admin) {
            return response()->json(['message' => 'admin did not exist'], 404);
        }

        $regionId = $request->input('region_id');
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $password = Str::random(10);
        $roleId = Role::query()->where('name', '=', 'admin')->value('id');
        $role = Role::query()->firstWhere('id', '=', $roleId);
        if (!$role) {
            return response()->json(['message' => 'role do not exist'], 404);
        }

        $region = Region::query()->firstWhere('id', '=', $regionId);
        if (!$region) {
            return response()->json(['message' => 'region do not exist'], 404);
        }

        $admin = AdminRepository::update($admin, $regionId, $roleId, $firstName, $lastName, $email, $password);

        if (!$admin) {
            return response()->json(['message' => 'error updating admin'], 400);
        } else {
            return response()->json(['message' => 'admin updated'], 201);
        }
    }

    public function destroy($adminId)
    {
        $admin = User::query()->find($adminId);
        if (!$admin) {
            return response()->json(['message' => 'admin do not exist'], 404);
        }

        if ($admin->role->name == Role::ROLE_SUPERADMIN) {
            return response()->json(['message' => 'you can not destroy super admin'], 403);
        }

        $deleted = $admin->delete();

        if (!$deleted) {
            return response()->json(['message' => 'error destroy admin'], 400);
        } else {
            return response()->json(['message' => 'admin deleted'], 204);
        }
    }

    public function getAdmin($adminId)
    {
        $admin = User::query()->with('region', 'role')->find($adminId);

        if (!$admin) {
            return response()->json(['message' => 'admin do not exist'], 400);
        }

        return response()->json(['admin' => $admin]);
    }
    public function isUnique(Request $request){
       $email=ucfirst(strtolower($request->email));
        $email=User::query()->where('email','=', $email)->first();

        if(!$email)
            return response()->json( 'true');
        else{

            if($email->id==($request->input('id') ))
                return response()->json( 'true');
            else
                return response()->json( 'Email already exists.');
        }
    }
}
