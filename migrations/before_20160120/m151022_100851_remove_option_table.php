<?php

use yii\db\Schema;
use yii\db\Migration;

class m151022_100851_remove_option_table extends Migration
{
    public function up()
    {
        // 代理店テーブル
        $this->dropTable('corp_option');
        $this->addColumn('corp_master', 'option100', Schema::TYPE_TEXT . ' COMMENT "オプション100"');
        $this->addColumn('corp_master', 'option101', Schema::TYPE_TEXT . ' COMMENT "オプション101"');
        $this->addColumn('corp_master', 'option102', Schema::TYPE_TEXT . ' COMMENT "オプション102"');
        $this->addColumn('corp_master', 'option103', Schema::TYPE_TEXT . ' COMMENT "オプション103"');
        $this->addColumn('corp_master', 'option104', Schema::TYPE_TEXT . ' COMMENT "オプション104"');
        $this->addColumn('corp_master', 'option105', Schema::TYPE_TEXT . ' COMMENT "オプション105"');
        $this->addColumn('corp_master', 'option106', Schema::TYPE_TEXT . ' COMMENT "オプション106"');
        $this->addColumn('corp_master', 'option107', Schema::TYPE_TEXT . ' COMMENT "オプション107"');
        $this->addColumn('corp_master', 'option108', Schema::TYPE_TEXT . ' COMMENT "オプション108"');
        $this->addColumn('corp_master', 'option109', Schema::TYPE_TEXT . ' COMMENT "オプション109"');

        // 管理者テーブル
        $this->dropTable('admin_option');
        $this->addColumn('admin_master', 'option100', Schema::TYPE_TEXT . ' COMMENT "オプション100"');
        $this->addColumn('admin_master', 'option101', Schema::TYPE_TEXT . ' COMMENT "オプション101"');
        $this->addColumn('admin_master', 'option102', Schema::TYPE_TEXT . ' COMMENT "オプション102"');
        $this->addColumn('admin_master', 'option103', Schema::TYPE_TEXT . ' COMMENT "オプション103"');
        $this->addColumn('admin_master', 'option104', Schema::TYPE_TEXT . ' COMMENT "オプション104"');
        $this->addColumn('admin_master', 'option105', Schema::TYPE_TEXT . ' COMMENT "オプション105"');
        $this->addColumn('admin_master', 'option106', Schema::TYPE_TEXT . ' COMMENT "オプション106"');
        $this->addColumn('admin_master', 'option107', Schema::TYPE_TEXT . ' COMMENT "オプション107"');
        $this->addColumn('admin_master', 'option108', Schema::TYPE_TEXT . ' COMMENT "オプション108"');
        $this->addColumn('admin_master', 'option109', Schema::TYPE_TEXT . ' COMMENT "オプション109"');

        // 掲載企業テーブル
        $this->dropTable('client_option');
        $this->addColumn('client_master', 'option100', Schema::TYPE_TEXT . ' COMMENT "オプション100"');
        $this->addColumn('client_master', 'option101', Schema::TYPE_TEXT . ' COMMENT "オプション101"');
        $this->addColumn('client_master', 'option102', Schema::TYPE_TEXT . ' COMMENT "オプション102"');
        $this->addColumn('client_master', 'option103', Schema::TYPE_TEXT . ' COMMENT "オプション103"');
        $this->addColumn('client_master', 'option104', Schema::TYPE_TEXT . ' COMMENT "オプション104"');
        $this->addColumn('client_master', 'option105', Schema::TYPE_TEXT . ' COMMENT "オプション105"');
        $this->addColumn('client_master', 'option106', Schema::TYPE_TEXT . ' COMMENT "オプション106"');
        $this->addColumn('client_master', 'option107', Schema::TYPE_TEXT . ' COMMENT "オプション107"');
        $this->addColumn('client_master', 'option108', Schema::TYPE_TEXT . ' COMMENT "オプション108"');
        $this->addColumn('client_master', 'option109', Schema::TYPE_TEXT . ' COMMENT "オプション109"');
    }

    public function down()
    {
        // 代理店テーブル
        $this->dropColumn('corp_master', 'option100');
        $this->dropColumn('corp_master', 'option101');
        $this->dropColumn('corp_master', 'option102');
        $this->dropColumn('corp_master', 'option103');
        $this->dropColumn('corp_master', 'option104');
        $this->dropColumn('corp_master', 'option105');
        $this->dropColumn('corp_master', 'option106');
        $this->dropColumn('corp_master', 'option107');
        $this->dropColumn('corp_master', 'option108');
        $this->dropColumn('corp_master', 'option109');

        $sql = <<<SQL
CREATE TABLE `corp_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `corp_master_id` int(11) NOT NULL COMMENT '代理店ID',
  `function_item_id` int(11) DEFAULT NULL COMMENT '項目管理ID',
  `option_value` text COMMENT 'オプション項目内容',
  PRIMARY KEY (`id`),
  KEY `idx_corp_option_corp_id` (`corp_master_id`),
  KEY `idx_corp_option_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='代理店オプション';

SQL;
        $this->execute($sql);

        // 管理者テーブル
        $this->dropColumn('admin_master', 'option100');
        $this->dropColumn('admin_master', 'option101');
        $this->dropColumn('admin_master', 'option102');
        $this->dropColumn('admin_master', 'option103');
        $this->dropColumn('admin_master', 'option104');
        $this->dropColumn('admin_master', 'option105');
        $this->dropColumn('admin_master', 'option106');
        $this->dropColumn('admin_master', 'option107');
        $this->dropColumn('admin_master', 'option108');
        $this->dropColumn('admin_master', 'option109');

        $sql = <<<SQL
CREATE TABLE `admin_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `admin_master_id` int(11) NOT NULL COMMENT '管理者ID',
  `function_item_id` int(11) DEFAULT NULL COMMENT '項目管理ID',
  `option_value` text COMMENT 'オプション項目内容',
  PRIMARY KEY (`id`),
  KEY `idx_admin_option_admin_id` (`admin_master_id`),
  KEY `idx_admin_option_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='管理者オプション';
SQL;
        $this->execute($sql);

        // 掲載企業テーブル
        $this->dropColumn('client_master', 'option100');
        $this->dropColumn('client_master', 'option101');
        $this->dropColumn('client_master', 'option102');
        $this->dropColumn('client_master', 'option103');
        $this->dropColumn('client_master', 'option104');
        $this->dropColumn('client_master', 'option105');
        $this->dropColumn('client_master', 'option106');
        $this->dropColumn('client_master', 'option107');
        $this->dropColumn('client_master', 'option108');
        $this->dropColumn('client_master', 'option109');

        $sql = <<<SQL
CREATE TABLE `client_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tenant_id` int(11) NOT NULL COMMENT 'テナントID',
  `client_master_id` int(11) NOT NULL COMMENT '掲載企業ID',
  `function_item_id` int(11) DEFAULT NULL COMMENT '項目管理ID',
  `option_value` text COMMENT 'オプション項目内容',
  PRIMARY KEY (`id`),
  KEY `idx_client_option_client_id` (`client_master_id`),
  KEY `idx_client_option_tenant_id` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='掲載企業オプション';
SQL;
        $this->execute($sql);
    }
}
