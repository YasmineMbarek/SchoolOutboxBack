<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateRegionsRequest;
use App\Models\Region;
use App\Repository\RegionRepository;
use Illuminate\Http\Request;
use function response;

class RegionController extends Controller

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

            $regions = RegionRepository::listing($perPage, $page, $search, $orderColumn, $orderDirection);

            return response()->json([
                'draw' => null,
                'recordsTotal' => $regions->total(),
                'recordsFiltered' => $regions->total(),
                'data' => $regions->items(),
            ]);
        }

        return view('regions');
    }

    public function store(CreateRegionsRequest $request)
    {
        $name =ucfirst(strtolower( $request->input('name')));
        $postalCode = $request->input('postal_code');

        // TODO Unnecessary check (could be removed)
        $region = Region::query()
            ->where('name', '=', $name)
            ->where('postal_code', '=', $postalCode)
            ->first();

        if ($region) {
            return response()->json(['message' => 'Name and postal code already taken'], 422);
        }

        $region = RegionRepository::store($name, $postalCode);

        if (!$region) {
            return response()->json(['message' => 'error creating region'], 400);
        } else {
            return response()->json(['message' => 'region created'], 201);
        }
    }

    public function update(CreateRegionsRequest $request, $regionId)
    {
        $region = Region::query()->find($regionId);

        if (!$region) {
            return response()->json(['message' => 'region do not exist'], 404);
        }

        $name = ucfirst(strtolower( $request->input('name')));
        $postalCode = $request->input('postal_code');

        $region = RegionRepository::update($region, $name, $postalCode);

        if (!$region) {
            return response()->json(['message' => 'error updating region'], 400);
        } else {
            return response()->json(['message' => 'region updated'], 201);
        }
    }

    public function destroy($regionId)
    {
        $region = Region::query()->find($regionId);

        if (!$region) {
            return response()->json(['message' => 'region do not exist'], 404);
        }

        if ($region->customers->count() !== 0 || $region->users->count() !== 0) {
            return response()->json(['message' => 'region has users and/or customers'], 403);
        }

        $deleted = $region->delete();

        if (!$deleted) {
            return response()->json(['message' => 'error destroy region'], 400);
        } else {
            return response()->json(['message' => 'region deleted'], 204);
        }
    }


    public function isUnique(Request $request){
        
        $name=ucfirst(strtolower($request->name));
        $region=Region::where('name','=', $name)
            ->orWhere('postal_code','=',$request->code)
        ->first();


        if(!$region)
            return response()->json( 'true');
        else{

            if($region->id==($request->input('id') ) )
                return response()->json( 'true');
            else
                return response()->json( 'Region already exists.');
        }




    }
}
