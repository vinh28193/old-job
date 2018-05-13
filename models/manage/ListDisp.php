<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "list_disp".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $column_name
 * @property integer $sort_no
 * @property integer $disp_type_id
 */
class ListDisp extends BaseModel
{
    protected $_listItems;
    protected $_notListItems;

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'list_disp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'column_name', 'sort_no', 'disp_type_id'], 'required'],
            [['tenant_id', 'sort_no', 'disp_type_id'], 'integer'],
            [['column_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'column_name' => Yii::t('app', 'job_masterのカラム'),
            'sort_no' => Yii::t('app', '表示順'),
            'disp_type_id' => Yii::t('app', '掲載タイプ'),
        ];
    }

    /**
     * dispTypeIdからListDisplayに表示する項目の情報を取得する
     * @param $dispTypeId
     * @return JobColumnSet[]
     */
    public static function items($dispTypeId)
    {
        $items = self::find()->with('jobColumnSet')->where([
            'disp_type_id' => $dispTypeId,
        ])->all();
        $items = array_filter($items, function (self $self) {
            return $self->jobColumnSet->valid_chk ?? false;
        });

//        $items = self::find()->joinWith('jobColumnSet')->select([
//            self::tableName() . '.column_name',
//            JobColumnSet::tableName() . '.is_must',
//        ])->where([
//            self::tableName() . '.disp_type_id' => $dispTypeId,
//            JobColumnSet::tableName() . '.valid_chk' => 1,
//        ])->all();
        ArrayHelper::multisort($items, 'sort_no');

        return ArrayHelper::getColumn($items, 'jobColumnSet');
    }

    /**
     * JobColumnSetとのrelation
     * @return \yii\db\ActiveQuery
     */
    public function getJobColumnSet()
    {
        return $this->hasOne(JobColumnSet::className(), ['column_name' => 'column_name']);
    }

    /**
     * DispTypeとのrelation
     * @return \yii\db\ActiveQuery
     */
    public function getDispType()
    {
        return $this->hasOne(DispType::className(), ['id' => 'disp_type_id']);
    }

    /**
     * ListDisplayに表示する項目のEditableを出力する
     * @param JobMaster $model
     * @param $dispTypeId
     * @return array
     */
    public static function editableDetailAttributes(JobMaster $model, $dispTypeId)
    {
        return ArrayHelper::getColumn(self::items($dispTypeId), function (JobColumnSet $item) use ($model) {
            switch ($item->column_name) {
                case 'job_no':
                    return $model->job_no ? 'job_no' : [
                        'label' => $item->label,
                        'value' => Yii::t('app', '※仕事IDは自動で採番されます'),
                    ];
                    break;
                default:
                    return [
                        'label' => $item->label,
                        'value' => $item->getEditable($model),
                        'format' => 'raw',
                    ];
                    break;
            }
        });
    }

    /**
     * 入力の無い項目を排除する
     * @param JobColumnSet[] $items
     * @param JobMaster $jobMaster
     * @return array
     */
    public static function removeEmptyJobAttributes($items, JobMaster $jobMaster)
    {
        return array_filter($items, function ($item) use ($jobMaster) {
            /** @var JobColumnSet $item */
            return !JmUtils::isEmpty($jobMaster->{$item->column_name});
        });
    }

    /**
     * JobMasterインスタンスを元にListDisplayに表示する項目を
     * 'attribute:format'の形式で出力する
     * @param JobMaster $jobMaster
     * @return array
     */
    public static function getJobAttributesWithFormat(JobMaster $jobMaster)
    {
        $items = self::removeEmptyJobAttributes(self::items($jobMaster->clientChargePlan->disp_type_id), $jobMaster);
        return ArrayHelper::getColumn($items, 'columnNameWithFormat');
    }

    /**
     * 有効な項目を、list表示するものとしないものに分けて取得する
     * list表示するものに関しては並び替えも行う
     * @param $dispTypeId
     * @return array
     */
    public static function bothItems($dispTypeId)
    {
        $bothItems = [];
        // dispTypeIdをセット
        JobColumnSet::setDispTypeId($dispTypeId);

        // インスタンス生成
        /** @var JobColumnSet[] $items */
        $items = JobColumnSet::find()->with('listDisp')->where(['valid_chk' => JobColumnSet::VALID])->andWhere([
            'NOT IN',
            'job_column_set.column_name',
            JobColumnSet::NOT_AVAILABLE_LIST_DISP_ITEMS,
        ])->all();

        // 振り分け
        foreach ($items as $item) {
            if ($item->listDisp) {
                $bothItems['listItems'][] = $item;
            } else {
                $bothItems['notListItems'][] = $item;
            }
        }
        // 並び替え
        ArrayHelper::multisort($bothItems['listItems'], 'listDisp.sort_no');

        return $bothItems;
    }
}
