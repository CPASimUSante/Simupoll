<?php

namespace CPASimUSante\SimupollBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2016/05/09 08:54:08
 */
class Version20160509085407 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE cpasimusante__simupoll 
            ADD has_paper TINYINT(1) DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE cpasimusante__simupoll 
            DROP has_paper
        ");
    }
}