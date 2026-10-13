<?php

declare(strict_types=1);

/**
 * Initialise la base de données.
 *
 *   php bin/setup.php            crée les tables (idempotent)
 *   php bin/setup.php --seed     + données de démonstration
 *   php bin/setup.php --fresh    supprime puis recrée tout (avec --seed si demandé)
 */

use App\Core\Database;
use App\Support\Status;

if (PHP_SAPI !== 'cli') {
    exit("Ce script s'exécute uniquement en ligne de commande.\n");
}

/** @var \App\Core\App $app */
$app = require dirname(__DIR__) . '/src/bootstrap.php';
$db = $app->db();
$driver = Database::driver($db);
$args = array_slice($argv, 1);

if (in_array('--fresh', $args, true)) {
    if ($driver === 'mysql') {
        $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    }
    foreach (['login_attempts', 'article_images', 'articles', 'users'] as $table) {
        $db->exec("DROP TABLE IF EXISTS $table");
    }
    if ($driver === 'mysql') {
        $db->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
    echo "• Tables supprimées\n";
}

$schema = file_get_contents(BASE_PATH . "/database/schema.$driver.sql");
foreach (explode(';', (string) $schema) as $statement) {
    $sql = trim((string) preg_replace('/^\s*--.*$/m', '', $statement));
    if ($sql !== '') {
        $db->exec($sql);
    }
}
echo "• Schéma $driver à jour\n";

if (!in_array('--seed', $args, true)) {
    exit(0);
}

if ((int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0) {
    echo "• Données déjà présentes, seed ignoré (utilisez --fresh --seed pour repartir de zéro)\n";
    exit(0);
}

$users = $app->users();
// Photos Unsplash ; « rect » recadre la même photo pour simuler des vues de détail.
$img = static fn (string $id, ?string $rect = null): string => "https://images.unsplash.com/photo-$id?"
    . ($rect ? "rect=$rect&" : '') . 'w=1200&h=900&q=75&auto=format&fit=crop';
$sofa = '1555041469-a586c61ea9bc';
$bike = '1485965120184-e220f721d03e';
$shirts = '1602810318383-e386cc2a3ccf';

// Comptes de démonstration (mot de passe : demo1234)
$ids = [
    'demo'  => $users->create('Compte Démo', 'demo@videgrenier.test', '+243810000001', 'demo1234'),
    'nadia' => $users->create('Nadia K.', 'nadia@videgrenier.test', '+243810000002', 'demo1234'),
    'joel'  => $users->create('Joël M.', 'joel@videgrenier.test', '+243810000003', 'demo1234'),
];

$items = [
    ['nadia', 'Canapé 3 places vert sapin', "Velours un peu usé sur l'accoudoir gauche, sinon très confortable. Il faut venir le chercher à deux, il est lourd.", 'meubles', 'Gombe', [$img($sofa), $img($sofa, '0.05,0.4,0.35,0.35'), $img($sofa, '0.55,0.35,0.4,0.4')], Status::PUBLISHED, 2],
    ['joel', 'Vélo de ville, taille M', 'Pneus neufs, freins réglés le mois dernier. La selle grince un peu. Idéal pour un étudiant.', 'loisirs', 'Lingwala', [$img($bike), $img($bike, '0.2,0.25,0.3,0.3'), $img($bike, '0.5,0.45,0.4,0.45')], Status::PUBLISHED, 5],
    ['demo', 'Lot de 3 chemises homme (L)', 'Bleu, blanc et bordeaux. Portées quelques fois, lavées et repassées.', 'vetements', 'Limete', [$img($shirts), $img($shirts, '0.35,0.2,0.4,0.5')], Status::PUBLISHED, 9],
    ['nadia', 'Commode blanche 4 tiroirs', 'Tous les tiroirs coulissent bien. Une petite rayure sur le dessus.', 'meubles', 'Ngaliema', [$img('1513694203232-719a280e022f')], Status::PUBLISHED, 14],
    ['joel', 'Casque audio filaire', 'Son correct, coussinets à changer à terme. Câble jack 3,5 mm fourni.', 'electronique', 'Bandalungwa', [$img('1505740420928-5e560c06d30e')], Status::PUBLISHED, 20],
    ['demo', 'Tabouret de bar en bois', 'Hauteur 75 cm, bois brut. Peut servir de table de chevet.', 'meubles', 'Kintambo', [$img('1503602642458-232111445657')], Status::PUBLISHED, 26],
    ['nadia', 'Combinaison bébé 6–12 mois', 'Combinaison polaire avec capuche à oreilles. Très chaude, parfaite pour la saison sèche.', 'enfants', 'Gombe', [$img('1522771930-78848d9293e8')], Status::PUBLISHED, 31],
    ['joel', 'Table ronde + chaise', 'Petite table de 80 cm de diamètre et sa chaise assortie. Pour un studio ou un balcon.', 'meubles', 'Kalamu', [$img('1519710164239-da123dc03ef4')], Status::PUBLISHED, 40],
    ['demo', 'Recueil de poésie', 'En anglais, bon état. Pour qui veut pratiquer la lecture.', 'livres', 'Limete', [$img('1544947950-fa07a98d237f')], Status::GIVEN, 55],
    ['nadia', 'Ordinateur portable (à réparer)', "L'écran fonctionne, la batterie ne tient plus la charge. Pour pièces ou pour un bricoleur.", 'electronique', 'Ngaliema', [$img('1517336714731-489689fd1ca8')], Status::DRAFT, 60],
];

$article = $db->prepare(
    'INSERT INTO articles (user_id, title, description, category, location, status, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$photo = $db->prepare('INSERT INTO article_images (article_id, filename, position, created_at) VALUES (?, ?, ?, ?)');
foreach ($items as [$owner, $title, $description, $category, $location, $photos, $status, $hoursAgo]) {
    $date = date('Y-m-d H:i:s', time() - $hoursAgo * 3600);
    $article->execute([$ids[$owner], $title, $description, $category, $location, $status, $date, $date]);
    $articleId = (int) $db->lastInsertId();
    foreach ($photos as $position => $url) {
        $photo->execute([$articleId, $url, $position, $date]);
    }
}

echo '• ' . count($ids) . ' comptes et ' . count($items) . " annonces de démonstration créés\n";
echo "  Connexion démo : demo@videgrenier.test / demo1234\n";
