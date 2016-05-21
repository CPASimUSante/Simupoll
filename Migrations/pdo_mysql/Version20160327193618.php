<?php

namespace CPASimUSante\SimupollBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution.
 *
 * Generation date: 2016/03/27 07:36:20
 */
class Version20160327193618 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statcategorygroup CHANGE `group` categorygroup VARCHAR(255) DEFAULT NULL
        ');
    }

    public function down(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statcategorygroup CHANGE categorygroup `group` VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci
        ');
    }
}
