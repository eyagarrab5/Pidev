<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219013929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE tags tags VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE forum_posts CHANGE tags tags VARCHAR(255) DEFAULT NULL, CHANGE attachment attachment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE auth_method auth_method VARCHAR(50) DEFAULT NULL, CHANGE image image VARCHAR(50) DEFAULT NULL, CHANGE telephone telephone VARCHAR(30) DEFAULT NULL, CHANGE vehicule vehicule VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE tags tags VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE forum_posts CHANGE tags tags VARCHAR(255) DEFAULT \'NULL\', CHANGE attachment attachment VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE auth_method auth_method VARCHAR(50) DEFAULT \'NULL\', CHANGE image image VARCHAR(50) DEFAULT \'NULL\', CHANGE telephone telephone VARCHAR(30) DEFAULT \'NULL\', CHANGE vehicule vehicule VARCHAR(100) DEFAULT \'NULL\'');
    }
}
