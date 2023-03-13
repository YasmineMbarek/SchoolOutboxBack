<?php

namespace App\Repository;

use App\Models\Region;

class RegionRepository
{
    public static function listing($perPage = 10, $page = 1, $search = null, $orderColumn = null, $orderDirection = null)
    {
        $regions = Region::query()->withCount('users', 'customers');
        if ($search) {
            $regions->where('name', 'like', '%' . $search . '%')
                ->orwhere('postal_code', 'like', '%' . $search . '%');
        }

        if ($orderColumn && $orderDirection) {
            $regions->orderBy($orderColumn, $orderDirection);
        }

        return $regions->paginate($perPage, ['*'], 'page', $page);
    }

    public static function store(string $name, int $postalCode)
    {
        $region = new Region();
        $region->name = $name;
        $region->postal_code = $postalCode;
        $region->save();
        return $region;
    }

    public static function update($region, string $name, int $postalCode): bool
    {
        $region->name = $name;
        $region->postal_code = $postalCode;

        return $region->save();
    }
}
