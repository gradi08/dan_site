<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Validator;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Repository\ArticleRepository;
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
        return $this->view('pages/dashboard/form', ['article' => null, 'images' => []]);
    }

    public function store(Request $request): Response
    {
        [$data, $validator] = $this->validate($request);
        $files = $request->files('images');
        $back = 'mes-annonces/nouvelle';

        $this->validateImageCount($validator, 0, count($files));
        if ($validator->fails()) {
            return $this->backWithErrors($back, $validator->errors(), $data);
        }

        try {
            $stored = $this->storeImages($files);
        } catch (UploadException $e) {
            return $this->backWithErrors($back, ['images' => $e->getMessage()], $data);
        }

        $articles = $this->app->articles();
        try {
            $articles->transaction(function () use ($articles, $data, $stored): void {
                $id = $articles->create($this->userId(), $data);
                $articles->addImages($id, $stored);
            });
        } catch (\Throwable $e) {
            $this->deleteFiles($stored);
            throw $e;
        }

        $this->success($data['status'] === Status::PUBLISHED
            ? 'Annonce publiée. Elle est visible par tous dès maintenant.'
            : 'Brouillon enregistré. Publiez-le quand vous êtes prêt.');

        return $this->redirect('mes-annonces');
    }

    public function edit(Request $request, int $id): Response
    {
        return $this->view('pages/dashboard/form', [
            'article' => $this->owned($id),
            'images'  => $this->app->articles()->images($id),
        ]);
    }

    public function update(Request $request, int $id): Response
    {
        $this->owned($id);
        [$data, $validator] = $this->validate($request);
        $back = "mes-annonces/$id/modifier";
        $articles = $this->app->articles();

        $existing = array_column($articles->images($id), 'id');
        $remove = array_values(array_intersect($existing, array_map('intval', (array) $request->input('remove', []))));
        $cover = (int) $request->input('cover', 0);
        $files = $request->files('images');

        $this->validateImageCount($validator, count($existing) - count($remove), count($files));
        if ($validator->fails()) {
            return $this->backWithErrors($back, $validator->errors(), $data);
        }

        try {
            $stored = $this->storeImages($files);
        } catch (UploadException $e) {
            return $this->backWithErrors($back, ['images' => $e->getMessage()], $data);
        }

        try {
            $removed = $articles->transaction(function () use ($articles, $id, $data, $remove, $stored, $cover): array {
                $articles->update($id, $this->userId(), $data);
                $removed = $articles->removeImages($id, $remove);
                $articles->addImages($id, $stored);
                $articles->reorder($id, in_array($cover, $remove, true) ? null : $cover);

                return $removed;
            });
        } catch (\Throwable $e) {
            $this->deleteFiles($stored);
            throw $e;
        }

        // Fichiers supprimés du disque seulement une fois la base à jour.
        $this->deleteFiles($removed);
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
        $this->owned($id);
        $articles = $this->app->articles();
        $files = array_column($articles->images($id), 'filename');

        $articles->delete($id, $this->userId());
        $this->deleteFiles($files);
        $this->success('Annonce supprimée.');

        return $this->redirect('mes-annonces');
    }

    private function validateImageCount(Validator $validator, int $kept, int $added): void
    {
        $total = $kept + $added;
        $max = ArticleRepository::MAX_IMAGES;

        if ($total < 1) {
            $validator->addError('images', 'Ajoutez au moins une photo : les annonces avec photo partent bien plus vite.');
        } elseif ($total > $max) {
            $validator->addError('images', $kept > 0
                ? sprintf('%d photos maximum : vous en avez déjà %d, vous pouvez en ajouter %d.', $max, $kept, max(0, $max - $kept))
                : sprintf('%d photos maximum par annonce. Retirez-en %d.', $max, $total - $max));
        }
    }

    /**
     * Enregistre toutes les photos ou aucune : en cas d'échec, celles déjà écrites sont supprimées.
     *
     * @param list<array<string, mixed>> $files
     * @return list<string>
     * @throws UploadException
     */
    private function storeImages(array $files): array
    {
        $stored = [];
        try {
            foreach ($files as $file) {
                $stored[] = $this->app->uploader()->store($file);
            }
        } catch (UploadException $e) {
            $this->deleteFiles($stored);
            $name = (string) ($file['name'] ?? '');
            throw new UploadException(($name !== '' ? "« $name » : " : '') . $e->getMessage());
        }

        return $stored;
    }

    /** @param list<string> $filenames */
    private function deleteFiles(array $filenames): void
    {
        foreach ($filenames as $filename) {
            $this->app->uploader()->delete($filename);
        }
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
