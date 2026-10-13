<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Validator;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Service\UploadException;
use App\Support\Category;
use App\Support\Status;

final class DashboardController extends Controller
{
    private const LABELS = [
        'title'       => 'Titre',
        'description' => 'Description',
        'category'    => 'Catégorie',
        'location'    => 'Commune / quartier',
        'status'      => 'Visibilité',
    ];

    public function index(Request $request): Response
    {
        $articles = $this->app->articles();

        return $this->view('pages/dashboard/index', [
            'articles' => $articles->forUser($this->userId()),
            'stats'    => $articles->statsForUser($this->userId()),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->view('pages/dashboard/form', ['article' => null]);
    }

    public function store(Request $request): Response
    {
        [$data, $validator] = $this->validate($request);
        $file = $request->file('image');

        if ($file === null) {
            $validator->addError('image', 'Ajoutez une photo : les annonces avec photo partent bien plus vite.');
        }
        if ($validator->fails()) {
            return $this->backWithErrors('mes-annonces/nouvelle', $validator->errors(), $data);
        }

        try {
            $data['image'] = $this->app->uploader()->store($file);
        } catch (UploadException $e) {
            return $this->backWithErrors('mes-annonces/nouvelle', ['image' => $e->getMessage()], $data);
        }

        $this->app->articles()->create($this->userId(), $data);
        $this->success($data['status'] === Status::PUBLISHED
            ? 'Annonce publiée. Elle est visible par tous dès maintenant.'
            : 'Brouillon enregistré. Publiez-le quand vous êtes prêt.');

        return $this->redirect('mes-annonces');
    }

    public function edit(Request $request, int $id): Response
    {
        return $this->view('pages/dashboard/form', ['article' => $this->owned($id)]);
    }

    public function update(Request $request, int $id): Response
    {
        $article = $this->owned($id);
        [$data, $validator] = $this->validate($request);
        $back = "mes-annonces/$id/modifier";

        if ($validator->fails()) {
            return $this->backWithErrors($back, $validator->errors(), $data);
        }

        $data['image'] = $article['image'];
        if ($file = $request->file('image')) {
            try {
                $data['image'] = $this->app->uploader()->store($file);
            } catch (UploadException $e) {
                return $this->backWithErrors($back, ['image' => $e->getMessage()], $data);
            }
        }

        $this->app->articles()->update($id, $this->userId(), $data);

        if ($data['image'] !== $article['image']) {
            $this->app->uploader()->delete($article['image']);
        }

        $this->success('Modifications enregistrées.');

        return $this->redirect('mes-annonces');
    }

    public function status(Request $request, int $id): Response
    {
        $this->owned($id);
        $status = (string) $request->input('status');

        if (!array_key_exists($status, Status::LABELS)) {
            throw new HttpException(422);
        }

        $this->app->articles()->updateStatus($id, $this->userId(), $status);
        $this->success(match ($status) {
            Status::PUBLISHED => 'Annonce remise en ligne.',
            Status::GIVEN     => 'Bravo, objet marqué comme donné ! Il reste visible avec la mention « Donné ».',
            default           => 'Annonce retirée du site. Elle reste dans vos brouillons.',
        });

        return $this->redirect('mes-annonces');
    }

    public function destroy(Request $request, int $id): Response
    {
        $article = $this->owned($id);

        $this->app->articles()->delete($id, $this->userId());
        $this->app->uploader()->delete($article['image']);
        $this->success('Annonce supprimée.');

        return $this->redirect('mes-annonces');
    }

    /** @return array<string, mixed> */
    private function owned(int $id): array
    {
        return $this->app->articles()->findOwned($id, $this->userId())
            ?? throw HttpException::notFound("Cette annonce n'existe pas ou ne vous appartient pas.");
    }

    /** @return array{0: array<string, mixed>, 1: Validator} */
    private function validate(Request $request): array
    {
        $data = $request->only(['title', 'description', 'category', 'location', 'status']);
        $data['location'] = $data['location'] !== '' ? $data['location'] : null;

        $validator = Validator::make($data, [
            'title'       => 'required|min:3|max:120',
            'description' => 'required|min:10|max:2000',
            'category'    => 'required|in:' . Category::slugs(),
            'location'    => 'max:80',
            'status'      => 'required|in:' . Status::values(),
        ], self::LABELS);

        return [$data, $validator];
    }
}
