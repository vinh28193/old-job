<?php

namespace app\common\traits;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/12/25
 * Time: 9:29
 *
 * ActiveRecordを継承したクラスでuseすること
 */
trait FileDeleteTrait
{
    /**
     * レコードを削除する
     * @param ActiveRecord[] $models
     * @return integer
     */
    public static function deleteRecords($models): int
    {
        $ids = ArrayHelper::getColumn($models, 'id');
        /** @noinspection PhpUndefinedMethodInspection */
        return static::deleteAll(['id' => $ids]);
    }

    /**
     * ファイル実体を削除する
     * @param UploadTrait[] $models
     */
    public static function deleteFiles($models)
    {
        foreach ($models as $model) {
            $model->deleteFile();
        }
    }
}
