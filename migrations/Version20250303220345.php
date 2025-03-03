<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303220345 extends AbstractMigration
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
        $this->addSql('ALTER TABLE proposition_covoiturage CHANGE conducteur_id conducteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proposition_covoiturage ADD CONSTRAINT FK_5433056AF16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5433056AF16F4AC6 ON proposition_covoiturage (conducteur_id)');
        $this->addSql('ALTER TABLE reservation CHANGE passager_id passager_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495571A51189 FOREIGN KEY (passager_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_42C8495571A51189 ON reservation (passager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_covoiturage DROP FOREIGN KEY FK_63EE14A9F16F4AC6');
        $this->addSql('DROP INDEX IDX_63EE14A9F16F4AC6 ON offre_covoiturage');
        $this->addSql('ALTER TABLE proposition_covoiturage DROP FOREIGN KEY FK_5433056AF16F4AC6');
        $this->addSql('DROP INDEX IDX_5433056AF16F4AC6 ON proposition_covoiturage');
        $this->addSql('ALTER TABLE proposition_covoiturage CHANGE conducteur_id conducteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495571A51189');
        $this->addSql('DROP INDEX IDX_42C8495571A51189 ON reservation');
        $this->addSql('ALTER TABLE reservation CHANGE passager_id passager_id INT NOT NULL');
    }
}
