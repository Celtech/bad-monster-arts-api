-- Doctrine Migration File Generated on 2020-07-21 05:07:15

-- Version 20200312183510
CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
INSERT INTO migration_versions (version, executed_at) VALUES ('20200312183510', CURRENT_TIMESTAMP);

-- Version 20200312185413
CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, posts_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3AF34668D5E258C5 (posts_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE posts (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_885DBAFAF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE categories ADD CONSTRAINT FK_3AF34668D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id);
ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES user (id);
ALTER TABLE user ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL;
INSERT INTO migration_versions (version, executed_at) VALUES ('20200312185413', CURRENT_TIMESTAMP);

-- Version 20200312185725
ALTER TABLE posts ADD title_image VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL;
INSERT INTO migration_versions (version, executed_at) VALUES ('20200312185725', CURRENT_TIMESTAMP);

-- Version 20200312185746
ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668D5E258C5;
DROP INDEX IDX_3AF34668D5E258C5 ON categories;
ALTER TABLE categories DROP posts_id;
INSERT INTO migration_versions (version, executed_at) VALUES ('20200312185746', CURRENT_TIMESTAMP);

-- Version 20200312194326
ALTER TABLE categories ADD posts_id INT DEFAULT NULL;
ALTER TABLE categories ADD CONSTRAINT FK_3AF34668D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id);
CREATE INDEX IDX_3AF34668D5E258C5 ON categories (posts_id);
INSERT INTO migration_versions (version, executed_at) VALUES ('20200312194326', CURRENT_TIMESTAMP);

-- Version 20200318163527
CREATE TABLE posts_categories (posts_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_A8C3AA46D5E258C5 (posts_id), INDEX IDX_A8C3AA46A21214B7 (categories_id), PRIMARY KEY(posts_id, categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE posts_categories ADD CONSTRAINT FK_A8C3AA46D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE;
ALTER TABLE posts_categories ADD CONSTRAINT FK_A8C3AA46A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE;
ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES user (id);
INSERT INTO migration_versions (version, executed_at) VALUES ('20200318163527', CURRENT_TIMESTAMP);

-- Version 20200721045952
CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, parent_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5F9E962A4B89032C (post_id), INDEX IDX_5F9E962A727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4B89032C FOREIGN KEY (post_id) REFERENCES posts (id);
ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A727ACA70 FOREIGN KEY (parent_id) REFERENCES comments (id);
ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668D5E258C5;
DROP INDEX IDX_3AF34668D5E258C5 ON categories;
ALTER TABLE categories DROP posts_id;
ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES user (id);
CREATE INDEX IDX_885DBAFAF675F31B ON posts (author_id);
INSERT INTO migration_versions (version, executed_at) VALUES ('20200721045952', CURRENT_TIMESTAMP);