<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Demand;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// TODO rename to DashboardController
class DashboardController extends Controller
{
    public function statistics()
    {
        $articlesByCategories= Category::with("articles")->get();
        $customersByRegions = Region::with("customers")->get();
        $categoriesArticles = Category::with("articles")->get();

        $articleReceivedDate= Article::get()->where('status', '=', Article::STATUS_RECEIVED)
            ->groupBy(function ($item) {
                return $item->created_at->format('M-Y');
            });
        $articlesRegions = Article::query()->with('customer.region')
            ->where('status', '=', Article::STATUS_RECEIVED)->get()->groupBy(function ($item) {
                return $item->customer->region->name;
            });
        $nbCustomerByMY = Customer::get()->groupBy(function ($item) {
            return $item->created_at->format('M-Y');
        });
        $articleAffectedByYM = Article::query()->where('status', '=', Article::STATUS_AFFECTED)
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('M-Y');
            });
        $articleStatus=Article::get()->groupBy('status');

        return view("index", ['articlesByCategories' => $articlesByCategories, 'customersByRegions' => $customersByRegions,
            'categoriesArticles' => $categoriesArticles, 'articleReceivedDate' => $articleReceivedDate, 'articlesRegions' => $articlesRegions,
            'nbCustomerByMY' => $nbCustomerByMY, 'articleAffectedByYM' => $articleAffectedByYM,'articleStatus'=>$articleStatus]);

    }

    public function admin()
    {
        $regionId = Auth::user()->region->id;
        $nbCustomer = Customer::get()->where('region_id','=',$regionId)->groupBy(function ($item) {
            return $item->created_at->format('M-Y');
        });
        $article=Article::join('customers','articles.customer_id','=','customers.id')->get()
            ->where('status','=',Article::STATUS_RECEIVED)->where('region_id','=',$regionId)->groupBy(function ($item) {
                return $item->created_at->format('M-Y');
            });
        $articleAffected=Article::join('customers','articles.customer_id','=','customers.id')->get()
            ->where('status','=',Article::STATUS_AFFECTED)->where('region_id','=',$regionId)->groupBy(function ($item) {
                return $item->created_at->format('M-Y');
            });
        $articleByCat=Article::where('status','=',Article::STATUS_RECEIVED)->join('categories','articles.category_id','=','categories.id')->
        join('customers','articles.customer_id','=','customers.id')->get()->where('region_id','=',$regionId)->groupBy('type');

            ;

        //dd(json_encode($articleByCat));
        return view("dashboardAdmin", ['nbCustomer'=>$nbCustomer, 'article'=>$article,
            'articleAffected'=>$articleAffected,
            'articleByCat'=>$articleByCat]);




    }
}
