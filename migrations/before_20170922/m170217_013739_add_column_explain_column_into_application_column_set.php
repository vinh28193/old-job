<?php

use app\models\manage\ApplicationColumnSet;
use yii\db\Migration;
use yii\helpers\ArrayHelper;
use yii\db\Schema;

/**
 * Class m170217_013738_add_column_explain_column_into_application_column_set
 * application_column_setテーブルに、column_explainカラムを追加
 * 応募者項目設定画面の項目説明文用カラム
 */
class m170217_013739_add_column_explain_column_into_application_column_set extends Migration
{
    public function safeUp()
    {
        if (ArrayHelper::isIn('column_explain', ApplicationColumnSet::getTableSchema()->columnNames)) {
            $this->safeDown();
        }
        $this->addColumn('application_column_set', 'column_explain', $this->string(180));
        $this->addCommentOnColumn('application_column_set', 'column_explain', '項目説明文');
    }

    public function safeDown()
    {
        $this->dropColumn('application_column_set', 'column_explain');
    }
}
