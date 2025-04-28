<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427203526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE carpooling_user (carpooling_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(carpooling_id, user_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_257FA72FAFB2200A ON carpooling_user (carpooling_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_257FA72FA76ED395 ON carpooling_user (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user ADD CONSTRAINT FK_257FA72FAFB2200A FOREIGN KEY (carpooling_id) REFERENCES carpooling (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user ADD CONSTRAINT FK_257FA72FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling ADD users_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling ADD CONSTRAINT FK_6CC153F167B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6CC153F167B3B43D ON carpooling (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER photo TYPE VARCHAR(50)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user DROP CONSTRAINT FK_257FA72FAFB2200A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user DROP CONSTRAINT FK_257FA72FA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpooling_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER photo TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling DROP CONSTRAINT FK_6CC153F167B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6CC153F167B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling DROP users_id
        SQL);
    }
}
