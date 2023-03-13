<?php

namespace App\Repository;

use App\Models\Article;
use App\Models\Picture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleRepository
{
    public static function listing($customer, $perPage = 10, $page = 1)
    {
        $articles = Article::query()
            ->where('customer_id', '=', $customer->id)
            ->with('pictures');

        return $articles->paginate($perPage, ['*'], 'page', $page);
    }

    public static function destroyPicture($picture)
    {
        $path = $picture->path;
        unlink($path);
        //Storage::delete($path);
        return $picture->delete();
    }

    public static function storePicture($image, $articleId)
    {
        $name_gen=hexdec(uniqid());
        $image_ext=strtolower($image->getClientOriginalExtension());
        $img_name=$name_gen.'.'.$image_ext;
        $up_location='image/articles/';
        $img=$up_location.$img_name;
        $image->move($up_location,$img_name);
       // $path = $image->store('image/articles');
        $picture = new Picture();
        $picture->article_id = $articleId;
        $picture->path = $img;
        $picture->save();
        return $picture;
    }

    public static function store(int $customerId, int $categoryId, string $name, string $depositDate, string $description, string $state)
    {
        $article = new Article();
        $article->customer_id = $customerId;
        $article->category_id = $categoryId;
        $article->name = $name;
        $article->deposit_date = $depositDate;
        $article->status = Article::STATUS_CREATED;
        $article->description = $description;
        $article->state = $state;
        $article->save();

        return $article;
    }

    public static function update(int $articleId, int $categoryId, string $name, string $depositDate, string $description, string $state)
    {
        $article = Article::query()->find($articleId);
        $article->category_id = $categoryId;
        $article->name = $name;
        $article->deposit_date = $depositDate;
        $article->description = $description;
        $article->status = Article::STATUS_CREATED;
        $article->state = $state;
        $article->save();
        return $article;
    }

    public static function setAffected($article)
    {
        $article->status = Article::STATUS_AFFECTED;
        $article->save();

        return $article;
    }
    public static function listingAdminArticles($perPage = 10, $page = 1, $search = null, $orderColumn = null,$orderDirection = null)
    {
        $regionId = Auth::user()->region->id;
        $articles = Article::query()->with('category', 'customer','pictures')
            ->where('status', '=', Article::STATUS_CREATED)
            ->whereHas('customer', function ( $customerQuery) use ($regionId) {
                $customerQuery->where('region_id', '=', $regionId);
            });

        if ($search) {
            $articles->where(function ($q) use ($search) {
                $q->whereHas('category', function ( $articleQuery) use ($search) {
                    $articleQuery->where('name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('customer', function ( $customerQuery) use ($search) {
                        $customerQuery->where('email', 'like', '%' . $search . '%')
                            ->orWhere('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('deposit_date', 'like', '%' . $search . '%');
            });
        }

        if ($orderDirection && $orderColumn) {
            if ($orderColumn === 'customer') {
                $articles->orderBy('customer_id', $orderDirection);
            }
            else{
                $articles->orderBy($orderColumn,$orderDirection);


            }

        }


        return $articles->paginate($perPage, ['*'], 'page', $page);

    }

}
