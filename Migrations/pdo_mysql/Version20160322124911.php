<?php

namespace CPASimUSante\SimupollBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2016/03/22 12:49:12
 */
class Version20160322124911 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE cpasimusante__simupoll_statmanage CHANGE completecategorylist completecategorylist LONGTEXT DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE cpasimusante__simupoll_statmanage CHANGE completecategorylist completecategorylist VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci
        ");
    }
}