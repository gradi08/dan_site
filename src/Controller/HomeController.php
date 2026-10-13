<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $articles = $this->app->articles();

        return $this->view('pages/home', [
            'latest' => $articles->latest(8),
            'stats'  => $articles->communityStats(),
        ]);
    }
}
