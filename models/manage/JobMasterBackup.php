<?php

namespace app\models\manage;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "job_master_backup".
 *
 * @property integer $deleted_at
 */
class JobMasterBackup extends JobMaster
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['deleted_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_master_backup';
    }

}
