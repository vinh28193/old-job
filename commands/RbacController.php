<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2015/11/24
 * Time: 20:43
 */

namespace app\commands;

use app\rbac\isOwnRule;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        /** 初期設定 */
        /** @var \yii\rbac\DbManager $auth */
        // DbManagerを呼び出す
        $auth = Yii::$app->authManager;

        /**  */
        $this->makeRole($auth);
        $this->makeIsOwnRule($auth);
        $this->makeCorpPermission($auth);
        $this->makeClientPermission($auth);
        $this->makeJobPermission($auth);
        $this->makeAdminPermission($auth);
        $this->makeApplicationPermission($auth);
        $this->makeOptionPermission($auth);
        $this->makeSearchKeyPermission($auth);
        $this->makeMediaUploadPermission($auth);
    }

    /**
     * 10.求人原稿関連許可を追加する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeMediaUploadPermission($auth)
    {
        // "mediaUpLoadListException" という許可を追加
        $mediaUpLoadListException = $auth->createPermission('mediaUpLoadListException');
        $mediaUpLoadListException->description = '画像一覧不許可';
        $auth->add($mediaUpLoadListException);

        // "mediaUploadCreateException" という許可を追加
        $mediaUploadCreateException = $auth->createPermission('mediaUploadCreateException');
        $mediaUploadCreateException->description = '画像アップロード不許可';
        $auth->add($mediaUploadCreateException);
    }

    /**
     * 9.検索キー関連許可を追加する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeSearchKeyPermission($auth)
    {
        // "searchException" という許可を追加
        $searchException = $auth->createPermission('searchException');
        $searchException->description = '検索キー項目不許可';
        $auth->add($searchException);

        // "jobTypeException" という許可を追加
        $jobTypeException = $auth->createPermission('jobTypeException');
        $jobTypeException->description = '職種検索キー不許可';
        $auth->add($jobTypeException);

        // "areaException" という許可を追加
        $areaException = $auth->createPermission('areaException');
        $areaException->description = 'エリア検索キー不許可';
        $auth->add($areaException);

        // "prefdistException" という許可を追加
        $prefdistException = $auth->createPermission('prefdistException');
        $prefdistException->description = '地域グループ検索キー不許可';
        $auth->add($prefdistException);

        // "employmentException" という許可を追加
        $employmentException = $auth->createPermission('employmentException');
        $employmentException->description = '雇用形態検索キー不許可';
        $auth->add($employmentException);

        // "occupationMemberException" という許可を追加
        $occupationMemberException = $auth->createPermission('occupationMemberException');
        $occupationMemberException->description = '属性キー不許可';
        $auth->add($occupationMemberException);

        // "worktimeException" という許可を追加
        $worktimeException = $auth->createPermission('worktimeException');
        $worktimeException->description = '勤務時間検索キー不許可';
        $auth->add($worktimeException);

        // "optionJobListException" という許可を追加
        $wageException = $auth->createPermission('wageException');
        $wageException->description = '給与検索キー不許可';
        $auth->add($wageException);

        // "meritException" という許可を追加
        $meritException = $auth->createPermission('meritException');
        $meritException->description = 'メリット検索キー不許可';
        $auth->add($meritException);

        // "option1Exception" という許可を追加
        $option1Exception = $auth->createPermission('option1Exception');
        $option1Exception->description = 'オプション検索キー1不許可';
        $auth->add($option1Exception);

        // "option2Exception" という許可を追加
        $option2Exception = $auth->createPermission('option2Exception');
        $option2Exception->description = 'オプション検索キー2不許可';
        $auth->add($option2Exception);

        // "option3Exception" という許可を追加
        $option3Exception = $auth->createPermission('option3Exception');
        $option3Exception->description = 'オプション検索キー3不許可';
        $auth->add($option3Exception);
    }

    /**
     * 8.項目管理関連許可を追加する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeOptionPermission($auth)
    {
        // "optionJobListException" という許可を追加
        $optionJobException = $auth->createPermission('optionJobException');
        $optionJobException->description = '求人情報項目管理不許可';
        $auth->add($optionJobException);

        // "optionCorpException" という許可を追加
        $optionCorpException = $auth->createPermission('optionCorpException');
        $optionCorpException->description = '代理店項目管理不許可';
        $auth->add($optionCorpException);

        // "optionClientException" という許可を追加
        $optionClientException = $auth->createPermission('optionClientException');
        $optionClientException->description = '掲載企業項目管理不許可';
        $auth->add($optionClientException);

        // "optionAdminException" という許可を追加
        $optionAdminException = $auth->createPermission('optionAdminException');
        $optionAdminException->description = '管理者項目管理不許可';
        $auth->add($optionAdminException);

        // "optionApplicationException" という許可を追加
        $optionApplicationException = $auth->createPermission('optionApplicationException');
        $optionApplicationException->description = '応募者項目管理不許可';
        $auth->add($optionApplicationException);

        // "optionMemberException" という許可を追加
        $optionMemberException = $auth->createPermission('optionMemberException');
        $optionMemberException->description = '登録者項目管理不許可';
        $auth->add($optionMemberException);

        // "optionDisptypeException" という許可を追加
        $optionDisptypeException = $auth->createPermission('optionDisptypeException');
        $optionDisptypeException->description = '掲載タイプ項目管理不許可';
        $auth->add($optionDisptypeException);
    }

    /**
     * 7.応募者関連許可を追加する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeApplicationPermission($auth)
    {
        // "applicationListException" という許可を追加
        $applicationListException = $auth->createPermission('applicationListException');
        $applicationListException->description = '応募者一覧・編集・削除不許可';
        $auth->add($applicationListException);
    }

    /**
     * 6.管理者関連許可を追加する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeAdminPermission($auth)
    {
        // "adminListException" という許可を追加
        $adminListException = $auth->createPermission('adminListException');
        $adminListException->description = '管理者一覧・編集・削除不許可';
        $auth->add($adminListException);

        // "adminCreateException" という許可を追加
        $adminCreateException = $auth->createPermission('adminCreateException');
        $adminCreateException->description = '管理者登録不許可';
        $auth->add($adminCreateException);

        // "adminProfileException" という許可を追加
        $adminProfileException = $auth->createPermission('adminProfileException');
        $adminProfileException->description = '管理者プロフィール不許可';
        $auth->add($adminProfileException);
    }

    /**
     * 5.求人原稿関連許可を追加する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeJobPermission($auth)
    {
        // "jobListException" という許可を追加
        $jobListException = $auth->createPermission('jobListException');
        $jobListException->description = '求人原稿一覧・編集・削除不許可';
        $auth->add($jobListException);

        // "jobCreateException" という許可を追加
        $jobCreateException = $auth->createPermission('jobCreateException');
        $jobCreateException->description = '求人原稿登録不許可';
        $auth->add($jobCreateException);
    }

    /**
     * 4.掲載企業関連許可を追加し、
     * isOwnClient許可とisOwnルールを関連付け、
     * isOwnClient許可とcorpDetail許可を結びつけ、
     * client権限がisOwnClient許可を使えるよう設定する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeClientPermission($auth)
    {
        // "clientListException" という許可を追加
        $clientListException = $auth->createPermission('clientListException');
        $clientListException->description = '掲載企業一覧・編集・削除不許可';
        $auth->add($clientListException);

        // "clientCreateException" という許可を追加
        $clientCreateException = $auth->createPermission('clientCreateException');
        $clientCreateException->description = '掲載企業登録不許可';
        $auth->add($clientCreateException);

        // "clientDetail" という許可を追加
        $clientDetail = $auth->createPermission('clientDetail');
        $clientDetail->description = '掲載企業詳細許可';
        $auth->add($clientDetail);

        // "isOwnClient" という許可を作成し、それに'isOwn'ルールを関連付ける。
        $isOwnClient = $auth->createPermission('isOwnClient');
        $isOwnClient->description = 'それが自分自身の掲載企業のものであるか';
        $isOwnClient->ruleName = 'isOwn';
        $auth->add($isOwnClient);

        // "isOwnClient" は "clientDetail" から使われる
        $auth->addChild($isOwnClient, $clientDetail);

        // "client" に自分の掲載企業詳細を閲覧することを許可する
        $clientAdmin = $auth->getRole('client_admin');
        $auth->addChild($clientAdmin, $isOwnClient);
    }

    /**
     * 3.代理店関連許可を追加し、
     * isOwnCorp許可（isOwnルールと関連づいている）とcorpDetailを結びつけ、
     * corp権限がisOwnCorp許可を使えるよう設定する
     * @param \yii\rbac\DbManager $auth
     */
    private function makeCorpPermission($auth)
    {
        // "corpListException" という許可を追加
        $corpListException = $auth->createPermission('corpListException');
        $corpListException->description = '代理店一覧・編集・削除不許可';
        $auth->add($corpListException);

        // "corpCreateException" という許可を追加
        $corpCreateException = $auth->createPermission('corpCreateException');
        $corpCreateException->description = '代理店新規登録不許可';
        $auth->add($corpCreateException);

        // "corpDetail" という許可を追加
        $corpDetail = $auth->createPermission('corpDetail');
        $corpDetail->description = '代理店詳細許可';
        $auth->add($corpDetail);

        // "isOwnCorp" は "corpDetail" から使われる
        $isOwnCorp = $auth->getPermission('isOwnCorp');
        $auth->addChild($isOwnCorp, $corpDetail);

        // "corp" に自分の代理店詳細を閲覧することを許可する
        $corp = $auth->getRole('corp_admin');
        $auth->addChild($corp, $isOwnCorp);
    }

    /**
     * 2.IsOwnRuleルールを追加し、
     * IsOwnCorpRuleという許可を作り、両者を結びつける
     * @param \yii\rbac\DbManager $auth
     */
    private function makeIsOwnRule($auth)
    {
        // ルールを呼び出す
        // 自分自身のもののみ許可
        $isOwnRule = new isOwnRule();
        /** ルールを伴う許可を作成し、その許可を親の許可に関連付け、そのルールを伴った許可をロールに関連付ける */
        // ルールを追加する
        $auth->add($isOwnRule);

        // "isOwnCorp" という許可を作成し、それにルールを関連付ける。
        $isOwnCorp = $auth->createPermission('isOwnCorp');
        $isOwnCorp->description = 'それが自分自身の代理店のものであるか';
        $isOwnCorp->ruleName = $isOwnRule->name;
        $auth->add($isOwnCorp);
    }

    /**
     * 1.ロールの追加ととりあえずの割り当てをする
     * @param \yii\rbac\DbManager $auth
     */
    private function makeRole($auth)
    {
        // "client_admin" というロールを追加
        $clientAdmin = $auth->createRole('client_admin');
        $auth->add($clientAdmin);

        // "corp_admin" というロールを追加
        $corpAdmin = $auth->createRole('corp_admin');
        $auth->add($corpAdmin);

        // "owner_admin" というロールを追加
        $ownerAdmin = $auth->createRole('owner_admin');
        $auth->add($ownerAdmin);

        // ロールをユーザに割り当てる。1 と 2 は IdentityInterface::getId() によって返される ID
        // IdentityInterface::getId() は、通常は User モデルの中で実装される
        // JM2の場合はadmin_masterのidカラムの値
        $auth->assign($clientAdmin, 3);
        $auth->assign($corpAdmin, 2);
        $auth->assign($ownerAdmin, 1);
    }
}
