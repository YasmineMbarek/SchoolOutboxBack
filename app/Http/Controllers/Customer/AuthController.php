<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Profile\ChangePasswordRequest;
use App\Http\Requests\Customer\Profile\RegisterRequest;
use App\Http\Requests\Customer\Profile\UpdateRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Region;
use App\Repository\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Validator;
use function auth;
use function request;
use function response;

class AuthController extends Controller
{
    public function regions()
    {
        $regions = Region::query()->get()->all();
        if (!$regions) {
            return response()->json(['message' => 'no region'], 404);

        } else {
            return response()->json(['regions' => $regions]);

        }

    }

    public function categories()
    {
        $categories = Category::query()->get()->all();
        if (!$categories) {
            return response()->json(['message' => 'no category'], 404);

        } else {
            return response()->json(['categories' => $categories]);

        }

    }


    public function articlesHome()
    {
        $artciles = Article::query()->with('pictures', 'customer')->where('status', '=', Article::STATUS_RECEIVED)->get();
        if (!$artciles) {
            return response()->json(['message' => 'no article exist'], 404);

        } else {
            return response()->json(['result' => $artciles]);

        }
    }

    public function articleHome($articleId)
    {
        $artcile = Article::query()->with('pictures', 'customer')
            ->where('id', '=', $articleId)
            ->where('status', '=', Article::STATUS_RECEIVED)
            ->get();
        if (!$artcile) {
            return response()->json(['message' => 'no article exist'], 404);

        } else {
            return response()->json(['result' => $artcile]);

        }
    }



    public function register(RegisterRequest $request)
    {
        $regionExist = Region::query()->find($request->input('region_id'));
        if (!$regionExist) {
            return response()->json(['message' => 'region do not exist'], 404);
        }


        $regionId = intval($request->input('region_id'));
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $password = $request->input('password');
        $grade = $request->input('grade');
        $customerExist = Customer::query()->firstWhere('email', "=", $email);
        if ($customerExist !== null) {
            return response()->json(['message' => 'email exist'], 422);

        }
        $customer = CustomerRepository::register($regionId, $firstName, $lastName, $email, $password, $grade);
        if (!$customer) {
            return response()->json(['message' => 'error creating customer'], 400);
        } else {
            return response()->json(['message' => 'customer created'], 201);
        }
    }

    public function storePicture(Request $request, $customerId)
    {
        $customer = Customer::query()->find($customerId);
        if (!$customer) {
            return response()->json(['message' => 'customer do not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $customer->id) {
            return response()->json(['message' => 'customer not belongs to you, can not post pic'], 403);
        }
        if ($customer->picture) {
            return response()->json(['message' => ' have  pic already '], 404);

        }
        $image = $request->file('image');

        $picture = CustomerRepository::storePicture($image, $customer);

        if (!$picture) {
            return response()->json(['message' => 'error creating picture'], 400);
        } else {
            return response()->json(['message' => 'picture created'], 201);
        }
    }

    public function destroyPicture($customerId)
    {
        $customer = Customer::query()->find($customerId);
        if (!$customer) {
            return response()->json(['message' => 'customer do not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $customer->id) {
            return response()->json(['message' => 'customer not belongs to you, can not destroy pic'], 403);
        }
        if (!$customer->picture) {
            return response()->json(['message' => ' have  not pic'], 404);

        }

        $picture = CustomerRepository::destroyPicture($customer);

        if (!$picture) {
            return response()->json(['message' => 'error destroyed picture'], 400);
        } else {
            return response()->json(['message' => 'picture destroyed'], 201);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->guard('customer')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout(Request $request)
    {

        auth()->guard('customer')->logout();

        return response()->json(['message' => 'customer successfully logged out.']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('customer')->refresh());
    }

    public function profile()
    {
        return response()->json(auth()->guard('customer')->user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('customer')->factory()->getTTL() * 60
        ]);
    }

    public function update(UpdateRequest $request, $customerId)
    {
        $customer = Customer::query()->find($customerId);
        if (!$customer) {
            return response()->json(['message' => 'customer did not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $customer->id) {
            return response()->json(['message' => 'not your profile, can not updated'], 403);
        }

        $regionExist = Region::query()->find($request->input('region_id'));
        if (!$regionExist) {
            return response()->json(['message' => 'region do not exist'], 404);
        }

        $regionId = $request->input('region_id');
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $email = $request->input('email');
        $grade = $request->input('grade');
        $customerExist = Customer::query()->firstWhere('email', "=", $email);
        if ($customerExist !== null && ($customer->email !== $email)) {
            return response()->json(['message' => 'email exist'], 422);

        }

        $customer = CustomerRepository::update($customer, $regionId, $firstName, $lastName, $email, $grade);

        if (!$customer) {
            return response()->json(['message' => 'error updating customer'], 400);
        } else {
            return response()->json(['message' => 'customer updating', 'customer' => $customer], 201);
        }
    }

    public function changePassword(ChangePasswordRequest $request, $customerId)
    {
        $customer = Customer::query()->find($customerId);
        if (!$customer) {
            return response()->json(['message' => 'customer do not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $customer->id) {
            return response()->json(['message' => 'not your profile, can not change your password'], 403);
        }

        $email = $request->input('email');
        $customerExist = Customer::query()->where('email', '=', $email)->first();
        if (!$customerExist) {
            return response()->json(['message' => 'email  not exist'], 404);
        }

        if ($customerExist->email !== $customer->email) {
            return response()->json(['message' => 'not your email'], 403);
        }

        $oldPassword = $request->input('old_password');
        if (!Hash::check($oldPassword, auth()->guard('customer')->user()->password)) {
            return response()->json(['message' => 'old password do not match '], 403);

        }
        $password = $request->input('password');
        $confirmPassword = $request->input('confirm_password');
        if ($password !== $confirmPassword) {
            return response()->json(['message' => ' new password not match '], 402);
        }

        $customer = CustomerRepository::changePassword($customer, $password);
        if (!$customer) {
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

    public function reset()
    {
        $input = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6'
        ]);
        $reset_password_status = $this->broker()->reset($input, function ($customer, $password) {
            $customer->password = Hash::make($password);
            $customer->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(['message' => 'error token'], 400);
        }

        return response()->json(["msg" => "Password has been successfully changed"]);
    }

    public function broker()
    {
        return Password::broker('customers');
    }
}
