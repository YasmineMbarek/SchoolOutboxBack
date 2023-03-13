<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateCategoriesRequest;
use App\Models\Category;
use App\Repository\CategoryRepository;
use Illuminate\Http\Request;
use function response;

class CategoryController extends Controller
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

            $categories = CategoryRepository::listing($perPage, $page, $search, $orderColumn, $orderDirection);

            return response()->json([
                'draw' => null,
                'recordsTotal' => $categories->total(),
                'recordsFiltered' => $categories->total(),
                'data' => $categories->items(),
            ]);
        }

        return view('categories');
    }

    public function store(CreateCategoriesRequest $request)
    {
        $type = ucfirst(strtolower($request->input('type')));
        $categoryExist = Category::query()->firstWhere('type', '=', $type);
        if ($categoryExist !== null) {
            return response()->json(['message' => 'category exist'], 422);
        }
        $category = CategoryRepository::store($type);

        if (!$category) {
            return response()->json(['message' => 'error creating category'], 400);
        } else {
            return response()->json(['message' => 'category created'], 201);
        }
    }

    public function update(CreateCategoriesRequest $request, $categoryId)
    {
        $category = Category::query()->find($categoryId);
        if (!$category) {
            return response()->json(['message' => 'category  not exist'], 404);
        }

        if ($category->type == Category::DEFAULT_CATEGORY) {
            return response()->json(['message' => 'cannot modify default category'], 403);
        }

        $type = ucfirst(strtolower($request->input('type')));

        $category = CategoryRepository::update($category, $type);

        if (!$category) {
            return response()->json(['message' => 'error updating category'], 400);
        } else {
            return response()->json(['message' => 'category updated'], 201);
        }
    }

    public function destroy($categoryId)
    {
        $category = Category::query()->find($categoryId);
        if (!$category) {
            return response()->json(['message' => 'category do not exist'], 404);
        }

        if ($category->type == Category::DEFAULT_CATEGORY) {
            return response()->json(['message' => 'cannot delete default category'], 403);
        }

        $defaultCategoryId = Category::query()->where('type', '=', Category::DEFAULT_CATEGORY)->value('id');

        foreach ($category->articles as $article) {
            $article->category_id = $defaultCategoryId;
            $article->save();
        }

        $deleted = $category->delete();

        if (!$deleted) {
            return response()->json(['message' => 'error destroy category'], 400);
        } else {
            return response()->json(['message' => 'category deleted'], 204);
        }
    }

    public function getCategory($categoryId)
    {
        $category = Category::query()->with('articles')->find($categoryId);

        if (!$category) {
            return response()->json(['message' => 'category  not exist'], 404);
        }

        return response()->json(['article' => $category]);
    }
    public function isUnique(Request $request){

        /*$type=ucfirst(strtolower($request->type));
        $category=Category::query()->where('type','=',$type)->first();
        return response()->json($category ? 'Category already exists.': 'true');*/
        $category = Category::where('type','=', $request->input('type'))->first();
        //return 'rou7i'.($request->input('id') ) ;
        //return $category;


        if(!$category)
            return response()->json( 'true');
        else{

            if($category->id==($request->input('id') ))
                return response()->json( 'true');
            else
           // return 'category already exists!!!!'. $category->id;
            return response()->json( 'Category already exists.');
            }
    }

}
