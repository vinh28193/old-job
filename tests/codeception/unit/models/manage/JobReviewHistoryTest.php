<?php
namespace models\manage;

use app\models\manage\JobReviewHistory;
use app\models\manage\JobReviewStatus;
use tests\codeception\unit\JmTestCase;

/**
 * 審査履歴テスト
 */
class JobReviewHistoryTest extends JmTestCase
{

    /**
     * テーブル名テスト
     */
    public function testTableName()
    {
        $model = new JobReviewHistory();
        verify($model->tableName())->equals('job_review_history');
    }

    /**
     * ラベル名テスト
     */
    public function testAttributeLabels()
    {
        $model = new JobReviewHistory();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new JobReviewHistory();
            $model->load([
                $model->formName() => [
                    'job_master_id' => null,
                    'admin_master_id' => null,
                    'job_review_status_id' => null,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('admin_master_id'))->true();
            verify($model->hasErrors('job_review_status_id'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new JobReviewHistory();
            $model->load([
                $model->formName() => [
                    'id' => '文字列',
                    'job_master_id' => '文字列',
                    'admin_master_id' => '文字列',
                    'job_review_status_id' => '文字列',
                    'created_at' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('id'))->true();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('admin_master_id'))->true();
            verify($model->hasErrors('job_review_status_id'))->true();
            verify($model->hasErrors('created_at'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new JobReviewHistory();
            $model->load([
                $model->formName() => [
                    'comment' => str_repeat('a', $model::COMMENT_MAX + 1),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('comment'))->true();
        });
        $this->specify('正しいチェック', function () {
            $model = new JobReviewHistory();
            $model->load([
                $model->formName() => [
                    'id' => 10000,
                    'tenant_id' => 2,
                    'job_master_id' => 3,
                    'admin_master_id' => 4,
                    'job_review_status_id' => 6,
                    'created_at' => 12345678,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('id'))->false();
            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('job_master_id'))->false();
            verify($model->hasErrors('admin_master_id'))->false();
            verify($model->hasErrors('job_review_status_id'))->false();
            verify($model->hasErrors('created_at'))->false();
        });
    }

    /**
     * 審査履歴表示用データプロバイダー取得テスト
     */
    public function testDataProvier()
    {
        /** @var yii\data\ActiveDataProvider $dataProvier */
        $dataProvier = JobReviewHistory::dataProvier(1);
        verify($dataProvier)->notEmpty();
        verify($dataProvier->pagination)->false();
        verify($dataProvier->sort)->false();
    }

    /**
     * 審査履歴として表示する項目設定（GridHelper用）取得テスト
     */
    public function testListItems()
    {
        $listItems = JobReviewHistory::listItems(1);
        $attributes = [
            'created_at' => 'created_at',
            'job_review_status_id' => function ($model) {
                /** @var JobMaster $model */
                return JobReviewStatus::name($model->job_review_status_id);
            },
            'admin_master_id' => 'adminMaster.fullName',
            'comment' => 'comment',
        ];
        foreach ($listItems as $item) {
            verify($item['type'])->equals('');
            verify($item['value'])->equals($attributes[$item['attribute']]);

            switch ($item['attribute']) {
                case 'created_at':
                    verify($item['format'])->equals('datetime');
                    break;
                case 'comment':
                    verify($item['headerOptions'])->equals(['class' => 'w45']);
                    verify($item['format'])->equals('ntext');
                    break;
            }
        }
    }
}