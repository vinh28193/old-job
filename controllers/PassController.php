<?php

namespace app\controllers;

use app\common\controllers\CommonController;
use Exception;
use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use app\models\manage\SendMailSet;
use app\models\PasswordReminder;
use app\models\AdminPasswordSetting;
use app\models\manage\MemberMaster;
use yii\helpers\Html;
use app\common\mail\MailSender;

/**
 * パスワード再設定コントローラ
 * @author Masaki Okada
 */
class PassController extends CommonController
{

    //key_flgに対応する定数。0であれば、管理者
    const IS_KEY_FLG = 0;

    /**
     * エラーチェック後のメッセージを作成する
     * @return string エラーメッセージ
     * @throws NotFoundHttpException
     */
    protected function createMessage($mes = null)
    {
        if($mes != null){
            return Yii::$app->session->setFlash('operationComment',
                str_replace('{message}', $mes, Html::tag('div',
                Html::tag('button', '<span aria-hidden="true">×</span>', ['class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']).
                '{message}',
                ['class' => 'alert alert-warning alert-dismissible']
            )));
        }else{
            return '';
        }
    }

    /**
     * パスワード再設定申請
     * @param string $flg 管理者、求職者を判断するフラグ
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionApply($flg = null)
    {
        $isAdmin = $flg;

        //TODO 現段階では、求職者の会員機能は未実装のため、404とする
        if($isAdmin != 'admin'){
            throw new NotFoundHttpException;
        }

        //flgなしは求職者のパスワード再設定
        $model = $isAdmin == 'admin' ? new AdminPasswordSetting() : new MemberMaster();

        return $this->render('apply', [
            'model' => $model,
            'flg' => $isAdmin,
        ]);
    }

    /**
     * パスワード再設定申請完了
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionApplyComplete()
    {
        $isAdmin = Yii::$app->request->post('flg');

        //TODO 現段階では、求職者の会員機能は未実装のため、404とする
        if($isAdmin != 'admin'){
            throw new NotFoundHttpException;
        }

        //管理者と求職者によって、処理を変更する
        if($isAdmin == 'admin'){
            $mailAddress = Yii::$app->request->post()['AdminPasswordSetting']['mail_address'];
            $model = new AdminPasswordSetting();

        }else{
            $mailAddress = Yii::$app->request->post()['MemberMaster']['mail_address'];
            $model = new MemberMaster();
        }

        $model = $model->find()->where(['mail_address' => $mailAddress])->one();

        //TODO メールアドレスの登録が無かった場合
        if ($model == null) {
            //メッセージ作成
            $this->createMessage(Yii::t('app', '入力されたメールアドレスでの登録がございません。'));

            return $this->redirect(Url::toRoute(['/pass/apply', 'flg' => $isAdmin]));
        }

        //トランザクション開始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //ランダム数値作成
            $key = Yii::$app->security->generateRandomString(30);

            //既存データを全て削除
            $model->unlinkAll('passwordReminder', true);

            //パスワード再設定テーブルに保存
            $passwordReminder = new PasswordReminder();
            $passwordReminder->key_id = $model->id;
            $passwordReminder->collation_key = $key;
            $passwordReminder->key_flg = self::IS_KEY_FLG;
            //リレーション通して保存実行
            $model->link('passwordReminder', $passwordReminder);

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollBack();
            //メッセージ作成
            $this->createMessage(Yii::t('app', 'パスワード再設定申請の処理中にエラーが発生しました。'));

            return $this->redirect(Url::toRoute(['/pass/apply', 'flg' => $isAdmin]));
        }

        //==============================
        // メール送信処理
        //==============================
        /** @var SendMailSet $mailSet */
        $mailSet = SendMailSet::findOne([
            'mail_type' => SendMailSet::MAIL_TYPE_PASS_RESET_MAIL,
            'mail_to' => $isAdmin == 'admin' ? SendMailSet::MAIL_TO_OWNER : SendMailSet::MAIL_TO_APPLICATION
        ]);
        $mailSet->model = $model;

        $mailSender = new MailSender();
        $mailSender->sendAutoMail($mailSet);

        //パスワード再設定申請完了
        return $this->render('apply-complete', [
            'mailAddress' => $mailAddress,
        ]);

    }

    /**
     * パスワード再設定
     * @param string $key ワンタイムパスワード
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEntry($key = null)
    {
        //2時間以内のアクセスかどうか判定
        $passwordReminder = PasswordReminder::find()
        ->andWhere(['collation_key' => $key])
        ->andWhere(['<=', 'created_at', strtotime("+ 2 hour")])
        ->one();

        //keyが存在しない場合は、申請ページへリダイレクト
        if (is_null($key) || $passwordReminder == null) {
            //TODO　会員機能が実装された際に分岐させる必要あり
            $isAdmin = 'admin';

            //メッセージ作成
            $this->createMessage(Yii::t('app', 'URLが無効です。メール送信から2時間以上経過しますと、URLが無効となりますので、ご注意ください。'));

            return  $this->redirect(Url::toRoute(['/pass/apply', 'flg' => $isAdmin]));
        }else{
            $isAdmin = $passwordReminder->key_flg == self::IS_KEY_FLG ? 'admin' : '';
            $model = $isAdmin == 'admin' ? new AdminPasswordSetting() : new MemberMaster();
            return $this->render('entry', [
                'model' => $model,
                'passwordReminder' => $passwordReminder,
            ]);
        }

    }

    /**
     * パスワード再設定完了
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEntryComplete()
    {
        $key = Yii::$app->request->post('key');

        //2時間以内のアクセスかどうか判定
        $passwordReminder = PasswordReminder::find()
            ->andWhere(['collation_key' => $key])
            ->andWhere(['<=', 'created_at', strtotime("+ 2 hour")])
            ->one();

        //keyが存在しない場合は、申請ページへリダイレクト
        if (is_null($key) || $passwordReminder == null) {
            //TODO　会員機能が実装された際に分岐させる必要あり
            $isAdmin = 'admin';

            //メッセージ作成
            $this->createMessage(Yii::t('app', 'URLが無効です。メール送信から2時間以上経過しますと、URLが無効となりますので、ご注意ください。'));

            return  $this->redirect(Url::toRoute(['/pass/apply', 'flg' => $isAdmin]));
        }else{
            $isAdmin = $passwordReminder->key_flg == self::IS_KEY_FLG ? 'admin' : '';
            $model = $isAdmin == 'admin' ? new AdminPasswordSetting() : new MemberMaster();
            $model = $model->find()->where(['id' => $passwordReminder->key_id])->one();

            //パスワード更新処理
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //データを削除
                $model->unlinkAll('passwordReminder', true);

                return $this->render('entry-complete', [
                    'loginId' => $model->login_id,
                    'password' => $model->password,
                    'flg' => $isAdmin,
                ]);
            } else {
                //メッセージ作成
                $this->createMessage(Yii::t('app', 'パスワード再設定の処理中にエラーが発生しました。'));

                return  $this->redirect(Url::toRoute(['/pass/apply', 'flg' => $isAdmin]));
            }
        }
    }

}
