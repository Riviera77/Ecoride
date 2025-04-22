<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421125617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE carpooling_participation (id SERIAL NOT NULL, users_id INT NOT NULL, carpooling_id INT NOT NULL, role VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D599D48F67B3B43D ON carpooling_participation (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D599D48FAFB2200A ON carpooling_participation (carpooling_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_participation ADD CONSTRAINT FK_D599D48F67B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_participation ADD CONSTRAINT FK_D599D48FAFB2200A FOREIGN KEY (carpooling_id) REFERENCES carpooling (id) NOT DEFERRABLE INITIALLY IMMEDIATE
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
            ALTER TABLE carpooling_participation DROP CONSTRAINT FK_D599D48F67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_participation DROP CONSTRAINT FK_D599D48FAFB2200A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpooling_participation
        SQL);
    }
}
