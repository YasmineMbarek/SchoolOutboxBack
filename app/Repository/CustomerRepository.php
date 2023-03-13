<?php

namespace App\Repository;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerRepository
{
    public static function register(int $regionId, string $firstName, string $lastName, string $email, string $password, string $grade)
    {
        $customer = new Customer();
        $customer->region_id = $regionId;
        $customer->first_name = $firstName;
        $customer->last_name = $lastName;
        $customer->email = $email;
        $customer->password = Hash::make($password);
        $customer->grade = $grade;
        $customer->save();
        return $customer;

    }

    public static function storePicture($image, $customer)
    {
        $path = $image->store('image/profile');

        $customer->picture = $path;

        return $customer->save();
    }

    public static function destroyPicture($customer)
    {
        $path = $customer->picture;
        Storage::delete($path);
        $customer->picture = null;
        return $customer->save();
    }

    public static function update($customer, string $regionId, string $firstName, string $lastName, string $email, $grade)
    {
        $customer->region_id = $regionId;
        $customer->first_name = $firstName;
        $customer->last_name = $lastName;
        $customer->email = $email;
        $customer->grade = $grade;
        $customer->save();
        return $customer;
    }

    public static function changePassword($customer, string $password)
    {
        $customer->password = Hash::make($password);
        $customer->save();
        return $customer;
    }
}
