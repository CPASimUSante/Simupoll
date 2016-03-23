<?php

namespace CPASimUSante\SimupollBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2016/03/22 08:28:11
 */
class Version20160322082810 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE cpasimusante__simupoll_statcategorygroup (
                id INT AUTO_INCREMENT NOT NULL, 
                statmanage_id INT DEFAULT NULL, 
                title VARCHAR(255) NOT NULL, 
                `group` VARCHAR(255) DEFAULT NULL, 
                INDEX IDX_4C4C706756581D86 (statmanage_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            ALTER TABLE cpasimusante__simupoll_statcategorygroup 
            ADD CONSTRAINT FK_4C4C706756581D86 FOREIGN KEY (statmanage_id) 
            REFERENCES cpasimusante__simupoll_statmanage (id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP TABLE cpasimusante__simupoll_statcategorygroup
        ");
    }
}