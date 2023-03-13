<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateDemandRequest;
use App\Models\Article;
use App\Models\Demand;
use App\Repository\DemandRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function response;

class DemandController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page');
        $page = $request->input('page');
        $status = $request->input('status');
        $customer = Auth::user();
        $demands = DemandRepository::listingCustomer($customer->id);
        return response()->json(['message' => 'get all demands', 'result' => $demands]);
    }

    public function create(CreateDemandRequest $request, $articleId)
    {
        $article = Article::query()->find($articleId);
        if (!$article) {
            return response()->json(['message' => 'article  not exist'], 404);
        }

        if ($article->status !== Article::STATUS_RECEIVED) {
            return response()->json(['message' => 'article not received yet'], 403);
        }

        $customer = Auth::user();
        $demand = Demand::query()
            ->where('customer_id', '=', $customer->id)
            ->where('article_id', '=', $articleId)
            ->first();

        if ($demand) {
            return response()->json(['message' => 'you already demand it'], 403);
        }

        if ($customer->id == $article->customer_id) {
            return response()->json(['message' => 'article belongs to you, can not demand it'], 403);
        }

        $motive = $request->input('motive');
        $demandDate = Carbon::now()->format('Y-m-d H:i:s');

        $demand = DemandRepository::create($articleId, $customer->id, $motive, $demandDate);

        if (!$demand) {
            return response()->json(['message' => 'error creating demand'], 400);
        } else {
            return response()->json(['message' => 'success demand', 'result' => $demand], 201);
        }
    }

    public function update(CreateDemandRequest $request, $demandId)
    {
        $demand = Demand::query()->find($demandId);
        if (!$demand) {
            return response()->json(['message' => 'demand  not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $demand->customer_id) {
            return response()->json(['message' => 'demand not belongs to you, can not updated'], 403);
        }
        if ($demand->status !== Demand::STATUS_PENDING) {
            return response()->json(['message' => 'demand not pending, you can not updated'], 403);
        }

        $motive = $request->input('motive');
        $demandDate = Carbon::now()->format('Y-m-d H:i:s');
        $demand = DemandRepository::update($demand, $motive, $demandDate);

        if (!$demand) {
            return response()->json(['message' => 'error updating demand'], 400);
        } else {
            return response()->json(['message' => 'demand updated', 'result' => $demand], 201);
        }
    }

    public function destroy($demandId)
    {
        $demand = Demand::query()->find($demandId);
        if (!$demand) {
            return response()->json(['message' => 'demand  not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $demand->customer_id) {
            return response()->json(['message' => 'demand not belongs to you, can not destroyed'], 403);
        }

        if ($demand->status !== Demand::STATUS_PENDING) {
            return response()->json(['message' => 'demand not pending you can not destroyed'], 403);
        }

        $deleted = $demand->delete();

        if (!$deleted) {
            return response()->json(['message' => 'error destroy demand'], 400);
        } else {
            return response()->json(['message' => 'demand deleted'], 204);
        }
    }

    public function getDemand($demandId)
    {
        $demand = Demand::query()->with('article.pictures', 'article.customer', 'customer')->find($demandId);
        if (!$demand) {
            return response()->json(['message' => 'demand not exist'], 404);
        }
        $customerId = Auth::user()->id;
        if ($customerId !== $demand->customer_id) {
            return response()->json(['message' => 'demand not belongs to you, can not get it '], 403);
        }

        return response()->json(['message' => 'get demand', 'result' => $demand]);
    }
}
