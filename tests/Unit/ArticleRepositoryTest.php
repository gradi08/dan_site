<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Support\Status;
use PDO;
use PHPUnit\Framework\TestCase;

final class ArticleRepositoryTest extends TestCase
{
    private PDO $db;
    private ArticleRepository $articles;
    private int $alice;
    private int $bob;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $this->db->exec('PRAGMA foreign_keys = ON');
        $this->db->exec((string) file_get_contents(BASE_PATH . '/database/schema.sqlite.sql'));

        $users = new UserRepository($this->db);
        $this->alice = $users->create('Alice', 'alice@test.cd', '+243810000001', 'password1');
        $this->bob = $users->create('Bob', 'bob@test.cd', '+243810000002', 'password1');
        $this->articles = new ArticleRepository($this->db);
    }

    private function make(int $userId, string $title, string $status = Status::PUBLISHED, string $category = 'autre'): int
    {
        return $this->articles->create($userId, [
            'title'       => $title,
            'description' => 'Description de test',
            'category'    => $category,
            'location'    => 'Gombe',
            'status'      => $status,
        ]);
    }

    public function testSearchOnlyReturnsPublishedArticles(): void
    {
        $this->make($this->alice, 'Table publiée');
        $this->make($this->alice, 'Table brouillon', Status::DRAFT);
        $this->make($this->alice, 'Table donnée', Status::GIVEN);

        $result = $this->articles->search(null, null, 1, 10);

        self::assertSame(1, $result['total']);
        self::assertSame('Table publiée', $result['items'][0]['title']);
    }

    public function testSearchFiltersByQueryAndCategory(): void
    {
        $this->make($this->alice, 'Vélo rouge', category: 'loisirs');
        $this->make($this->alice, 'Chaise', category: 'meubles');

        self::assertSame(1, $this->articles->search('vélo', null, 1, 10)['total']);
        self::assertSame(1, $this->articles->search(null, 'meubles', 1, 10)['total']);
        self::assertSame(0, $this->articles->search('vélo', 'meubles', 1, 10)['total']);
    }

    public function testLikeWildcardsAreEscaped(): void
    {
        $this->make($this->alice, 'Objet quelconque');

        self::assertSame(0, $this->articles->search('%', null, 1, 10)['total']);
        self::assertSame(0, $this->articles->search('_', null, 1, 10)['total']);
    }

    public function testPagination(): void
    {
        foreach (range(1, 5) as $i) {
            $this->make($this->alice, "Objet $i");
        }

        $page = $this->articles->search(null, null, 2, 2);

        self::assertSame(5, $page['total']);
        self::assertCount(2, $page['items']);
    }

    public function testOwnershipIsEnforced(): void
    {
        $id = $this->make($this->alice, 'À Alice');

        self::assertNull($this->articles->findOwned($id, $this->bob));

        $this->articles->delete($id, $this->bob);
        $this->articles->updateStatus($id, $this->bob, Status::DRAFT);

        $article = $this->articles->findOwned($id, $this->alice);
        self::assertNotNull($article);
        self::assertSame(Status::PUBLISHED, $article['status']);
    }

    public function testDraftsAreNotPubliclyVisible(): void
    {
        $id = $this->make($this->alice, 'Brouillon', Status::DRAFT);

        self::assertNull($this->articles->findPublic($id));
    }

    public function testStats(): void
    {
        $this->make($this->alice, 'A');
        $this->make($this->alice, 'B', Status::GIVEN);
        $this->make($this->bob, 'C', Status::DRAFT);

        self::assertSame(
            [Status::DRAFT => 0, Status::PUBLISHED => 1, Status::GIVEN => 1],
            $this->articles->statsForUser($this->alice),
        );
        self::assertSame(['published' => 1, 'given' => 1, 'givers' => 1], $this->articles->communityStats());
    }

    public function testDeletingUserCascadesToArticles(): void
    {
        $this->make($this->alice, 'A');
        $this->db->prepare('DELETE FROM users WHERE id = ?')->execute([$this->alice]);

        self::assertSame(0, (int) $this->db->query('SELECT COUNT(*) FROM articles')->fetchColumn());
    }

    public function testCoverIsFirstImageByPosition(): void
    {
        $id = $this->make($this->alice, 'Avec photos');
        $this->articles->addImages($id, ['a.jpg', 'b.jpg', 'c.jpg']);

        self::assertSame(['a.jpg', 'b.jpg', 'c.jpg'], array_column($this->articles->images($id), 'filename'));
        self::assertSame('a.jpg', $this->articles->findPublic($id)['image']);
        self::assertSame('a.jpg', $this->articles->latest(1)[0]['image']);
    }

    public function testReorderPutsChosenCoverFirstAndKeepsOthersInOrder(): void
    {
        $id = $this->make($this->alice, 'Avec photos');
        $this->articles->addImages($id, ['a.jpg', 'b.jpg', 'c.jpg']);
        $c = $this->articles->images($id)[2]['id'];

        $this->articles->reorder($id, $c);

        $images = $this->articles->images($id);
        self::assertSame(['c.jpg', 'a.jpg', 'b.jpg'], array_column($images, 'filename'));
        self::assertSame([0, 1, 2], array_column($images, 'position'));
        self::assertSame('c.jpg', $this->articles->findOwned($id, $this->alice)['image']);
    }

    public function testRemoveImagesIgnoresImagesOfOtherArticles(): void
    {
        $mine = $this->make($this->alice, 'À moi');
        $theirs = $this->make($this->bob, 'À Bob');
        $this->articles->addImages($mine, ['a.jpg', 'b.jpg']);
        $this->articles->addImages($theirs, ['bob.jpg']);
        $bobImage = $this->articles->images($theirs)[0]['id'];
        $a = $this->articles->images($mine)[0]['id'];

        $removed = $this->articles->removeImages($mine, [$a, $bobImage]);

        self::assertSame(['a.jpg'], $removed);
        self::assertSame(['b.jpg'], array_column($this->articles->images($mine), 'filename'));
        self::assertSame([0], array_column($this->articles->images($mine), 'position'));
        self::assertCount(1, $this->articles->images($theirs));
    }

    public function testAddImagesAppendsAfterExistingOnes(): void
    {
        $id = $this->make($this->alice, 'Photos');
        $this->articles->addImages($id, ['a.jpg']);
        $this->articles->addImages($id, ['b.jpg', 'c.jpg']);

        self::assertSame([0, 1, 2], array_column($this->articles->images($id), 'position'));
    }

    public function testDeletingArticleCascadesToImages(): void
    {
        $id = $this->make($this->alice, 'Photos');
        $this->articles->addImages($id, ['a.jpg', 'b.jpg']);

        $this->articles->delete($id, $this->alice);

        self::assertSame(0, (int) $this->db->query('SELECT COUNT(*) FROM article_images')->fetchColumn());
    }

    public function testTransactionRollsBackOnFailure(): void
    {
        try {
            $this->articles->transaction(function (): void {
                $this->make($this->alice, 'Annulé');
                throw new \RuntimeException('échec');
            });
        } catch (\RuntimeException) {
        }

        self::assertSame(0, (int) $this->db->query('SELECT COUNT(*) FROM articles')->fetchColumn());
    }
}
