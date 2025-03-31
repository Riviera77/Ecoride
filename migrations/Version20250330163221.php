<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250330163221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE car (id SERIAL NOT NULL, users_id INT DEFAULT NULL, registration VARCHAR(50) NOT NULL, date_first_registration VARCHAR(50) NOT NULL, model VARCHAR(50) NOT NULL, color VARCHAR(50) NOT NULL, mark VARCHAR(50) NOT NULL, energy BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_773DE69D67B3B43D ON car (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE carpooling (id SERIAL NOT NULL, cars_id INT DEFAULT NULL, departure_address VARCHAR(50) NOT NULL, arrival_address VARCHAR(50) NOT NULL, departure_date DATE NOT NULL, arrival_date DATE NOT NULL, departure_time TIME(0) WITHOUT TIME ZONE NOT NULL, arrival_time TIME(0) WITHOUT TIME ZONE NOT NULL, price DOUBLE PRECISION NOT NULL, number_seats INT NOT NULL, preference VARCHAR(50) DEFAULT NULL, status VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6CC153F18702F506 ON carpooling (cars_id)
        SQL);
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
            CREATE TABLE credit (id SERIAL NOT NULL, users_id INT DEFAULT NULL, balance INT NOT NULL, transaction_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1CC16EFE67B3B43D ON credit (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, photo BYTEA DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car ADD CONSTRAINT FK_773DE69D67B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling ADD CONSTRAINT FK_6CC153F18702F506 FOREIGN KEY (cars_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user ADD CONSTRAINT FK_257FA72FAFB2200A FOREIGN KEY (carpooling_id) REFERENCES carpooling (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user ADD CONSTRAINT FK_257FA72FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE67B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car DROP CONSTRAINT FK_773DE69D67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling DROP CONSTRAINT FK_6CC153F18702F506
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user DROP CONSTRAINT FK_257FA72FAFB2200A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carpooling_user DROP CONSTRAINT FK_257FA72FA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE credit DROP CONSTRAINT FK_1CC16EFE67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE car
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpooling
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carpooling_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE credit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
