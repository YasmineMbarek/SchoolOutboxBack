<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Demand;
use App\Repository\ArticleRepository;
use App\Repository\DemandRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandController extends Controller
{
    public function listing(Request $request)
    {
        $adminRegion = Auth::user()->region->id;
        if ($request->ajax()) {
            $start = $request['start'];
            $perPage = $request['length'];
            $page = ($start / $perPage) + 1;
            $search = $request['search']['value'];
            $orderDirection = $request['order'][0]['dir'];
            $orderColumn = $request['columns'][$request['order'][0]['column']]['data'];

            $demands = DemandRepository::listingAdmin($perPage, $page, $search, $orderColumn, $orderDirection, $adminRegion);

            return response()->json([
                'draw' => null,
                'recordsTotal' => $demands->total(),
                'recordsFiltered' => $demands->total(),
                'data' => $demands->items(),
            ]);
        }

        return view('demands');
    }

    public function accept($demandId)
    {
        $demand = Demand::query()->find($demandId);

        if (!$demand) {
            return response()->json(['message' => 'demand do not exist'], 404);
        }

        if ($demand->status !== Demand::STATUS_PENDING) {
            return response()->json(['message' => 'demand already accepted'], 403);
        }

        $article = $demand->article;
        if ($article->status !== Article::STATUS_RECEIVED) {
            return response()->json(['message' => 'article already affected'], 403);
        }

        $article = ArticleRepository::setAffected($article);
        $demand = DemandRepository::accept($demand);
        $demands = $article->demands->where('status', '=', Demand::STATUS_PENDING);

        foreach ($demands as $pendingDemand) {
            DemandRepository::refuse($pendingDemand);
        }

        if (!$demand) {
            return response()->json(['message' => 'error accepting demand'], 400);
        } else {
            return response()->json(['message' => 'success accepting demand'], 201);
        }
    }

    public function refuse($demandId)
    {
        $demand = Demand::query()->find($demandId);

        if (!$demand) {
            return response()->json(['message' => 'demand do not exist'], 404);
        }

        if ($demand->status !== Demand::STATUS_PENDING) {
            return response()->json(['message' => 'demand already refused'], 403);
        }
        $article = $demand->article;
        if ($article->status !== Article::STATUS_RECEIVED) {
            return response()->json(['message' => 'article already affected'], 403);
        }

        $demand = DemandRepository::refuse($demand);

        if (!$demand) {
            return response()->json(['message' => 'error refusing demand'], 400);
        } else {
            return response()->json(['message' => 'success refusing  demand'], 201);
        }
    }


}
