<?php

namespace CPASimUSante\SimupollBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution.
 *
 * Generation date: 2016/03/28 06:37:40
 */
class Version20160328183738 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statcategorygroup 
            DROP FOREIGN KEY FK_4C4C706756581D86
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statcategorygroup 
            ADD CONSTRAINT FK_4C4C706756581D86 FOREIGN KEY (statmanage_id) 
            REFERENCES cpasimusante__simupoll_statmanage (id) 
            ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statcategorygroup 
            DROP FOREIGN KEY FK_4C4C706756581D86
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statcategorygroup 
            ADD CONSTRAINT FK_4C4C706756581D86 FOREIGN KEY (statmanage_id) 
            REFERENCES cpasimusante__simupoll_statmanage (id)
        ');
    }
}
