<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateArticleRequest;
use App\Http\Requests\Customer\UploadPicturesRequest;
use App\Models\Article;
use App\Models\Picture;
use App\Repository\ArticleRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller

{
    public function index(Request $request)
    {
        $page = $request->input('page');
        $perPage = $request->input('per_page');
        $customer = Auth::user();

        $articles = ArticleRepository::listing($customer, $perPage, $page);

        return response()->json(['message' => 'get all articles', 'result' => $articles]);
    }

    public function store(CreateArticleRequest $request)
    {
        $customerId = Auth::user()->id;
        $name = $request->input('name');
        $description = $request->input('description');
        $state = $request->input('state');
        $categoryId = $request->input('category_id');
        $depositDate = Carbon::now()->format('Y-m-d H:i:s');

        $article = ArticleRepository::store($customerId, $categoryId, $name, $depositDate, $description, $state);

        if (!$article) {
            return response()->json(['message' => 'error creating article'], 400);
        } else {
            return response()->json(['message' => 'article created', 'result' => $article], 201);
        }
    }

    public function storePicture(UploadPicturesRequest $request, $articleId)
    {
        $article = Article::query()->find($articleId);
        if (!$article) {
            return response()->json(['message' => 'article  not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $article->customer_id) {
            return response()->json(['message' => 'article not belongs to you, can not store pic'], 403);
        }

        if ($article->status !== Article::STATUS_CREATED) {
            return response()->json(['message' => 'article already received, can not store pic'], 403);
        }

        $image = $request->file('image');

        $picturesCount = Picture::query()->where('article_id', '=', $articleId)->count();
        if ($picturesCount > Picture::MAX_PICTURES) {
            return response()->json(['message' => 'must 3 picture to upload'], 400);
        }

        $picture = ArticleRepository::storePicture($image, $articleId);

        if (!$picture) {
            return response()->json(['message' => 'error creating picture'], 400);
        } else {
            return response()->json(['message' => 'picture created', 'result' => $picture], 201);
        }
    }

    public function update(CreateArticleRequest $request, $articleId)
    {
        $article = Article::query()->find($articleId);
        if (!$article) {
            return response()->json(['message' => 'article not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $article->customer_id) {
            return response()->json(['message' => 'article not belongs to you, can not updated'], 403);
        }

        if ($article->status !== Article::STATUS_CREATED) {
            return response()->json(['message' => 'article already received, can not updated'], 403);
        }

        $categoryId = $request->input('category_id');
        $name = $request->input('name');
        $depositDate = Carbon::now()->format('Y-m-d H:i:s');
        $description = $request->input('description');
        $state = $request->input('state');

        $article = ArticleRepository::update($articleId, $categoryId, $name, $depositDate, $description, $state);

        if (!$article) {
            return response()->json(['message' => 'error updating article'], 400);
        } else {
            return response()->json(['message' => 'article updated', 'result' => $article], 201);
        }
    }

    public function destroy($articleId)
    {
        $article = Article::query()->find($articleId);
        if (!$article) {
            return response()->json(['message' => 'article  not exist'], 404);
        }

        $customerId = Auth::user()->id;
        if ($customerId !== $article->customer_id) {
            return response()->json(['message' => 'article not belongs to you , can not deleted'], 403);
        }

        if ($article->status !== Article::STATUS_CREATED) {
            return response()->json(['message' => 'article already received, can not deleted'], 403);
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

    public function destroyPicture($pictureId)
    {
        $picture = Picture::query()->find($pictureId);
        if (!$picture) {
            return response()->json(['message' => 'picture not exist'], 400);
        }

        $customer = $picture->article->customer;
        $customerId = Auth::user()->id;
        if ($customerId !== $customer->id) {
            return response()->json(['message' => 'article not belongs to you , can not delete picture'], 403);
        }

        $article = $picture->article;
        if ($article->status !== Article::STATUS_CREATED) {
            return response()->json(['message' => 'article already received, can not delete picture'], 403);
        }

        $deleted = ArticleRepository::destroyPicture($picture);

        if (!$deleted) {
            return response()->json(['message' => 'error destroy picture'], 400);
        } else {
            return response()->json(['message' => 'picture deleted'], 204);
        }
    }

    public function getArticle($articleId)
    {
        $article = Article::query()->with('pictures', 'customer')->find($articleId);
        $customerId = Auth::user()->id;

        if (!$article) {
            return response()->json(['message' => 'article  not exist'], 400);
        }
        if ($article->customer_id !== $customerId) {
            return response()->json(['message' => 'article not belongs to you , can not see it'], 403);
        }

        return response()->json(['message' => 'get article', 'result' => $article]);
    }
}
