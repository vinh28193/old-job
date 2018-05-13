<?php

namespace app\models;

use proseeds\models\BaseModel;
use yii;
use app\models\manage\JobMaster;
use yii\helpers\ArrayHelper;
use app\models\queries\JobDispQuery;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\ClientChargePlan;
use app\models\manage\DispType;
use app\models\manage\JobReviewStatus;

/**
 * This is the model class for table "job_access_recommend".
 *
 * @property integer $id
 * @property integer $job_master_id
 * @property integer $tenant_id
 * @property integer $accessed_job_master_id_1
 * @property integer $accessed_job_master_id_2
 * @property integer $accessed_job_master_id_3
 * @property integer $accessed_job_master_id_4
 * @property integer $accessed_job_master_id_5
 * @property JobMaster $JobMaster
 * @property array $AccessJobMasters
 * @property jobMasterDisp $accessJobMasterDisp1
 * @property jobMasterDisp $accessJobMasterDisp2
 * @property jobMasterDisp $accessJobMasterDisp3
 * @property jobMasterDisp $accessJobMasterDisp4
 * @property jobMasterDisp $accessJobMasterDisp5
 */
class JobAccessRecommend extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'job_access_recommend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['job_master_id'], 'required'],
            [[
                'job_master_id',
                'tenant_id',
                'accessed_job_master_id_1',
                'accessed_job_master_id_2',
                'accessed_job_master_id_3',
                'accessed_job_master_id_4',
                'accessed_job_master_id_5'
            ], 'integer'],
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (!$this->tenant_id) {
                $this->tenant_id = Yii::$app->tenant->id;
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'job_master_id' => Yii::t('app', '仕事ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'accessed_job_master_id_1' => Yii::t('app', '閲覧仕事ID1'),
            'accessed_job_master_id_2' => Yii::t('app', '閲覧仕事ID2'),
            'accessed_job_master_id_3' => Yii::t('app', '閲覧仕事ID3'),
            'accessed_job_master_id_4' => Yii::t('app', '閲覧仕事ID4'),
            'accessed_job_master_id_5' => Yii::t('app', '閲覧仕事ID5'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobMaster()
    {
        return $this->hasOne(JobMaster::className(), ['id' => 'job_master_id']);
    }


    /**
     * @return array
     */
    public function getAccessJobMasters()
    {
        return [
            $this->accessJobMasterDisp1,
            $this->accessJobMasterDisp2,
            $this->accessJobMasterDisp3,
            $this->accessJobMasterDisp4,
            $this->accessJobMasterDisp5,
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessJobMasterDisp1()
    {
        // TODO:必ず有効であるかのクエリは、
        return $this->hasOne(JobMasterDisp::className(), ['id' => 'accessed_job_master_id_1'])->andWhere([
            JobMasterDisp::tableName() . '.valid_chk'    => JobMasterDisp::FLAG_VALID,
            JobMasterDisp::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
            ClientMaster::tableName() . '.valid_chk'     => JobMasterDisp::FLAG_VALID,
            CorpMaster::tableName() . '.valid_chk'       => JobMasterDisp::FLAG_VALID,
            ClientChargePlan::tableName() . '.valid_chk' => JobMasterDisp::FLAG_VALID,
            DispType::tableName() . '.valid_chk'         => JobMasterDisp::FLAG_VALID,
        ])->andWhere([
            'or',
            ['<=', JobMasterDisp::tableName() . '.disp_start_date', time()],
            [JobMasterDisp::tableName() . '.disp_start_date' => null],
        ])->andWhere([
            'or',
            ['>=', JobMasterDisp::tableName() . '.disp_end_date', strtotime('today')],
            [JobMasterDisp::tableName() . '.disp_end_date' => null],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessJobMasterDisp2()
    {
        return $this->hasOne(JobMasterDisp::className(), ['id' => 'accessed_job_master_id_2'])->andWhere([
            JobMasterDisp::tableName() . '.valid_chk'    => JobMasterDisp::FLAG_VALID,
            JobMasterDisp::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
            ClientMaster::tableName() . '.valid_chk'     => JobMasterDisp::FLAG_VALID,
            CorpMaster::tableName() . '.valid_chk'       => JobMasterDisp::FLAG_VALID,
            ClientChargePlan::tableName() . '.valid_chk' => JobMasterDisp::FLAG_VALID,
            DispType::tableName() . '.valid_chk'         => JobMasterDisp::FLAG_VALID,
        ])->andWhere([
            'or',
            ['<=', JobMasterDisp::tableName() . '.disp_start_date', time()],
            [JobMasterDisp::tableName() . '.disp_start_date' => null],
        ])->andWhere([
            'or',
            ['>=', JobMasterDisp::tableName() . '.disp_end_date', strtotime('today')],
            [JobMasterDisp::tableName() . '.disp_end_date' => null],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessJobMasterDisp3()
    {
        return $this->hasOne(JobMasterDisp::className(), ['id' => 'accessed_job_master_id_3'])->andWhere([
            JobMasterDisp::tableName() . '.valid_chk'    => JobMasterDisp::FLAG_VALID,
            JobMasterDisp::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
            ClientMaster::tableName() . '.valid_chk'     => JobMasterDisp::FLAG_VALID,
            CorpMaster::tableName() . '.valid_chk'       => JobMasterDisp::FLAG_VALID,
            ClientChargePlan::tableName() . '.valid_chk' => JobMasterDisp::FLAG_VALID,
            DispType::tableName() . '.valid_chk'         => JobMasterDisp::FLAG_VALID,
        ])->andWhere([
            'or',
            ['<=', JobMasterDisp::tableName() . '.disp_start_date', time()],
            [JobMasterDisp::tableName() . '.disp_start_date' => null],
        ])->andWhere([
            'or',
            ['>=', JobMasterDisp::tableName() . '.disp_end_date', strtotime('today')],
            [JobMasterDisp::tableName() . '.disp_end_date' => null],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessJobMasterDisp4()
    {
        return $this->hasOne(JobMasterDisp::className(), ['id' => 'accessed_job_master_id_4'])->andWhere([
            JobMasterDisp::tableName() . '.valid_chk'    => JobMasterDisp::FLAG_VALID,
            JobMasterDisp::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
            ClientMaster::tableName() . '.valid_chk'     => JobMasterDisp::FLAG_VALID,
            CorpMaster::tableName() . '.valid_chk'       => JobMasterDisp::FLAG_VALID,
            ClientChargePlan::tableName() . '.valid_chk' => JobMasterDisp::FLAG_VALID,
            DispType::tableName() . '.valid_chk'         => JobMasterDisp::FLAG_VALID,
        ])->andWhere([
            'or',
            ['<=', JobMasterDisp::tableName() . '.disp_start_date', time()],
            [JobMasterDisp::tableName() . '.disp_start_date' => null],
        ])->andWhere([
            'or',
            ['>=', JobMasterDisp::tableName() . '.disp_end_date', strtotime('today')],
            [JobMasterDisp::tableName() . '.disp_end_date' => null],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessJobMasterDisp5()
    {
        return $this->hasOne(JobMasterDisp::className(), ['id' => 'accessed_job_master_id_5'])->andWhere([
            JobMasterDisp::tableName() . '.valid_chk'    => JobMasterDisp::FLAG_VALID,
            JobMasterDisp::tableName() . '.job_review_status_id' => JobReviewStatus::STEP_REVIEW_OK,
            ClientMaster::tableName() . '.valid_chk'     => JobMasterDisp::FLAG_VALID,
            CorpMaster::tableName() . '.valid_chk'       => JobMasterDisp::FLAG_VALID,
            ClientChargePlan::tableName() . '.valid_chk' => JobMasterDisp::FLAG_VALID,
            DispType::tableName() . '.valid_chk'         => JobMasterDisp::FLAG_VALID,
        ])->andWhere([
            'or',
            ['<=', JobMasterDisp::tableName() . '.disp_start_date', time()],
            [JobMasterDisp::tableName() . '.disp_start_date' => null],
        ])->andWhere([
            'or',
            ['>=', JobMasterDisp::tableName() . '.disp_end_date', strtotime('today')],
            [JobMasterDisp::tableName() . '.disp_end_date' => null],
        ]);
    }

    /**
     * モデル内に含まれている仕事ID、閲覧仕事IDのリストを返す（リストを更新する前の判別用）
     * @return array
     */
    public function getJobMasterIdList()
    {
        return [
            'job_master_id' => $this->job_master_id,
            'accessed_job_master_id_1' => $this->accessed_job_master_id_1,
            'accessed_job_master_id_2' => $this->accessed_job_master_id_2,
            'accessed_job_master_id_3' => $this->accessed_job_master_id_3,
            'accessed_job_master_id_4' => $this->accessed_job_master_id_4,
            'accessed_job_master_id_5' => $this->accessed_job_master_id_5,
        ];
    }

    /**
     * 閲覧仕事IDの更新処理
     * @param $jobMasterId
     * @param $sessionJobMasterId
     */
    public function updateAccessedJobMasterIds($jobMasterId, $sessionJobMasterId)
    {
        // 表示しようとしている原稿及び、すでに閲覧履歴として登録してある
        // 原稿に関しては表示しないように、フィルターしている
        if (isset($sessionJobMasterId) &&
            $jobMasterId != $sessionJobMasterId &&
            !ArrayHelper::isIn($sessionJobMasterId, $this->getJobMasterIdList())
        ) {
            $this->load([
                'job_master_id' => $jobMasterId,
                'accessed_job_master_id_1' => $sessionJobMasterId,
                'accessed_job_master_id_2' => $this->accessed_job_master_id_1,
                'accessed_job_master_id_3' => $this->accessed_job_master_id_2,
                'accessed_job_master_id_4' => $this->accessed_job_master_id_3,
                'accessed_job_master_id_5' => $this->accessed_job_master_id_4,
            ], '');
            if ($this->validate()) {
                $this->save();
            }
        }
    }
}
