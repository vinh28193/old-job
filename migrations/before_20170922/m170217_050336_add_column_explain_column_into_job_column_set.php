<?php

use app\models\manage\JobColumnSet;
use yii\db\Migration;
use yii\helpers\ArrayHelper;
use yii\db\Schema;

/**
 * Class m170217_050336_add_column_explain_column_into_job_column_set
 * job_column_setテーブルに、column_explainカラムを追加
 * 求人原稿項目設定画面の項目説明文用カラム
 */
class m170217_050336_add_column_explain_column_into_job_column_set extends Migration
{
    public function safeUp()
    {
        if (ArrayHelper::isIn('column_explain', JobColumnSet::getTableSchema()->columnNames)) {
            $this->safeDown();
        }
        $this->addColumn('job_column_set', 'column_explain', $this->string(1000));
        $this->addCommentOnColumn('job_column_set', 'column_explain', '項目説明文');
    }

    public function safeDown()
    {
        $this->dropColumn('job_column_set', 'column_explain');
    }
}
