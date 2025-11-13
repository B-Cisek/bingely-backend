<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251102222153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "tv_show" (id UUID NOT NULL, tmdb_id INT NOT NULL, is_adult BOOLEAN NOT NULL, backdrop_path VARCHAR(255) NOT NULL, origin_country JSON NOT NULL, original_language VARCHAR(255) NOT NULL, popularity DOUBLE PRECISION NOT NULL, poster_path VARCHAR(255) NOT NULL, first_air_date DATE NOT NULL, vote_average DOUBLE PRECISION NOT NULL, vote_count INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F1399B9D55BCC5E5 ON "tv_show" (tmdb_id)');
        $this->addSql('COMMENT ON COLUMN "tv_show".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "tv_show".first_air_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "tv_show".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "tv_show".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tv_show_genre_mapping (tv_show_id UUID NOT NULL, tv_show_genre_id UUID NOT NULL, PRIMARY KEY(tv_show_id, tv_show_genre_id))');
        $this->addSql('CREATE INDEX IDX_BD61D4DE5E3A35BB ON tv_show_genre_mapping (tv_show_id)');
        $this->addSql('CREATE INDEX IDX_BD61D4DEC653B18C ON tv_show_genre_mapping (tv_show_genre_id)');
        $this->addSql('COMMENT ON COLUMN tv_show_genre_mapping.tv_show_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tv_show_genre_mapping.tv_show_genre_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "tv_show_genre" (id UUID NOT NULL, tmdb_id INT NOT NULL, name VARCHAR(50) NOT NULL, translations JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3763623455BCC5E5 ON "tv_show_genre" (tmdb_id)');
        $this->addSql('COMMENT ON COLUMN "tv_show_genre".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "tv_show_translation" (id UUID NOT NULL, tv_show_id UUID NOT NULL, language VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, overview TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F7F97C585E3A35BB ON "tv_show_translation" (tv_show_id)');
        $this->addSql('COMMENT ON COLUMN "tv_show_translation".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "tv_show_translation".tv_show_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tv_show_genre_mapping ADD CONSTRAINT FK_BD61D4DE5E3A35BB FOREIGN KEY (tv_show_id) REFERENCES "tv_show" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tv_show_genre_mapping ADD CONSTRAINT FK_BD61D4DEC653B18C FOREIGN KEY (tv_show_genre_id) REFERENCES "tv_show_genre" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "tv_show_translation" ADD CONSTRAINT FK_F7F97C585E3A35BB FOREIGN KEY (tv_show_id) REFERENCES "tv_show" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tv_show_genre_mapping DROP CONSTRAINT FK_BD61D4DE5E3A35BB');
        $this->addSql('ALTER TABLE tv_show_genre_mapping DROP CONSTRAINT FK_BD61D4DEC653B18C');
        $this->addSql('ALTER TABLE "tv_show_translation" DROP CONSTRAINT FK_F7F97C585E3A35BB');
        $this->addSql('DROP TABLE "tv_show"');
        $this->addSql('DROP TABLE tv_show_genre_mapping');
        $this->addSql('DROP TABLE "tv_show_genre"');
        $this->addSql('DROP TABLE "tv_show_translation"');
    }
}
