<?php

declare(strict_types=1);

namespace App\Repository;

use App\Support\Status;
use PDO;

final class ArticleRepository
{
    public const MAX_IMAGES = 5;

    /** Photo principale = la photo de position la plus basse. */
    private const COVER = '(SELECT i.filename FROM article_images i WHERE i.article_id = a.id
        ORDER BY i.position, i.id LIMIT 1) AS image';

    private const PUBLIC_COLUMNS = 'a.id, a.user_id, a.title, a.description, a.category, a.location,
        a.status, a.created_at, a.updated_at, u.name AS owner_name, u.phone AS owner_phone, ' . self::COVER;

    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Annonces visibles publiquement, filtrées et paginées.
     *
     * @return array{items: list<array<string, mixed>>, total: int}
     */
    public function search(?string $query, ?string $category, int $page, int $perPage): array
    {
        $where = ['a.status = :status'];
        $params = [':status' => Status::PUBLISHED];

        if ($query !== null && $query !== '') {
            // ESCAPE explicite : comportement identique sous MySQL et SQLite.
            $where[] = "(a.title LIKE :q1 ESCAPE '!' OR a.description LIKE :q2 ESCAPE '!' OR a.location LIKE :q3 ESCAPE '!')";
            $like = '%' . strtr($query, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
            $params += [':q1' => $like, ':q2' => $like, ':q3' => $like];
        }
        if ($category !== null && $category !== '') {
            $where[] = 'a.category = :category';
            $params[':category'] = $category;
        }

        $whereSql = implode(' AND ', $where);

        $count = $this->db->prepare("SELECT COUNT(*) FROM articles a WHERE $whereSql");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT ' . self::PUBLIC_COLUMNS . " FROM articles a
             INNER JOIN users u ON u.id = a.user_id
             WHERE $whereSql
             ORDER BY a.created_at DESC, a.id DESC
             LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, ($page - 1) * $perPage), PDO::PARAM_INT);
        $stmt->execute();

        return ['items' => $stmt->fetchAll(), 'total' => $total];
    }

    /** @return list<array<string, mixed>> */
    public function latest(int $limit): array
    {
        return $this->search(null, null, 1, $limit)['items'];
    }

    /** @return array<string, mixed>|null */
    public function findPublic(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ' . self::PUBLIC_COLUMNS . ' FROM articles a
             INNER JOIN users u ON u.id = a.user_id
             WHERE a.id = ? AND a.status IN (?, ?)'
        );
        $stmt->execute([$id, Status::PUBLISHED, Status::GIVEN]);

        return $stmt->fetch() ?: null;
    }

    /** @return array<string, mixed>|null Seulement si l'annonce appartient à l'utilisateur. */
    public function findOwned(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT a.*, ' . self::COVER . ' FROM articles a WHERE a.id = ? AND a.user_id = ?');
        $stmt->execute([$id, $userId]);

        return $stmt->fetch() ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, ' . self::COVER . ' FROM articles a WHERE a.user_id = ? ORDER BY a.created_at DESC, a.id DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    /** @return array<string, int> Nombre d'annonces par statut. */
    public function statsForUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT status, COUNT(*) AS n FROM articles WHERE user_id = ? GROUP BY status');
        $stmt->execute([$userId]);

        $stats = array_fill_keys(array_keys(Status::LABELS), 0);
        foreach ($stmt->fetchAll() as $row) {
            $stats[$row['status']] = (int) $row['n'];
        }

        return $stats;
    }

    /** @return array{published: int, given: int, givers: int} */
    public function communityStats(): array
    {
        $row = $this->db->query(
            "SELECT
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS published,
                SUM(CASE WHEN status = 'given' THEN 1 ELSE 0 END) AS given,
                COUNT(DISTINCT CASE WHEN status <> 'draft' THEN user_id END) AS givers
             FROM articles"
        )->fetch() ?: [];

        return [
            'published' => (int) ($row['published'] ?? 0),
            'given'     => (int) ($row['given'] ?? 0),
            'givers'    => (int) ($row['givers'] ?? 0),
        ];
    }

    /** @param array{title: string, description: string, category: string, location: ?string, status: string} $data */
    public function create(int $userId, array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare(
            'INSERT INTO articles (user_id, title, description, category, location, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId, $data['title'], $data['description'], $data['category'],
            $data['location'], $data['status'], $now, $now,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @param array{title: string, description: string, category: string, location: ?string, status: string} $data */
    public function update(int $id, int $userId, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE articles SET title = ?, description = ?, category = ?, location = ?, status = ?, updated_at = ?
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([
            $data['title'], $data['description'], $data['category'], $data['location'],
            $data['status'], date('Y-m-d H:i:s'), $id, $userId,
        ]);
    }

    public function updateStatus(int $id, int $userId, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE articles SET status = ?, updated_at = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$status, date('Y-m-d H:i:s'), $id, $userId]);
    }

    public function delete(int $id, int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM articles WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }

    // ---------- Photos ----------

    /** @return list<array{id: int, filename: string, position: int}> Photo principale en premier. */
    public function images(int $articleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, filename, position FROM article_images WHERE article_id = ? ORDER BY position, id'
        );
        $stmt->execute([$articleId]);

        return array_map(static fn (array $row) => [
            'id'       => (int) $row['id'],
            'filename' => (string) $row['filename'],
            'position' => (int) $row['position'],
        ], $stmt->fetchAll());
    }

    /** @param list<string> $filenames Ajoutées à la suite des photos existantes. */
    public function addImages(int $articleId, array $filenames): void
    {
        $next = count($this->images($articleId));
        $stmt = $this->db->prepare(
            'INSERT INTO article_images (article_id, filename, position, created_at) VALUES (?, ?, ?, ?)'
        );
        foreach ($filenames as $filename) {
            $stmt->execute([$articleId, $filename, $next++, date('Y-m-d H:i:s')]);
        }
    }

    /**
     * Retire des photos de l'annonce (ids étrangers à l'annonce ignorés).
     *
     * @param list<int> $imageIds
     * @return list<string> Fichiers à supprimer du disque.
     */
    public function removeImages(int $articleId, array $imageIds): array
    {
        $removed = [];
        foreach ($this->images($articleId) as $image) {
            if (in_array($image['id'], $imageIds, true)) {
                $this->db->prepare('DELETE FROM article_images WHERE id = ? AND article_id = ?')
                    ->execute([$image['id'], $articleId]);
                $removed[] = $image['filename'];
            }
        }
        $this->reorder($articleId, null);

        return $removed;
    }

    /** Place la photo choisie en tête et renumérote les positions de 0 à n-1. */
    public function reorder(int $articleId, ?int $coverId): void
    {
        $images = $this->images($articleId);
        usort($images, static fn ($a, $b) => ($b['id'] === $coverId) <=> ($a['id'] === $coverId));

        $stmt = $this->db->prepare('UPDATE article_images SET position = ? WHERE id = ? AND article_id = ?');
        foreach ($images as $position => $image) {
            if ($image['position'] !== $position) {
                $stmt->execute([$position, $image['id'], $articleId]);
            }
        }
    }

    /** @param callable(): mixed $callback */
    public function transaction(callable $callback): mixed
    {
        $this->db->beginTransaction();
        try {
            $result = $callback();
            $this->db->commit();

            return $result;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
