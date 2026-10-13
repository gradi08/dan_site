<?php

declare(strict_types=1);

use App\Controller\ArticleController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\HomeController;
use App\Controller\ProfileController;
use App\Http\Router;

return static function (Router $r): void {
    // Public
    $r->get('/', [HomeController::class, 'index']);
    $r->get('/objets', [ArticleController::class, 'index']);
    $r->get('/objets/{id}', [ArticleController::class, 'show']);

    // Authentification
    $r->get('/connexion', [AuthController::class, 'loginForm'], ['guest']);
    $r->post('/connexion', [AuthController::class, 'login'], ['guest']);
    $r->get('/inscription', [AuthController::class, 'registerForm'], ['guest']);
    $r->post('/inscription', [AuthController::class, 'register'], ['guest']);
    $r->post('/deconnexion', [AuthController::class, 'logout'], ['auth']);

    // Espace donneur
    $r->get('/mes-annonces', [DashboardController::class, 'index'], ['auth']);
    $r->get('/mes-annonces/nouvelle', [DashboardController::class, 'create'], ['auth']);
    $r->post('/mes-annonces', [DashboardController::class, 'store'], ['auth']);
    $r->get('/mes-annonces/{id}/modifier', [DashboardController::class, 'edit'], ['auth']);
    $r->post('/mes-annonces/{id}', [DashboardController::class, 'update'], ['auth']);
    $r->post('/mes-annonces/{id}/statut', [DashboardController::class, 'status'], ['auth']);
    $r->post('/mes-annonces/{id}/supprimer', [DashboardController::class, 'destroy'], ['auth']);

    $r->get('/profil', [ProfileController::class, 'edit'], ['auth']);
    $r->post('/profil', [ProfileController::class, 'update'], ['auth']);
    $r->post('/profil/mot-de-passe', [ProfileController::class, 'password'], ['auth']);

    // Compatibilité avec les liens de la v1 déjà partagés
    $r->redirect('/index.php', '/');
    $r->redirect('/articles.php', 'objets');
    $r->redirect('/connexion.php', 'connexion');
    $r->redirect('/enregistrement.php', 'inscription');
    $r->redirect('/mes_articles.php', 'mes-annonces');
    $r->redirect('/ajouter-produit.php', 'mes-annonces/nouvelle');
    $r->redirect('/profils.php', 'profil');
};
