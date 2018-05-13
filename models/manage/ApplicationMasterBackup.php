<?php

namespace app\models\manage;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "application_master_backup".
 */
class ApplicationMasterBackup extends ApplicationMaster
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
        return 'application_master_backup';
    }
}
