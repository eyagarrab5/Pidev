<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225154659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_covoiturage DROP FOREIGN KEY FK_89E0C1FD51E8871B');
        $this->addSql('DROP INDEX IDX_89E0C1FD51E8871B ON demande_covoiturage');
        $this->addSql('ALTER TABLE demande_covoiturage DROP favoris_id');
        $this->addSql('ALTER TABLE favoris ADD demande_id INT DEFAULT NULL, ADD id_passager INT NOT NULL');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C43280E95E18 FOREIGN KEY (demande_id) REFERENCES demande_covoiturage (id)');
        $this->addSql('CREATE INDEX IDX_8933C43280E95E18 ON favoris (demande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_covoiturage ADD favoris_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demande_covoiturage ADD CONSTRAINT FK_89E0C1FD51E8871B FOREIGN KEY (favoris_id) REFERENCES favoris (id)');
        $this->addSql('CREATE INDEX IDX_89E0C1FD51E8871B ON demande_covoiturage (favoris_id)');
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C43280E95E18');
        $this->addSql('DROP INDEX IDX_8933C43280E95E18 ON favoris');
        $this->addSql('ALTER TABLE favoris DROP demande_id, DROP id_passager');
    }
}
