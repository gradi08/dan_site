<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Support\Category;

final class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $query = mb_substr(trim((string) $request->query('q', '')), 0, 80);
        $category = (string) $request->query('categorie', '');
        $category = array_key_exists($category, Category::ALL) ? $category : '';
        $page = max(1, (int) $request->query('page', 1));
        $perPage = (int) $this->app->config('pagination.per_page');

        $result = $this->app->articles()->search($query, $category, $page, $perPage);
        $pages = max(1, (int) ceil($result['total'] / $perPage));

        if ($page > $pages) {
            throw HttpException::notFound();
        }

        return $this->view('pages/articles/index', [
            'articles' => $result['items'],
            'total'    => $result['total'],
            'page'     => $page,
            'pages'    => $pages,
            'q'        => $query,
            'category' => $category,
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $article = $this->app->articles()->findPublic($id)
            ?? throw HttpException::notFound("Cette annonce n'existe pas ou a été retirée par son donneur.");

        return $this->view('pages/articles/show', [
            'article' => $article,
            'isOwner' => $this->app->auth()->id() === (int) $article['user_id'],
        ]);
    }
}
