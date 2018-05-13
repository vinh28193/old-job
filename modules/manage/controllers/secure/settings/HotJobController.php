<?php

namespace app\modules\manage\controllers\secure\settings;

use app\common\AccessControl;
use app\models\manage\HotJob;
use app\models\manage\HotJobPriority;
use app\models\manage\JobColumnSet;
use yii\web\NotFoundHttpException;
use app\modules\manage\controllers\CommonController;
use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

/**
 * 注目情報コントローラ
 *
 */
class HotJobController extends CommonController
{
    /**
     * ビヘイビア設定
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'complete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'complete'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 【画面】注目のお仕事の設定編集
     * @return string 描画結果
     */
    public function actionUpdate()
    {
        if (isset($this->post['complete'])) {
            return $this->updateRegister();
        } else {
            $model = HotJob::find()->with('hotJobPriority')->one();
            $model->disp_type_ids = HotJob::getExplodeDispType();
            $jobColumnSet = array_column(JobColumnSet::find()->select(['column_name', 'label'])->all(), 'label',
                'column_name');

            //テキスト1-4の表示項目で不要なものを除外
            $removes = [
                'client_charge_plan_id',
                'job_pict_text_3',
                'job_pict_text_4',
                'job_pict_text_5',
                'media_upload_id_1',
                'media_upload_id_2',
                'media_upload_id_3',
                'media_upload_id_4',
                'media_upload_id_5',
                'mail_body'
            ];
            foreach ($removes as $remove) {
               ArrayHelper::remove($jobColumnSet, $remove);
            }

            $dispTypeName = HotJob::getDispTypeName();

            return $this->render('update', [
                'model' => $model,
                'jobColumnSet' => $jobColumnSet,
                'dispTypeName' => $dispTypeName,
            ]);
        }
    }


    /**
     * 【機能】注目情報設定編集
     * @param $id int (使用していないが、継承元と引数を揃える為)
     * @return string 描画結果
     * @throws NotFoundHttpException
     */
    public function updateRegister($id = null)
    {
        $hotJob = HotJob::find()->one();
        //DBカラムのフォーマットに合わせる
        $this->post['HotJob']['disp_type_ids'] = implode(',', $this->post['HotJob']['disp_type_ids']);
        $hotJob->load($this->post);

        // トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 設定DB保存
            if (!$hotJob->save()) {
                throw new Exception;
            }

            // 子テーブルのhot_job_priorityに優先順を保存
            //disp_priority順にDBから取得
            $priorityItems = ArrayHelper::index(HotJobPriority::find()->all(), 'disp_priority');
            //変更前後の優先順を比較し、変更あれば新たな優先順を保存
            $afters = explode(',', $this->post['hotJobPriority']);
            foreach ($afters as $key => $after) {
                $before = $key + 1;
                if ($before != $after) {
                    $priorityItems[$after]->disp_priority = $before;
                    if (!$priorityItems[$after]->validate() || !$priorityItems[$after]->save()) {
                        throw new Exception;
                    }
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->session->setFlash('updateError', Yii::t('app', '更新に失敗しました。'));
            return $this->redirect('update');
        }
        return $this->redirect('complete');
    }


    /**
     * 【画面】編集完了
     * @return string 描画結果
     */
    public function actionComplete()
    {
        return $this->render('complete');
    }

}
