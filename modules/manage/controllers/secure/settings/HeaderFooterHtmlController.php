<?php

namespace app\modules\manage\controllers\secure\settings;

use app\models\manage\HeaderFooterSetting;
use app\models\manage\SiteHtml;
use app\common\AccessControl;
use yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use app\modules\manage\controllers\CommonController;

/**
 * HeaderFooterHtmlController implements the CRUD actions for HeaderFooter model.
 */
class HeaderFooterHtmlController extends CommonController
{
    /**
     * 運営元権限だけに絞る
     *
     * @return array
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
     * Uploadコンポーネントを参照できるようにする
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate()
    {
        if (isset($this->post['complete'])) {
            return $this->updateRegister();
        } else {
            return $this->render('update', [
                'model' => $this->findHeaderFooter(),
            ]);
        }
    }

    /**
     * @param $id int (使用していないが、継承元と引数を揃える為)
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function updateRegister($id = null)
    {
        $headerFooter = $this->findHeaderFooter();
        $headerFooter->load($this->post);

        // トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // ヘッダーフッター設定DB保存
            if (!$headerFooter->save()) {
                throw new Exception;
            }

            // ヘッダーフッターhtmlDB保存
            $siteHtml = $this->findSiteHtml();
            $siteHtml->fixHtml($headerFooter);
            if (!$siteHtml->save()) {
                throw new Exception;
            }

            // 画像ファイルの保存と旧ファイルの削除
            $headerFooter->saveFiles();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->session->setFlash('updateError', Yii::t('app', '更新に失敗しました。もう一度登録し直して頂くか、サポートセンターまでお問い合わせ下さい。'));
            return $this->render('update', [
                'model' => $this->findHeaderFooter(),
            ]);
        }
        return $this->redirect('complete');
    }

    /**
     * 完了画面
     *
     * @return string
     */
    public function actionComplete()
    {
        return $this->render('complete');
    }

    /**
     * HeaderFooterSettingモデルの取得
     * @return HeaderFooterSetting モデル
     */
    private function findHeaderFooter()
    {
        /** @var HeaderFooterSetting|null $model */
        $model = HeaderFooterSetting::find()->one();
        if (isset($model)) {
            return $model;
        } else {
            return new HeaderFooterSetting;
        }
    }

    /**
     * SiteHtmlモデルの取得
     * @return SiteHtml モデル
     */
    private function findSiteHtml()
    {
        /** @var SiteHtml|null $model */
        $model = SiteHtml::find()->one();
        if (isset($model)) {
            return $model;
        } else {
            return new SiteHtml;
        }
    }
}

