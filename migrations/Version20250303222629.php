<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303222629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_covoiturage ADD CONSTRAINT FK_63EE14A9F16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_63EE14A9F16F4AC6 ON offre_covoiturage (conducteur_id)');
        $this->addSql('ALTER TABLE proposition_covoiturage ADD conducteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD passager_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_covoiturage DROP FOREIGN KEY FK_63EE14A9F16F4AC6');
        $this->addSql('DROP INDEX IDX_63EE14A9F16F4AC6 ON offre_covoiturage');
        $this->addSql('ALTER TABLE proposition_covoiturage DROP conducteur_id');
        $this->addSql('ALTER TABLE reservation DROP passager_id');
    }
}
