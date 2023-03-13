<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\ChangePasswordRequest;
use App\Http\Requests\Customer\Profile\RegisterRequest;
use App\Models\Customer;
use App\Models\Region;
use App\Models\User;
use App\Repository\AdminRepository;
use App\Repository\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Mail;
use Validator;
use function auth;
use function response;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->guard('admin')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout(Request $request)
    {

        auth()->guard('admin')->logout();

        return response()->json(['message' => 'admin successfully logged out.']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('admin')->refresh());
    }

    public function profile(Request $request)
    {
        if ($request->ajax()) {
            $admin=Auth()->user();
            return response()->json([
                'draw' => null,


                'data' =>$admin,
            ]);
        }

        return view("profile");

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('admin')->factory()->getTTL() * 60
        ]);
    }

    public function update(RegisterRequest $request, $adminId)
    {
        $admin = User::query()->find($adminId);
        if (!$admin) {
            return response()->json(['message' => 'admin  not exist'], 404);
        }

        $adminId = Auth::user()->id;
        if ($adminId !== $admin->id) {
            return response()->json(['message' => 'not your profile, can not updated'], 403);
        }

        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $admin= AdminRepository::updateProfile($admin, $firstName, $lastName, $email);

        if (!$admin) {
            return response()->json(['message' => 'error updating admin'], 400);
        } else {
            return response()->json(['message' => 'admin updating'], 201);
        }
    }

    public function changePassword(ChangePasswordRequest $request, $adminId)
    {
        $admin = User::query()->find($adminId);
        if (!$admin) {
            return response()->json(['message' => 'admin do not exist'], 404);
        }

        $adminId = Auth::user()->id;
        if ($adminId !== $adminId) {
            return response()->json(['message' => 'not your profile, can not change your password'], 403);
        }

        $email = $request->input('email');
        $adminExist = User::query()->where('email', '=', $email)->first();
        if (!$adminExist) {
            return response()->json(['message' => 'email do not exist'], 404);
        }
        if ($email !== $admin->email) {
            return response()->json(['message' => 'not your email'], 403);
        }

        $oldPassword = $request->input('old_password');
        if (!Hash::check($oldPassword, Auth::user()->password)) {
            return response()->json(['message' => 'old password do not match '], 403);

        }
        $password = $request->input('password');
        $confirmPassword = $request->input('confirm_password');
        if ($password !== $confirmPassword) {
            return response()->json(['message' => ' new password not match '], 402);
        }

        $admin = AdminRepository::changePassword($admin, $password);
        if (!$admin) {
            return response()->json(['message' => 'error changing password'], 400);
        } else {
            return response()->json(['message' => 'success updating password'], 201);
        }

    }

    public function forgot()
    {
        $email = request()->validate(['email' => 'required|email']);
        $response = $this->broker()->sendResetLink($email);
        if ($response !== Password::RESET_LINK_SENT) {
            return response()->json(["msg" => 'Reset password link sent on your email failed']);
        } else {
            return response()->json(["msg" => 'Reset password link sent on your email id.']);
        }
    }

    public function reset(Request $request)
    {
        $input = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6'
        ]);
        $reset_password_status = $this->broker()->reset($input, function ($customer, $password) {
            $customer->password =Hash::make($password);
            $customer->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(['message' => 'error token'], 400);
        }

        return response()->json(["msg" => "Password has been successfully changed"]);
    }

    public function broker()
    {
        return Password::broker('admins');
    }
}
