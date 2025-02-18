<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209173814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
         // Créer la table `comments`
    $this->addSql('CREATE TABLE comments (
        id INT AUTO_INCREMENT NOT NULL,
        content LONGTEXT NOT NULL,
        created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
        updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
        likes INT DEFAULT NULL,
        tags VARCHAR(255) NOT NULL,
        attachment VARCHAR(255) NOT NULL,
        user_id INT NOT NULL,
        forum_post_id INT NOT NULL,
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

    // Créer la table `forum_posts`
    $this->addSql('CREATE TABLE forum_posts (
        id INT AUTO_INCREMENT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
        updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
        category VARCHAR(100) NOT NULL,
        likes INT DEFAULT NULL,
        tags VARCHAR(255) DEFAULT NULL,
        attachment VARCHAR(255) DEFAULT NULL,
        comments_count INT NOT NULL DEFAULT 0,
        user_id INT NOT NULL, -- Ajout de la colonne user_id
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Ajouter ou modifier des colonnes
    $this->addSql('ALTER TABLE forum_posts ADD user_id INT NOT NULL');
    $this->addSql('ALTER TABLE forum_posts ADD CONSTRAINT FK_FORUM_POSTS_USER FOREIGN KEY (user_id) REFERENCES user (id)');

    // Ajouter la contrainte de clé étrangère pour `comments`
    $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_COMMENTS_USER FOREIGN KEY (user_id) REFERENCES user (id)');
    $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_COMMENTS_FORUM_POST FOREIGN KEY (forum_post_id) REFERENCES forum_posts (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE forum_posts');
        $this->addSql('DROP TABLE messenger_messages');
        // Supprimer la contrainte de clé étrangère
    $this->addSql('ALTER TABLE forum_posts DROP FOREIGN KEY FK_FORUM_POSTS_USER');

    // Supprimer la colonne `user_id`
    $this->addSql('ALTER TABLE forum_posts DROP COLUMN user_id');
    $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_COMMENTS_USER');
    $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_COMMENTS_FORUM_POST');
    }
}
