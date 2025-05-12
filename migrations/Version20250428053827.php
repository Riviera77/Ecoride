<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428053827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE carpooling_participation_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_participation DROP CONSTRAINT fk_d599d48f67b3b43d
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_participation DROP CONSTRAINT fk_d599d48fafb2200a
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpooling_participation
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE carpooling_participation_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE carpooling_participation (id SERIAL NOT NULL, users_id INT NOT NULL, carpooling_id INT NOT NULL, role VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d599d48fafb2200a ON carpooling_participation (carpooling_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d599d48f67b3b43d ON carpooling_participation (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_participation ADD CONSTRAINT fk_d599d48f67b3b43d FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_participation ADD CONSTRAINT fk_d599d48fafb2200a FOREIGN KEY (carpooling_id) REFERENCES carpooling (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
