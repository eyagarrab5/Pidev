<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305090736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_covoiturage (id INT AUTO_INCREMENT NOT NULL, passager_id INT NOT NULL, depart VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, date DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, budget DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favori (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favoris (id INT AUTO_INCREMENT NOT NULL, demande_id INT DEFAULT NULL, id_passager INT NOT NULL, INDEX IDX_8933C43280E95E18 (demande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_covoiturage (id INT AUTO_INCREMENT NOT NULL, depart VARCHAR(255) NOT NULL, conducteur_id INT NOT NULL, destination VARCHAR(255) NOT NULL, mat_vehicule INT NOT NULL, places_dispo INT NOT NULL, date DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, img VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposition_covoiturage (id INT AUTO_INCREMENT NOT NULL, demande_id INT DEFAULT NULL, conducteur_id INT NOT NULL, statut VARCHAR(255) NOT NULL, places_dispo INT NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_5433056A80E95E18 (demande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, offre_id INT DEFAULT NULL, passager_id INT NOT NULL, statut VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_42C849554CC8505A (offre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, testeya VARCHAR(255) NOT NULL, testsara DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C43280E95E18 FOREIGN KEY (demande_id) REFERENCES demande_covoiturage (id)');
        $this->addSql('ALTER TABLE proposition_covoiturage ADD CONSTRAINT FK_5433056A80E95E18 FOREIGN KEY (demande_id) REFERENCES demande_covoiturage (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554CC8505A FOREIGN KEY (offre_id) REFERENCES offre_covoiturage (id)');
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE forum_posts CHANGE is_pinned is_pinned TINYINT(1) DEFAULT 0 NOT NULL, CHANGE favorited_by favorited_by JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE notifications notifications JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE reservation_vehicule CHANGE notifications notifications JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE reservation_vehicule ADD CONSTRAINT FK_B49A1D095258F8E6 FOREIGN KEY (id_vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE vehicule CHANGE notifications notifications JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE likes likes INT DEFAULT 0 NOT NULL, CHANGE dislikes dislikes INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(1024) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, executed_at DATETIME DEFAULT NULL, execution_time INT DEFAULT NULL, PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C43280E95E18');
        $this->addSql('ALTER TABLE proposition_covoiturage DROP FOREIGN KEY FK_5433056A80E95E18');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554CC8505A');
        $this->addSql('DROP TABLE demande_covoiturage');
        $this->addSql('DROP TABLE favori');
        $this->addSql('DROP TABLE favoris');
        $this->addSql('DROP TABLE offre_covoiturage');
        $this->addSql('DROP TABLE proposition_covoiturage');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE test');
        $this->addSql('ALTER TABLE forum_posts CHANGE is_pinned is_pinned TINYINT(1) DEFAULT 0, CHANGE notifications notifications JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE favorited_by favorited_by JSON DEFAULT \'json_array()\' NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE reservation_vehicule DROP FOREIGN KEY FK_B49A1D095258F8E6');
        $this->addSql('ALTER TABLE reservation_vehicule CHANGE notifications notifications JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE vehicule CHANGE notifications notifications JSON DEFAULT \'[]\' NOT NULL COMMENT \'(DC2Type:json)\', CHANGE likes likes INT DEFAULT 0, CHANGE dislikes dislikes INT DEFAULT 0');
    }
}
