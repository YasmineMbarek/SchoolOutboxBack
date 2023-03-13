<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Repository\AdminArticleRepository;
use App\Repository\ArticleRepository;
use Illuminate\Http\Request;

class ArticleController extends Controller
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

            $articles = ArticleRepository::listingAdminArticles($perPage, $page, $search, $orderColumn, $orderDirection);

            return response()->json([
                'draw' => null,
                'recordsTotal' => $articles->total(),
                'recordsFiltered' => $articles->total(),
                'data' => $articles->items(),
            ]);
        }

        return view('articles');
    }

    public function updateStatus(Request $request, $articleId)
    {
        $article = Article::query()->find($articleId);

        if (!$article) {
            return response()->json(['message' => 'article do not exist'], 404);
        }
        if($article->status != Article::STATUS_CREATED){
            return response()->json(['message' => 'article already received'], 403);
        }

        $status = $request->input('status');

        $article->status = $status;
        $article->save();

        if (!$article) {
            return response()->json(['message' => 'error updating status'], 400);
        } else {
            return response()->json(['message' => 'status updated'], 201);
        }
    }
    public function destroyArticle($articleId)
    {
        $article = Article::query()->find($articleId);

        if (!$article) {
            return response()->json(['message' => 'article  not exist'], 404);
        }

        if ($article->status != Article::STATUS_CREATED) {
            return response()->json(['message' => 'article status already invalid '], 403);
        }
        foreach ($article->pictures as $picture) {
            ArticleRepository::destroyPicture($picture);
        }

        $deleted = $article->delete();

        if (!$deleted) {
            return response()->json(['message' => 'error destroy article'], 400);
        } else {
            return response()->json(['message' => 'article deleted'], 204);
        }
    }
}
