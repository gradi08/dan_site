-- Migration de l'ancienne base (v1 : tables users/articles en MyISAM, colonnes en français)
-- vers le schéma v2. À exécuter UNE FOIS dans phpMyAdmin, après une sauvegarde.
-- Les anciennes tables sont conservées sous le nom legacy_* pour pouvoir revenir en arrière.
-- Pensez ensuite à copier les fichiers de l'ancien dossier img/ vers public/uploads/.

SET NAMES utf8mb4;

RENAME TABLE users TO legacy_users, articles TO legacy_articles;

-- (schéma v2 : copie de database/schema.mysql.sql)
CREATE TABLE users (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100)  NOT NULL,
    email         VARCHAR(190)  NOT NULL,
    phone         VARCHAR(20)   NOT NULL,
    password_hash VARCHAR(255)  NOT NULL,
    created_at    DATETIME      NOT NULL,
    updated_at    DATETIME      NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE articles (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED  NOT NULL,
    title       VARCHAR(120)  NOT NULL,
    description TEXT          NOT NULL,
    category    VARCHAR(30)   NOT NULL DEFAULT 'autre',
    location    VARCHAR(80)   NULL,
    status      ENUM('draft', 'published', 'given') NOT NULL DEFAULT 'draft',
    created_at  DATETIME      NOT NULL,
    updated_at  DATETIME      NOT NULL,
    PRIMARY KEY (id),
    KEY articles_status_created_idx (status, created_at),
    KEY articles_user_idx (user_id),
    CONSTRAINT articles_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE article_images (
    id         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    article_id INT UNSIGNED     NOT NULL,
    filename   VARCHAR(255)     NOT NULL,
    position   TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME         NOT NULL,
    PRIMARY KEY (id),
    KEY article_images_article_idx (article_id, position),
    CONSTRAINT article_images_article_fk FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    throttle_key CHAR(64)     NOT NULL,
    attempted_at DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY login_attempts_key_idx (throttle_key, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (id, name, email, phone, password_hash, created_at, updated_at)
SELECT id, nom, LOWER(email), Tel, mot_de_passe,
       COALESCE(date_inscription, NOW()), COALESCE(date_inscription, NOW())
FROM legacy_users;

INSERT INTO articles (id, user_id, title, description, category, status, created_at, updated_at)
SELECT a.id, a.user_id, LEFT(a.titre, 120), COALESCE(a.description, ''), 'autre',
       CASE a.statut WHEN 'publie' THEN 'published' WHEN 'offert' THEN 'given' ELSE 'draft' END,
       COALESCE(a.date_creation, NOW()), COALESCE(a.date_creation, NOW())
FROM legacy_articles a
INNER JOIN users u ON u.id = a.user_id; -- ignore les annonces orphelines

-- La photo unique de la v1 devient la photo principale (position 0).
INSERT INTO article_images (article_id, filename, position, created_at)
SELECT a.id, l.image, 0, a.created_at
FROM articles a
INNER JOIN legacy_articles l ON l.id = a.id
WHERE l.image IS NOT NULL AND l.image <> '';
