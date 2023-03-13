<?php

namespace App\Repository;

use App\Models\Demand;
use Illuminate\Support\Facades\Auth;

class DemandRepository
{
    public static function listingAdmin($perPage = 10, $page = 1, $search = null, $orderColumn = null, $orderDirection = null)
    {
        $regionId = Auth::user()->region->id;
        $demands = Demand::query()->with('article.customer', 'customer')
            ->where('status', '=', Demand::STATUS_PENDING)
            ->whereHas('customer', function ( $customerQuery) use ($regionId) {
                $customerQuery->where('region_id', '=', $regionId);
            });

        if ($search) {
            $demands->where(function ($q) use ($search) {
                $q->whereHas('article', function ( $articleQuery) use ($search) {
                    $articleQuery->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('customer', function ( $customerQuery) use ($search) {
                        $customerQuery->where('email', 'like', '%' . $search . '%')
                            ->orWhere('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('demand_date', 'like', '%' . $search . '%');
            });
        }
        if ($orderDirection && $orderColumn) {
            if ($orderColumn === 'article') {
                $demands->orderby('article_id', $orderDirection);


            }
            elseif ($orderColumn === 'customer') {
                $demands->orderby('customer_id', $orderDirection);

            }

        /*if ($orderDirection && $orderColumn) {
            if ($orderColumn === 'article') {
                $demands->whereHas('article', function ($q) use($orderDirection){
                        $q->orderby('name', $orderDirection);

                    });


            }
            elseif ($orderColumn === 'customer') {
                $demands->whereHas('customer', function ($q) use($orderDirection){
                        $q->orderby('email', $orderDirection);

                    });


            }*/
            else{
                $demands->orderBy($orderColumn,$orderDirection);


            }

        }


        return $demands->paginate($perPage, ['*'], 'page', $page);

    }

    public static function accept($demand)
    {
        $demand->status = Demand::STATUS_ACCEPTED;
        return $demand->save();
    }

    public static function refuse($demand)
    {
        $demand->status = Demand::STATUS_REFUSED;
        return $demand->save();
    }

    public static function listingCustomer(int $idCustomer)
    {
        $demands = Demand::query()->with('article', 'customer', 'article.pictures')
            ->where('demands.customer_id', '=', $idCustomer)
            ->get();
        return $demands;
    }

    public static function create(int $articleId, int $customerId, string $motive, string $demandDate)
    {
        $demand = new Demand();
        $demand->article_id = $articleId;
        $demand->customer_id = $customerId;
        $demand->motive = $motive;
        $demand->demand_date = $demandDate;
        $demand->status = Demand::STATUS_PENDING;
        $demand->save();
        return $demand;
    }

    public static function update($demand, string $motive, string $demandDate)
    {
        $demand->motive = $motive;
        $demand->demand_date = $demandDate;
        $demand->save();
        return $demand;
    }


}
