<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428055630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE carpooling_passengers (carpooling_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(carpooling_id, user_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B28A59EAAFB2200A ON carpooling_passengers (carpooling_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B28A59EAA76ED395 ON carpooling_passengers (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_passengers ADD CONSTRAINT FK_B28A59EAAFB2200A FOREIGN KEY (carpooling_id) REFERENCES carpooling (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_passengers ADD CONSTRAINT FK_B28A59EAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user DROP CONSTRAINT fk_257fa72fafb2200a
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user DROP CONSTRAINT fk_257fa72fa76ed395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpooling_user
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE carpooling_user (carpooling_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(carpooling_id, user_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_257fa72fa76ed395 ON carpooling_user (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_257fa72fafb2200a ON carpooling_user (carpooling_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user ADD CONSTRAINT fk_257fa72fafb2200a FOREIGN KEY (carpooling_id) REFERENCES carpooling (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user ADD CONSTRAINT fk_257fa72fa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_passengers DROP CONSTRAINT FK_B28A59EAAFB2200A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_passengers DROP CONSTRAINT FK_B28A59EAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpooling_passengers
        SQL);
    }
}
