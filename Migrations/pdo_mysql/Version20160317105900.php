<?php

namespace CPASimUSante\SimupollBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution.
 *
 * Generation date: 2016/03/17 10:59:02
 */
class Version20160317105900 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll_organization (
                id INT AUTO_INCREMENT NOT NULL, 
                simupoll_id INT DEFAULT NULL, 
                choice VARCHAR(255) NOT NULL, 
                choice_data LONGTEXT NOT NULL, 
                category_list LONGTEXT NOT NULL, 
                INDEX IDX_2F384248C9D1F0D2 (simupoll_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll (
                id INT AUTO_INCREMENT NOT NULL, 
                title VARCHAR(255) NOT NULL, 
                resourceNode_id INT DEFAULT NULL, 
                UNIQUE INDEX UNIQ_BC9663DBB87FAB32 (resourceNode_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql("
            CREATE TABLE cpasimusante__simupoll_proposition (
                id INT AUTO_INCREMENT NOT NULL, 
                question_id INT DEFAULT NULL, 
                choice VARCHAR(255) NOT NULL, 
                mark DOUBLE PRECISION DEFAULT '0' NOT NULL, 
                INDEX IDX_1F8DDB511E27F6BF (question_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll_paper (
                id INT AUTO_INCREMENT NOT NULL, 
                user_id INT DEFAULT NULL, 
                simupoll_id INT DEFAULT NULL, 
                period_id INT DEFAULT NULL, 
                start DATETIME NOT NULL, 
                end DATETIME DEFAULT NULL, 
                num_paper INT NOT NULL, 
                INDEX IDX_3626D4F7A76ED395 (user_id), 
                INDEX IDX_3626D4F7C9D1F0D2 (simupoll_id), 
                INDEX IDX_3626D4F7EC8B7ADE (period_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll_answer (
                id INT AUTO_INCREMENT NOT NULL, 
                paper_id INT DEFAULT NULL, 
                question_id INT DEFAULT NULL, 
                mark DOUBLE PRECISION NOT NULL, 
                answer LONGTEXT NOT NULL, 
                INDEX IDX_DA8A47FE6758861 (paper_id), 
                INDEX IDX_DA8A47F1E27F6BF (question_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll_question (
                id INT AUTO_INCREMENT NOT NULL, 
                simupoll_id INT DEFAULT NULL, 
                category_id INT DEFAULT NULL, 
                title VARCHAR(255) DEFAULT NULL, 
                orderq INT DEFAULT 0 NOT NULL, 
                INDEX IDX_B111E49AC9D1F0D2 (simupoll_id), 
                INDEX IDX_B111E49A12469DE2 (category_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll_period (
                id INT AUTO_INCREMENT NOT NULL, 
                simupoll_id INT DEFAULT NULL, 
                start DATETIME NOT NULL, 
                stop DATETIME NOT NULL, 
                INDEX IDX_12CDF094C9D1F0D2 (simupoll_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll_category (
                id INT AUTO_INCREMENT NOT NULL, 
                parent_id INT DEFAULT NULL, 
                user_id INT DEFAULT NULL, 
                simupoll_id INT DEFAULT NULL, 
                name VARCHAR(255) NOT NULL, 
                lft INT NOT NULL, 
                lvl INT NOT NULL, 
                rgt INT NOT NULL, 
                root INT DEFAULT NULL, 
                INDEX IDX_1AAB415727ACA70 (parent_id), 
                INDEX IDX_1AAB415A76ED395 (user_id), 
                INDEX IDX_1AAB415C9D1F0D2 (simupoll_id), 
                UNIQUE INDEX category_unique_name_and_simupoll (simupoll_id, user_id, name), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE cpasimusante__simupoll_statmanage (
                id INT AUTO_INCREMENT NOT NULL, 
                user_id INT DEFAULT NULL, 
                simupoll_id INT DEFAULT NULL, 
                userlist VARCHAR(255) DEFAULT NULL, 
                categorylist VARCHAR(255) DEFAULT NULL, 
                completecategorylist VARCHAR(255) DEFAULT NULL, 
                INDEX IDX_8F5E4368A76ED395 (user_id), 
                INDEX IDX_8F5E4368C9D1F0D2 (simupoll_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_organization 
            ADD CONSTRAINT FK_2F384248C9D1F0D2 FOREIGN KEY (simupoll_id) 
            REFERENCES cpasimusante__simupoll (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll 
            ADD CONSTRAINT FK_BC9663DBB87FAB32 FOREIGN KEY (resourceNode_id) 
            REFERENCES claro_resource_node (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_proposition 
            ADD CONSTRAINT FK_1F8DDB511E27F6BF FOREIGN KEY (question_id) 
            REFERENCES cpasimusante__simupoll_question (id)
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_paper 
            ADD CONSTRAINT FK_3626D4F7A76ED395 FOREIGN KEY (user_id) 
            REFERENCES claro_user (id)
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_paper 
            ADD CONSTRAINT FK_3626D4F7C9D1F0D2 FOREIGN KEY (simupoll_id) 
            REFERENCES cpasimusante__simupoll (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_paper 
            ADD CONSTRAINT FK_3626D4F7EC8B7ADE FOREIGN KEY (period_id) 
            REFERENCES cpasimusante__simupoll_period (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_answer 
            ADD CONSTRAINT FK_DA8A47FE6758861 FOREIGN KEY (paper_id) 
            REFERENCES cpasimusante__simupoll_paper (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_answer 
            ADD CONSTRAINT FK_DA8A47F1E27F6BF FOREIGN KEY (question_id) 
            REFERENCES cpasimusante__simupoll_question (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_question 
            ADD CONSTRAINT FK_B111E49AC9D1F0D2 FOREIGN KEY (simupoll_id) 
            REFERENCES cpasimusante__simupoll (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_question 
            ADD CONSTRAINT FK_B111E49A12469DE2 FOREIGN KEY (category_id) 
            REFERENCES cpasimusante__simupoll_category (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_period 
            ADD CONSTRAINT FK_12CDF094C9D1F0D2 FOREIGN KEY (simupoll_id) 
            REFERENCES cpasimusante__simupoll (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_category 
            ADD CONSTRAINT FK_1AAB415727ACA70 FOREIGN KEY (parent_id) 
            REFERENCES cpasimusante__simupoll_category (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_category 
            ADD CONSTRAINT FK_1AAB415A76ED395 FOREIGN KEY (user_id) 
            REFERENCES claro_user (id)
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_category 
            ADD CONSTRAINT FK_1AAB415C9D1F0D2 FOREIGN KEY (simupoll_id) 
            REFERENCES cpasimusante__simupoll (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statmanage 
            ADD CONSTRAINT FK_8F5E4368A76ED395 FOREIGN KEY (user_id) 
            REFERENCES claro_user (id)
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statmanage 
            ADD CONSTRAINT FK_8F5E4368C9D1F0D2 FOREIGN KEY (simupoll_id) 
            REFERENCES cpasimusante__simupoll (id) 
            ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_organization 
            DROP FOREIGN KEY FK_2F384248C9D1F0D2
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_paper 
            DROP FOREIGN KEY FK_3626D4F7C9D1F0D2
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_question 
            DROP FOREIGN KEY FK_B111E49AC9D1F0D2
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_period 
            DROP FOREIGN KEY FK_12CDF094C9D1F0D2
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_category 
            DROP FOREIGN KEY FK_1AAB415C9D1F0D2
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_statmanage 
            DROP FOREIGN KEY FK_8F5E4368C9D1F0D2
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_answer 
            DROP FOREIGN KEY FK_DA8A47FE6758861
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_proposition 
            DROP FOREIGN KEY FK_1F8DDB511E27F6BF
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_answer 
            DROP FOREIGN KEY FK_DA8A47F1E27F6BF
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_paper 
            DROP FOREIGN KEY FK_3626D4F7EC8B7ADE
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_question 
            DROP FOREIGN KEY FK_B111E49A12469DE2
        ');
        $this->addSql('
            ALTER TABLE cpasimusante__simupoll_category 
            DROP FOREIGN KEY FK_1AAB415727ACA70
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_organization
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_proposition
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_paper
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_answer
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_question
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_period
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_category
        ');
        $this->addSql('
            DROP TABLE cpasimusante__simupoll_statmanage
        ');
    }
}
