<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/12
 * Time: 21:24
 */

namespace app\models\manage\searchkey;


use app\common\Helper\JmUtils;
use proseeds\models\BaseModel;
use yii;

/**
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property int[] $itemIds
 * @property string $itemForeignKey
 */
class BaseSearchKeyJunction extends BaseModel
{
    public $itemIds;
    public $itemForeignKey;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['itemIds', 'safe'],
            ['tenant_id', 'number'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ['itemIds' => Yii::$app->searchKey->label($this->tableName())];
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->itemIds && $this->tenant_id && $this->job_master_id) {
            $values = [];
            foreach ($this->itemIds as $itemId) {
                $values[] = [$this->tenant_id, $this->job_master_id, $itemId];
            }
            if (!JmUtils::isEmpty($values)) {
                return Yii::$app->db->createCommand()->batchInsert(self::tableName(),
                    ['tenant_id', 'job_master_id', $this->itemForeignKey], $values)->execute();
            }
        }
        return parent::save($runValidation, $attributeNames);
    }
}