<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * 審査履歴モデル
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property integer $admin_master_id
 * @property integer $job_review_status_id
 * @property string $comment
 * @property integer $created_at
 * hasOne relation
 * @property JobMaster $jobMaster
 * @property AdminMaster $adminMaster
 * @property JobReviewStatus $jobReviewStatus
 */
class JobReviewHistory extends BaseModel
{
    /** コメントの最大文字数 */
    const COMMENT_MAX = 500;

    /** 審査履歴の表示最大数 */
    const HISTORY_MAX = 20;

    /** @var JobReviewStatus 審査ステータス用 */
    private $_jobReviewStatus = null;

    /**
     * テーブル名
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_review_history';
    }

    /**
     * ルール
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['job_master_id', 'admin_master_id', 'job_review_status_id'], 'required'],
            [['id', 'job_master_id', 'admin_master_id', 'job_review_status_id', 'created_at'], 'integer'],
            ['comment', 'string', 'max' => self::COMMENT_MAX],
        ];
    }

    /**
     * 要素のラベル名を設定
     * 審査画面でしか使用していないため、審査画面で使用するラベル名に設定している。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '審査履歴ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'job_master_id' => Yii::t('app', '求人ID'),
            'admin_master_id' => Yii::t('app', '担当'),
            'job_review_status_id' => JobReviewStatus::attributeLabel(),
            'comment' => Yii::t('app', 'コメント'),
            'created_at' => Yii::t('app', '更新日時'),
        ];
    }

    /**
     * 求人リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getJobMaster()
    {
        return $this->hasOne(JobMaster::className(), ['id' => 'job_master_id']);
    }

    /**
     * 管理者リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getAdminMaster()
    {
        return $this->hasOne(AdminMaster::className(), ['id' => 'admin_master_id']);
    }

    /**
     * 審査ステータスモデルを返す
     * @return JobReviewStatus
     */
    public function getJobReviewStatus()
    {
        if ($this->_jobReviewStatus === null) {
            $this->_jobReviewStatus = new JobReviewStatus();
        }
        $this->_jobReviewStatus->id = $this->job_review_status_id;
        return $this->_jobReviewStatus;
    }

    /**
     * 審査履歴として表示するデータプロバイダーを返す
     * @param integer $id
     * @return ActiveDataProvider
     */
    public static function dataProvier($id)
    {
        $query = self::find()->where(['job_master_id' => $id])->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])->limit(self::HISTORY_MAX);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        return $dataProvider;
    }

    /**
     * 審査履歴として表示する項目設定（GridHelper用）を返す
     * @return array
     */
    public static function listItems()
    {
        // 審査履歴 - 表示項目リスト
        $listItems = [];
        foreach (static::listAttributes() as $labelAttribute => $valueAttribute) {
            $item = [
                'type' => '',
                'attribute' => $labelAttribute,
                'value' => $valueAttribute,
            ];
            if ($labelAttribute === 'created_at') {
                $item['format'] = 'datetime';
            } elseif ($labelAttribute === 'comment') {
                $item['headerOptions'] = ['class' => 'w45'];
                $item['format'] = 'ntext';
            }

            $listItems[] = $item;
        }
        return $listItems;
    }

    /**
     * 審査履歴として表示する属性を返す
     * @return array
     */
    private static function listAttributes()
    {
        return [
            'created_at' => 'created_at',
            'job_review_status_id' => function ($model) {
                /** @var JobReviewHistory $model */
                return $model->jobReviewStatus->name;
            },
            'admin_master_id' => 'adminMaster.fullName',
            'comment' => 'comment',
        ];
    }
}
