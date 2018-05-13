<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/23
 * Time: 17:36
 */

namespace app\controllers;

use app\common\controllers\CommonController;
use app\models\manage\Policy;
use yii\web\NotFoundHttpException;

/**
 * 規約表示コントローラ
 */
class PolicyController extends CommonController
{

    /**
     * todo app\modules\manage\controllers\secure\settings\PolicyControllerとまとめられるとこまとめる
     * @param $policy_no
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($policy_no)
    {
        $model = $this->findModel($policy_no);
        if ($policy_no == Policy::ADMIN_POLICY_NO) {
            // 管理者規約のみviewが違う
            $this->layout = '@app/modules/manage/views/layouts/main';
            return $this->render('manage', [
                'model' => $model,
            ]);
        }
        return $this->render('index', [
            'model' => $model
        ]);

    }

    /**
     * Finds the Policy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $policy_no
     * @return Policy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($policy_no)
    {
        $model = Policy::find()->where(['policy_no' => $policy_no, 'valid_chk' => Policy::VALID])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}