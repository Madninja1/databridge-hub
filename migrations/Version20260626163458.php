<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260626163458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add triggers for automatic updated_at timestamps';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION set_updated_at()
            RETURNS trigger AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER trg_app_user_set_updated_at
            BEFORE UPDATE ON app_user
            FOR EACH ROW
            EXECUTE FUNCTION set_updated_at();
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TRIGGER trg_company_set_updated_at
            BEFORE UPDATE ON company
            FOR EACH ROW
            EXECUTE FUNCTION set_updated_at();
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TRIGGER trg_company_membership_set_updated_at
            BEFORE UPDATE ON company_membership
            FOR EACH ROW
            EXECUTE FUNCTION set_updated_at();
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS trg_company_membership_set_updated_at ON company_membership');
        $this->addSql('DROP TRIGGER IF EXISTS trg_company_set_updated_at ON company');
        $this->addSql('DROP TRIGGER IF EXISTS trg_app_user_set_updated_at ON app_user');

        $this->addSql('DROP FUNCTION IF EXISTS set_updated_at');
    }
}
