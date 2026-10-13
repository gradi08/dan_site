-- VideGrenier+ — schéma MySQL / MariaDB
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
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

CREATE TABLE IF NOT EXISTS articles (
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

CREATE TABLE IF NOT EXISTS article_images (
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
