<?php

use yii\db\Migration;

class m180208_002806_add_column_job_review_tables extends Migration
{
    public function safeUp()
    {
        // 代理店マスタに「代理店審査フラグ」を追加
        $this->addColumn(
            'corp_master',
            'corp_review_flg',
            $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('代理店審査フラグ')->after('tanto_name')
        );

        // テナントに「審査機能ON/OFFフラグ」を追加
        $this->addColumn(
            'tenant',
            'review_use',
            $this->tinyInteger(1)->unsigned()->notNull()->comment('審査機能ON/OFFフラグ')->after('language_code')
        );

        // 求人マスタに「審査ステータスID」を追加
        $this->addColumn(
            'job_master',
            'job_review_status_id',
            $this->integer()->notNull()->defaultValue(6)->comment('審査ステータスID')->after('review_flg')
        );
        // 既存のカラムを削除
        $this->dropColumn('job_master', 'review_flg');

        // 求人マスタバックアップに「審査ステータスID」を追加
        $this->addColumn(
            'job_master_backup',
            'job_review_status_id',
            $this->integer()->notNull()->defaultValue(6)->comment('審査ステータスID')->after('review_flg')
        );
        // 既存のカラムを削除
        $this->dropColumn('job_master_backup', 'review_flg');
    }

    public function safeDown()
    {
        $this->dropColumn('corp_master', 'corp_review_flg');
        $this->dropColumn('tenant', 'review_use');
        $this->dropColumn('job_master', 'job_review_status_id');
        $this->dropColumn('job_master_backup', 'job_review_status_id');
        $this->addColumn(
            'job_master',
            'review_flg',
            $this->tinyInteger(4)->defaultValue(3)->comment('審査フラグ(0=改変、1=審査依頼中、2=審査NG、3=審査OK)')->after('client_charge_plan_id')
        );
        $this->addColumn(
            'job_master_backup',
            'review_flg',
            $this->tinyInteger(4)->defaultValue(3)->comment('審査フラグ(0=改変、1=審査依頼中、2=審査NG、3=審査OK)')->after('client_charge_plan_id')
        );
    }

    /**
     * Creates a tinyint column.
     * @param $length integer
     */
    public function tinyInteger($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinyint', $length);
    }
}
