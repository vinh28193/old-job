<?php

namespace models\manage;

use app\models\AdminPasswordSetting;
use app\models\JobMasterDisp;
use app\models\manage\InquiryMaster;
use app\models\manage\MemberMaster;
use app\models\PasswordReminder;
use tests\codeception\unit\fixtures\SiteMasterFixture;
use tests\codeception\unit\fixtures\JobMasterFixture;
use Yii;
use yii\helpers\Url;
use app\models\MailSend;
use app\models\Apply;
use app\models\manage\CorpMaster;
use app\models\manage\SendMailSet;
use app\models\manage\JobMaster;
use app\models\manage\AdminMaster;
use tests\codeception\unit\JmTestCase;
use tests\codeception\fixtures\SendMailSetFixture;
use app\modules\manage\models\JobReview;
use app\modules\manage\models\ManageAuth;
use app\modules\manage\models\Manager;
use app\models\manage\JobReviewStatus;

/**
 * Class SendMailSetTest
 * @package models\manage
 *
 * @property SendMailSetFixture $send_mail_set
 */
class SendMailSetTest extends JmTestCase
{
    /**
     * 一応
     */
    public function testTableName()
    {
        $model = new SendMailSet();
        verify($model->tableName())->equals('send_mail_set');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new SendMailSet();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new SendMailSet();
            $model->validate();
            verify($model->hasErrors('from_name'))->true();
            verify($model->hasErrors('from_address'))->true();
            verify($model->hasErrors('subject'))->true();
            verify($model->hasErrors('contents'))->true();
            verify($model->hasErrors('mail_sign'))->true();
            verify($model->hasErrors('mail_name'))->true();
            verify($model->hasErrors('notification_address'))->false();
            $model->scenario = SendMailSet::SCENARIO_INQUILY_NOTIFICATION;
            $model->validate();
            verify($model->hasErrors('notification_address'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new SendMailSet();
            $model->load([$model->formName() => [
                'mail_to' => '文字列',
                'valid_chk' => '文字列',
                'mail_type_id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('mail_to'))->true();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('mail_type_id'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new SendMailSet();
            $model->load([$model->formName() => [
                'contents' => (int)1,
                'mail_sign' => (int)1,
                'mail_name' => (int)1,
                'from_name' => (int)1,
                'subject' => (int)1,
                'notification_address' => (int)1,
                'mail_to_description' => (int)1,
            ]]);
            $model->validate();
            verify($model->hasErrors('contents'))->true();
            verify($model->hasErrors('mail_sign'))->true();
            verify($model->hasErrors('mail_name'))->true();
            verify($model->hasErrors('from_name'))->true();
            verify($model->hasErrors('subject'))->true();
            verify($model->hasErrors('notification_address'))->true();
            verify($model->hasErrors('mail_to_description'))->true();
        });
        $this->specify('文字列の最大', function () {
            $model = new SendMailSet();
            $model->load([$model->formName() => [
                'from_name' => str_repeat('a', 256),
                'mail_name' => str_repeat('a', 21),
                'from_address' => str_repeat('a', 1001),
                'subject' => str_repeat('a', 1001),
                'notification_address' => str_repeat('a', 256),
                'mail_to_description' => str_repeat('a', 256),
            ]]);
            $model->validate();
            verify($model->hasErrors('from_name'))->true();
            verify($model->hasErrors('mail_name'))->true();
            verify($model->hasErrors('from_address'))->true();
            verify($model->hasErrors('subject'))->true();
            verify($model->hasErrors('notification_address'))->true();
            verify($model->hasErrors('mail_to_description'))->true();
        });
        $this->specify('emailチェック', function () {
            $model = new SendMailSet();
            $model->load([$model->formName() => [
                'from_address' => '文字列',
                'notification_address' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('from_address'))->true();
            verify($model->hasErrors('notification_address'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new SendMailSet();
            $model->load([$model->formName() => [
                'contents' => str_repeat('a', 3000),
                'default_contents' => str_repeat('a', 3000),
                'mail_sign' => str_repeat('a', 900),
                'mail_name' => str_repeat('a', 15),
                'mail_to' => SendMailSet::MAIL_TO_APPLICATION,
                'valid_chk' => SendMailSet::VALID,
                'from_name' => str_repeat('a', 150),
                'from_address' => 'testEmail@test.test',
                'subject' => str_repeat('a', 200),
                'mail_type_id' => MailSend::TYPE_SEND_JOB,
                'notification_address' => 'testEmail@test.test',
                'mail_to_description' => str_repeat('a', 254),
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * mailToLabelのtest
     */
    public function testMailToLabel()
    {
        $this->specify('求職者', function () {
            verify(SendMailSet::getMailToLabel()[SendMailSet::MAIL_TO_APPLICATION])->equals('求職者');
        });
        $this->specify('運営元', function () {
            verify(SendMailSet::getMailToLabel()[SendMailSet::MAIL_TO_OWNER])->equals('運営元');
        });
        $this->specify('代理店', function () {
            verify(SendMailSet::getMailToLabel()[SendMailSet::MAIL_TO_CORP])->equals('代理店');
        });
        $this->specify('掲載企業', function () {
            verify(SendMailSet::getMailToLabel()[SendMailSet::MAIL_TO_CLIENT])->equals('掲載企業');
        });
    }

    /**
     * getFormatTableのtest
     */
    public function testGetFormatTable()
    {
        $model = new SendMailSet();
        $array = [
            'mail_to' =>
                [
                    SendMailSet::MAIL_TO_APPLICATION => Yii::t('app', '求職者'),
                    SendMailSet::MAIL_TO_OWNER => Yii::t('app', '運営元'),
                    SendMailSet::MAIL_TO_CORP => Yii::t('app', '代理店'),
                    SendMailSet::MAIL_TO_CLIENT => Yii::t('app', '掲載企業')
                ]
        ];
        verify($model->formatTable)->equals($array);
    }

    /**
     * getSendUserPropertiesのtest
     */
    public function testGetSendUserProperties()
    {
        // 仕事転送メール
        $this->specify('仕事転送メール :: TYPE_SEND_JOB', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_SEND_JOB;
            $jobMasterDisp = new JobMasterDisp();
            $jobMasterDisp->mailAddress = 'to@mail.address';
            $sendMailSet->model = $jobMasterDisp;
            verify($sendMailSet->sendUserProperties)->equals([['pc_mail_address' => 'to@mail.address']]);
        });

        // 管理者登録メール
        $this->specify('管理者登録メール :: TYPE_ADMN_CREATE', function () {
            $sendMailSet = new SendMailSet();
            $adminMaster = new AdminMaster();
            $sendMailSet->mail_type_id = MailSend::TYPE_ADMN_CREATE;
            $adminMaster->mail_address = 'admin@mail.address';
            $adminMaster->id = 999;
            $sendMailSet->model = $adminMaster;
            verify($sendMailSet->sendUserProperties)->equals([['pc_mail_address' => 'admin@mail.address', 'user_id' => 999]]);
        });

        // 応募登録完了メール
        $this->specify('応募登録完了メール :: TYPE_APPLY_TO_APPLICATION', function () {
            $sendMailSet = new SendMailSet();
            $apply = new Apply();
            $sendMailSet->mail_type_id = MailSend::TYPE_APPLY_TO_APPLICATION;
            $apply->mail_address = 'apply@mail.address';
            $apply->id = 999;
            $sendMailSet->model = $apply;
            verify($sendMailSet->sendUserProperties)->equals([['pc_mail_address' => 'apply@mail.address', 'user_id' => 999]]);
        });

        // 応募登録通知メール　通知先メール設定あり
        $this->specify('応募登録通知メール 通知先メール設定あり :: TYPE_APPLY_TO_ADMIN', function () {
            $sendMailSet = new SendMailSet([
                'mail_type_id' => MailSend::TYPE_APPLY_TO_ADMIN,
                'notification_address' => 'applytonotice@admin.adress'
            ]);
            $apply = new Apply();
            $apply->job_master_id = 999;
            $apply->populateRelation('jobMaster', new JobMaster(['application_mail' => 'applytoadmin@mail.address']));
            $sendMailSet->model = $apply;
            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => 'applytoadmin@mail.address', 'user_id' => 999],
                ['pc_mail_address' => $sendMailSet->notification_address]
            ]);
        });

        // 応募登録通知メール　通知先メール設定なし
        $this->specify('応募登録通知メール 通知先メール設定なし :: TYPE_APPLY_TO_ADMIN', function () {
            $sendMailSet = new SendMailSet();
            $apply = new Apply();
            $sendMailSet->mail_type_id = MailSend::TYPE_APPLY_TO_ADMIN;
            $apply->job_master_id = 999;
            $apply->populateRelation('jobMaster', new JobMaster(['application_mail' => 'applytoadmin@mail.address']));
            $sendMailSet->model = $apply;
            verify($sendMailSet->sendUserProperties)->equals([['pc_mail_address' => 'applytoadmin@mail.address', 'user_id' => 999]]);
        });

        //会員機能未実装のためコメントアウトしています。
        //会員登録完了メール
//        $this->specify('会員登録完了メール :: TYPE_MEMBERSHIP_TO_MEMBER', function () {
//            $sendMailSet = new SendMailSet();
//            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_MEMBER;
//        });

        // 会員登録通知メール
//        $this->specify('会員登録通知メール :: TYPE_MEMBERSHIP_TO_ADMIN', function () {
//            $sendMailSet = new SendMailSet();
//            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_ADMIN;
//        });

        // 管理者パスワード再設定メール
        $this->specify('管理者パスワード再設定メール :: TYPE_MANAGE_PASS_RESET', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_MANAGE_PASS_RESET;
            $adminMaster = new AdminPasswordSetting();
            $adminMaster->mail_address = 'admin@mail.address';
            $adminMaster->id = 999;
            $sendMailSet->model = $adminMaster;
            verify($sendMailSet->sendUserProperties)->equals([['pc_mail_address' => 'admin@mail.address', 'user_id' => 999]]);
        });

        //会員機能未実装のためコメントアウトしています。
        //会員パスワード再設定メール
//        $this->specify('会員パスワード再設定メール :: TYPE_MEMBER_PASS_RESET', function () {
//            $sendMailSet = new SendMailSet();
//            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBER_PASS_RESET;
//            $memberMaster = new MemberMaster();
//            $memberMaster->id = 999;
//            $memberMaster->mail_address = 'admin@mail.address';
//            $sendMailSet->model = $memberMaster;
//            verify($sendMailSet->sendUserProperties)->equals([['pc_mail_address' => 'admin@mail.address', 'user_id' => 999]]);
//        });

        // 掲載の問いあわせ通知 通知先メール設定必須
        $this->specify('掲載の問いあわせ通知 通知先メール設定あり :: TYPE_INQUILY_NOTIFICATION', function () {
            $sendMailSet = new SendMailSet([
                'mail_type_id' => MailSend::TYPE_INQUILY_NOTIFICATION,
                'from_address' => 'inquiry@admin.address',
                'notification_address' => 'inquilytonotice@mail.address'
            ]);
            $sendMailSet->model = new InquiryMaster([
                'mail_address' => 'inquiry@guest.address'
            ]);
            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => $sendMailSet->model->mail_address, 'user_id' => 0],
                ['pc_mail_address' => $sendMailSet->notification_address, 'user_id' => -1]
            ]);
        });

        // 審査依頼、審査OKメール
        $this->specify('審査依頼、審査OKメール :: TYPE_JOB_REVIEW', function () {
            $jobMasterId = 101;
            $notificationAddress = 'job_review_notice@mail.address';
            $sendMailSet = new SendMailSet([
                'mail_type_id' => MailSend::TYPE_JOB_REVIEW,
                'from_address' => 'job_review@admin.address',
                'notification_address' => $notificationAddress
            ]);
            $sendMailSet->model = new JobReview([
                'review' => true,   // 審査OK
                'job_master_id' => $jobMasterId,
            ]);

            // 運営元でログイン
            $this->setIdentity(Manager::OWNER_ADMIN);

            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => ''],
            ]);

            // 代理店でログイン
            $this->setIdentity(Manager::CORP_ADMIN);

            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => $notificationAddress],
            ]);

            // 掲載企業でログイン
            $this->setIdentity(Manager::CLIENT_ADMIN);

            // 代理店審査あり(取得値更新のため、modelを再セット)
            CorpMaster::updateAll(['corp_review_flg' => true], ['id' => $sendMailSet->model->jobMaster->corpMasterId]);
            $sendMailSet->model = new JobReview([
                'review' => true,   // 審査OK
                'job_master_id' => $jobMasterId,
            ]);
            $admins = AdminMaster::find()->addCorpAdminQuery($sendMailSet->model->jobMaster->corpMasterId)->all();
            $users = [];
            foreach ($admins as $admin) {
                $users[] = [
                    ['pc_mail_address' => $admin->mail_address ?? '', 'user_id' => $admin->id],
                ];
            }
            verify($sendMailSet->sendUserProperties)->equals($users);
            // 代理店審査なし(取得値更新のため、modelを再セット)
            CorpMaster::updateAll(['corp_review_flg' => false], ['id' => $sendMailSet->model->jobMaster->corpMasterId]);
            $sendMailSet->model = new JobReview([
                'review' => true,   // 審査OK
                'job_master_id' => $jobMasterId,
            ]);
            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => $notificationAddress],
            ]);
        });

        // 審査NGメール
        $this->specify('審査NGメール :: TYPE_JOB_REVIEW', function () {
            $jobMasterId = 101;
            $notificationAddress = 'job_review_notice@mail.address';
            $sendMailSet = new SendMailSet([
                'mail_type_id' => MailSend::TYPE_JOB_REVIEW,
                'from_address' => 'job_review@admin.address',
                'notification_address' => $notificationAddress
            ]);

            // 運営元でログイン
            $this->setIdentity(Manager::OWNER_ADMIN);

            // 代理店審査あり
            $jobReview = new JobReview();
            $jobReview->scenario = JobReview::SCENARIO_REVIEW;
            $jobReview->load([
                $jobReview->formName() => [
                    'review' => false,  // 審査NG
                    'job_master_id' => $jobMasterId,
                ],
            ]);
            $sendMailSet->model = $jobReview;
            CorpMaster::updateAll(['corp_review_flg' => true], ['id' => $sendMailSet->model->jobMaster->corpMasterId]);
            $admins = AdminMaster::find()->addCorpAdminQuery($sendMailSet->model->jobMaster->corpMasterId)->all();
            $users = [];
            foreach ($admins as $admin) {
                $users[] = ['pc_mail_address' => $admin->mail_address ?? '', 'user_id' => $admin->id];
            }
            $users[] = ['pc_mail_address' => $sendMailSet->model->jobMaster->application_mail, 'user_id' => $jobMasterId];
            verify($sendMailSet->sendUserProperties)->equals($users);

            // 代理店審査なし(取得値更新のため、modelを再セット)
            CorpMaster::updateAll(['corp_review_flg' => false], ['id' => $sendMailSet->model->jobMaster->corpMasterId]);
            $jobReview = new JobReview();
            $jobReview->scenario = JobReview::SCENARIO_REVIEW;
            $jobReview->load([
                $jobReview->formName() => [
                    'review' => false,  // 審査NG
                    'job_master_id' => $jobMasterId,
                ],
            ]);
            $sendMailSet->model = $jobReview;
            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => $sendMailSet->model->jobMaster->application_mail, 'user_id' => $jobMasterId],
            ]);

            // 代理店でログイン
            $this->setIdentity(Manager::CORP_ADMIN);

            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => $sendMailSet->model->jobMaster->application_mail, 'user_id' => $jobMasterId],
            ]);

            // 掲載企業でログイン
            $this->setIdentity(Manager::CLIENT_ADMIN);

            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => ''],
            ]);
        });

        // 審査完了メール
        $this->specify('審査完了メール :: TYPE_JOB_REVIEW_COMPLETE', function () {
            $jobMasterId = 101;
            $notificationAddress = '';
            $sendMailSet = new SendMailSet([
                'mail_type_id' => MailSend::TYPE_JOB_REVIEW_COMPLETE,
                'from_address' => 'job_review_complete@admin.address',
                'notification_address' => $notificationAddress
            ]);
            $sendMailSet->model = new JobReview([
                'review' => false,   // 審査NG
                'job_master_id' => $jobMasterId,
            ]);

            verify($sendMailSet->sendUserProperties)->equals([
                ['pc_mail_address' => $sendMailSet->model->jobMaster->application_mail ?? '', 'user_id' => $jobMasterId],
            ]);
        });
    }

    /**
     * getReplacedTitleのtest
     * getReplacesとgetReplaceとreplaceのtestも兼ねる
     */
    public function testGetReplacedTitle()
    {
        // 初期の文字
        $string = 'サイトURL:[SITE_URL],
            サイト名:[SITE_NAME],
            仕事詳細URL:[JOB_URL],
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:[JOB_ID],
            審査ステータス:[JOB_REVIEW_STATUS],
            審査コメント:[JOB_REVIEW_COMMENT]';

        // 共通の置換
        $siteName = Yii::$app->site->info->site_name;
        $siteUrl = Url::to('/', true);

        $this->specify('TYPE_SEND_JOB', function () use ($string,$siteName,$siteUrl){
            // 仕事転送メール
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_SEND_JOB;
            $sendMailSet->subject = $string;
            $jobMaster = new JobMasterDisp();
            $jobMaster->job_no = 999;
            $sendMailSet->model = $jobMaster;

            $jobUrl = Url::to('/' . Yii::$app->tenant->tenant->kyujin_detail_dir . '/' . 999, true);

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:{$jobUrl},
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:[JOB_ID],
            審査ステータス:[JOB_REVIEW_STATUS],
            審査コメント:[JOB_REVIEW_COMMENT]");
        });
        // 管理者登録メール
        $this->specify('TYPE_ADMN_CREATE', function () use ($string,$siteUrl,$siteName) {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_ADMN_CREATE;
            $sendMailSet->subject = $string;
            $adminMaster = new AdminMaster();
            $adminMaster->name_sei = '姓';
            $adminMaster->name_mei = '名';
            $adminMaster->login_id = 'loginId';
            $adminMaster->password = 'password';
            $sendMailSet->model = $adminMaster;

            $adminUrl = Url::to('/manage/login', true);

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:[JOB_URL],
            管理者名:{$adminMaster->fullName},
            管理画面URL:{$adminUrl},
            ログインID:{$adminMaster->login_id},
            ログインパスワード:{$adminMaster->password},
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:[JOB_ID],
            審査ステータス:[JOB_REVIEW_STATUS],
            審査コメント:[JOB_REVIEW_COMMENT]");
        });

        // 応募登録完了メール
        $this->specify('TYPE_APPLY_TO_APPLICATION', function () use ($string,$siteUrl,$siteName) {
            $sendMailSet = new SendMailSet;
            $sendMailSet->mail_type_id = MailSend::TYPE_APPLY_TO_APPLICATION;
            $sendMailSet->subject = $string;
            $applicationMaster = new Apply();
            $applicationMaster->name_sei = '姓';
            $applicationMaster->name_sei = '名';
            $applicationMaster->application_no = 99;
            $entryHistoryUrl = Url::toRoute('/mypage/entry-history', true);
            $applicationMaster->populateRelation('jobMaster', new JobMaster(['job_no' => 999]));
            $jobUrl = Url::to('/' . Yii::$app->tenant->tenant->kyujin_detail_dir . '/' . 999, true);
            $sendMailSet->model = $applicationMaster;

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:{$jobUrl},
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:{$applicationMaster->fullName},
            応募番号:{$applicationMaster->application_no}
            応募履歴確認画面:{$entryHistoryUrl},
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:[JOB_ID],
            審査ステータス:[JOB_REVIEW_STATUS],
            審査コメント:[JOB_REVIEW_COMMENT]");
        });

        // 応募登録通知メール
        $this->specify('TYPE_APPLY_TO_ADMIN', function () use ($string,$siteUrl,$siteName) {
            $sendMailSet = new SendMailSet;
            $sendMailSet->mail_type_id = MailSend::TYPE_APPLY_TO_ADMIN;
            $sendMailSet->subject = $string;
            $applicationMaster = new Apply();
            $applicationMaster->name_sei = '姓';
            $applicationMaster->name_sei = '名';
            $applicationMaster->application_no = 98;
            $entryHistoryUrl = Url::toRoute('/mypage/entry-history', true);
            $applicationMaster->populateRelation('jobMaster', new JobMaster(['job_no' => 999]));
            $jobUrl = Url::to('/' . Yii::$app->tenant->tenant->kyujin_detail_dir . '/' . 999, true);
            $adminSiteUrl = Url::toRoute('/manage/login/', true);
            $sendMailSet->model = $applicationMaster;

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:{$jobUrl},
            管理者名:[ADMIN_NAME],
            管理画面URL:{$adminSiteUrl},
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:{$applicationMaster->fullName},
            応募番号:{$applicationMaster->application_no}
            応募履歴確認画面:{$entryHistoryUrl},
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:[JOB_ID],
            審査ステータス:[JOB_REVIEW_STATUS],
            審査コメント:[JOB_REVIEW_COMMENT]");
        });

        // 会員登録完了メール
//        $this->specify('TYPE_MEMBERSHIP_TO_MEMBER', function () use ($string,$siteUrl,$siteName) {
//            $sendMailSet = new SendMailSet;
//            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_MEMBER;
//        });

        // 会員登録通知メール
//        $this->specify('TYPE_MEMBERSHIP_TO_ADMIN', function () use ($string,$siteUrl,$siteName) {
//            $sendMailSet = new SendMailSet;
//            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_ADMIN;
//        });

//        会員機能未実装のためコメントアウトしています。
         // 会員パスワード再設定メール
 //        $sendMailSet = new SendMailSet();
 //        $sendMailSet->mail_type_id = MailSend::TYPE_MEMBER_PASS_RESET;

        // 管理者パスワード再設定メール
        $this->specify('TYPE_MANAGE_PASS_RESET', function () use ($string,$siteUrl,$siteName) {
            $sendMailSet = new SendMailSet;
            $sendMailSet->mail_type_id = MailSend::TYPE_MANAGE_PASS_RESET;
            $sendMailSet->subject = $string;
            // 関連モデル準備
            $adminMaster = new AdminPasswordSetting();
            $adminMaster->name_sei = '姓';
            $adminMaster->name_mei = '名';
            $adminMaster->login_id = 'loginId';
            // リレーションモデル準備
            $passwordReminder = new PasswordReminder();
            $passwordReminder->key_id = $adminMaster->id;
            $passwordReminder->collation_key = 'keykeykey';
            // リレーション代入
            $adminMaster->populateRelation('passwordReminder', $passwordReminder);
            // 関連モデル代入
            $sendMailSet->model = $adminMaster;
            // 置換準備
            $adminSiteUrl = Url::toRoute('/manage/login/', true);
            $passSettingUrl = Url::toRoute(['/pass/entry', 'key' => $sendMailSet->model->passwordReminder->collation_key], true);

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:[JOB_URL],
            管理者名:{$adminMaster->fullName},
            管理画面URL:{$adminSiteUrl},
            ログインID:{$adminMaster->login_id},
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:{$passSettingUrl},
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:[JOB_ID],
            審査ステータス:[JOB_REVIEW_STATUS],
            審査コメント:[JOB_REVIEW_COMMENT]");
        });

        //掲載の問いあわせ通知
        $this->specify('TYPE_INQUILY_NOTIFICATION', function () use ($string,$siteUrl,$siteName) {
            $sendMailSet = new SendMailSet;
            $sendMailSet->mail_type_id = MailSend::TYPE_INQUILY_NOTIFICATION;
            $sendMailSet->subject = $string;
            //関連モデルの準備
            $inquiryMaster = new InquiryMaster();
            $inquiryMaster->company_name = 'ああああ';
            $inquiryMaster->tanto_name = 'うううう';
            // 関連モデル代入
            $sendMailSet->model = $inquiryMaster;

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:[JOB_URL],
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:{$inquiryMaster->tanto_name},
            会社名:{$inquiryMaster->company_name},
            求人ID:[JOB_ID],
            審査ステータス:[JOB_REVIEW_STATUS],
            審査コメント:[JOB_REVIEW_COMMENT]");
        });

        //審査依頼、審査OK/NG
        $this->specify('TYPE_JOB_REVIEW', function () use ($string,$siteUrl,$siteName) {
            $sendMailSet = new SendMailSet;
            $sendMailSet->mail_type_id = MailSend::TYPE_JOB_REVIEW;
            $sendMailSet->subject = $string;
            //関連モデルの準備
            $jobReview = new JobReview();
            $jobReview->job_master_id = 101;
            $jobReview->job_review_status_id = JobReviewStatus::STEP_JOB_EDIT;
            $jobReview->comment = 'コメントです';
            // 関連モデル代入
            $sendMailSet->model = $jobReview;
            // 置換準備
            $adminSiteUrl = Url::toRoute('/manage/login/', true);

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:[JOB_URL],
            管理者名:[ADMIN_NAME],
            管理画面URL:{$adminSiteUrl},
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:{$jobReview->jobMaster->job_no},
            審査ステータス:{$jobReview->jobReviewStatus->name},
            審査コメント:{$jobReview->comment}");
        });

        //審査完了
        $this->specify('TYPE_JOB_REVIEW_COMPLETE', function () use ($string,$siteUrl,$siteName) {
            $sendMailSet = new SendMailSet;
            $sendMailSet->mail_type_id = MailSend::TYPE_JOB_REVIEW_COMPLETE;
            $sendMailSet->subject = $string;
            //関連モデルの準備
            $jobReview = new JobReview();
            $jobReview->job_master_id = 101;
            $jobReview->job_review_status_id = JobReviewStatus::STEP_REVIEW_OK;
            $jobReview->comment = 'コメントです';
            // 関連モデル代入
            $sendMailSet->model = $jobReview;
            // 置換準備
            $adminSiteUrl = Url::toRoute('/manage/login/', true);

            verify($sendMailSet->replacedTitle)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:[JOB_URL],
            管理者名:[ADMIN_NAME],
            管理画面URL:{$adminSiteUrl},
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL],
            担当者名:[REPRESENTATIVE_NAME],
            会社名:[COMPANY_NAME],
            求人ID:{$jobReview->jobMaster->job_no},
            審査ステータス:{$jobReview->jobReviewStatus->name},
            審査コメント:{$jobReview->comment}");
        });
    }

    /**
     * getReplacedBodyのtest
     * 置換testはtitleの方で網羅できているため、こちらでは
     * 挿入文のtestのみ行う
     */
    public function testGetReplacedBody()
    {
        // 初期の文字
        $string = 'サイトURL:[SITE_URL],
            サイト名:[SITE_NAME],
            仕事詳細URL:[JOB_URL],
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL]';


        // 仕事転送メール
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_SEND_JOB;
            $sendMailSet->contents = $string;
            $sendMailSet->mail_sign = $string;
            $sendMailSet->additionalText = $string;
            $jobMaster = new JobMaster();
            $jobMaster->job_no = 999;
            $sendMailSet->model = $jobMaster;

            // 置換
            $siteName = Yii::$app->site->info->site_name;
            $siteUrl = Url::to('/', true);
            $jobUrl = Url::to('/' . Yii::$app->tenant->tenant->kyujin_detail_dir . '/' . 999, true);

            verify($sendMailSet->replacedBody)->equals("サイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:{$jobUrl},
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL]\n\nサイトURL:[SITE_URL],
            サイト名:[SITE_NAME],
            仕事詳細URL:[JOB_URL],
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL]\n\nサイトURL:{$siteUrl},
            サイト名:{$siteName},
            仕事詳細URL:{$jobUrl},
            管理者名:[ADMIN_NAME],
            管理画面URL:[ADMIN_SITE_URL],
            ログインID:[LOGIN_ID],
            ログインパスワード:[LOGIN_PASS],
            会員名[MEMBER_NAME],
            応募者名:[APPLICATION_NAME],
            応募番号:[APPLICATION_NO]
            応募履歴確認画面:[ENTRY_HISTORY_URL],
            パスワード再設定画面:[PASS_SETTING_URL]");
    }
    /**
     * getNeedlesのtest
     * getOptionNeedlesのtestも兼ねる
     */
    public function testGetNeedles(){
        $model = new SendMailSet();
        $model->mail_type_id = MailSend::TYPE_SEND_JOB;
        verify($model->needles)->equals([
            SendMailSet::NEEDLE_SITE_URL,
            SendMailSet::NEEDLE_SITE_NAME,
            SendMailSet::NEEDLE_JOB_URL
        ]);
    }
    /**
     * getNeedlesのtest
     * getOptionNeedlesのtestも兼ねる
     */
    public function testGetOptionNeedles()
    {
        // 仕事情報転送メール
        $this->specify('仕事情報転送メール :: TYPE_SEND_JOB', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_SEND_JOB;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_JOB_URL
            ]);
        });

        // 管理者登録メール
        $this->specify('管理者登録メール :: TYPE_ADMN_CREATE', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_ADMN_CREATE;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_ADMIN_NAME,
                SendMailSet::NEEDLE_LOGIN_ID,
                SendMailSet::NEEDLE_LOGIN_PASS,
                SendMailSet::NEEDLE_ADMIN_SITE_URL,
            ]);
        });

        // 応募登録メール(応募者宛)
        $this->specify('応募登録メール(応募者宛) :: TYPE_APPLY_TO_APPLICATION', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_APPLY_TO_APPLICATION;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_APPLICATION_NAME,
                SendMailSet::NEEDLE_APPLICATION_NO,
                SendMailSet::NEEDLE_ENTRY_HISTORY_URL,
                SendMailSet::NEEDLE_JOB_URL,
            ]);
        });

        // 応募登録メール(管理者宛)
        $this->specify('応募登録メール(管理者宛) :: TYPE_APPLY_TO_ADMIN', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_APPLY_TO_ADMIN;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_APPLICATION_NAME,
                SendMailSet::NEEDLE_APPLICATION_NO,
                SendMailSet::NEEDLE_ENTRY_HISTORY_URL,
                SendMailSet::NEEDLE_JOB_URL,
                SendMailSet::NEEDLE_ADMIN_SITE_URL,
            ]);
        });
        // 会員機能未実装のためコメントアウトしています。
//        $this->specify('会員機能未実装のためコメントアウトしています :: TYPE_MEMBERSHIP_TO_MEMBER', function () {
//            $model = new SendMailSet();
//            $model->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_MEMBER;
//            verify($model->optionNeedles)->equals([
//                SendMailSet::NEEDLE_MEMBER_NAME
//            ]);
//        });

//        $this->specify('会員機能未実装のためコメントアウトしています :: TYPE_MEMBERSHIP_TO_ADMIN', function () {
//            $model = new SendMailSet();
//            $model->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_ADMIN;
//            verify($model->optionNeedles)->equals([
//                SendMailSet::NEEDLE_MEMBER_NAME
//            ]);
//        });

        // 管理者パスワード再設定
        $this->specify('管理者パスワード再設定 :: TYPE_MANAGE_PASS_RESET', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_MANAGE_PASS_RESET;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_LOGIN_ID,
                SendMailSet::NEEDLE_ADMIN_NAME,
                SendMailSet::NEEDLE_PASS_SETTING_URL,
                SendMailSet::NEEDLE_ADMIN_SITE_URL
            ]);
        });

        // 会員機能未実装のためコメントアウトしています。
//        $this->specify('会員機能未実装のためコメントアウトしています :: TYPE_MEMBER_PASS_RESET', function () {
//            $model = new SendMailSet();
//            $model->mail_type_id = MailSend::TYPE_MEMBER_PASS_RESET;
//            verify($model->optionNeedles)->equals([
//                SendMailSet::NEEDLE_LOGIN_ID,
//                SendMailSet::NEEDLE_LOGIN_PASS,
//                SendMailSet::NEEDLE_MEMBER_NAME,
//                SendMailSet::NEEDLE_PASS_SETTING_URL
//            ]);
//        });

        //掲載の問いあわせ通知
        $this->specify('TYPE_INQUILY_NOTIFICATION', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_INQUILY_NOTIFICATION;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_COMPANY_NAME,
                SendMailSet::NEEDLE_REPRESENTATIVE_NAME
            ]);
        });

        // 審査
        $this->specify('TYPE_JOB_REVIEW', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_JOB_REVIEW;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_ADMIN_SITE_URL,
                SendMailSet::NEEDLE_JOB_ID,
                SendMailSet::NEEDLE_JOB_REVIEW_STATUS,
                SendMailSet::NEEDLE_JOB_REVIEW_COMMENT,
            ]);
        });
        // 審査完了
        $this->specify('TYPE_JOB_REVIEW_COMPLETE', function () {
            $model = new SendMailSet();
            $model->mail_type_id = MailSend::TYPE_JOB_REVIEW_COMPLETE;
            verify($model->optionNeedles)->equals([
                SendMailSet::NEEDLE_ADMIN_SITE_URL,
                SendMailSet::NEEDLE_JOB_ID,
                SendMailSet::NEEDLE_JOB_REVIEW_STATUS,
                SendMailSet::NEEDLE_JOB_REVIEW_COMMENT,
            ]);
        });
    }

    /**
     * getAdditionalTextとsetAdditionalTextのtest
     * todo job_masterのテストレコードが整備でき次第コメントアウト外す
     */
     public function testGetAdditionalText()
     {
         $sendMailSet = new SendMailSet();
         $sendMailSet->mail_type_id = MailSend::TYPE_APPLY_TO_APPLICATION;
         $apply = new Apply();
         $apply->job_master_id = $this->id(1, 'job_master');
         $sendMailSet->model = $apply;
         $additionalText = $apply->jobMaster->mail_body;
         verify($sendMailSet->additionalText)->equals($additionalText);

         $additionalText = 'ようこそ';
         $sendMailSet = new SendMailSet();
         $sendMailSet->additionalText = $additionalText;
         verify($sendMailSet->additionalText)->equals($additionalText);
     }
    /**
     * getEntityIdのtest
     * todo testGetEntityId
     */
    public function testGetEntityId()
    {
        $this->specify('TYPE_SEND_JOB', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_SEND_JOB;
            $sendMailSet->model = new JobMasterDisp(['id' => 999999]);
            verify($sendMailSet->entityId)->equals(999999);
        });

        $this->specify('TYPE_APPLY_TO_APPLICATION', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_APPLY_TO_APPLICATION;
            $sendMailSet->model = new Apply(['job_master_id' => 999999]);
            verify($sendMailSet->entityId)->equals(999999);
        });

        $this->specify('TYPE_APPLY_TO_ADMIN', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_APPLY_TO_ADMIN;
            $sendMailSet->model = new Apply(['id' => 999999]);
            verify($sendMailSet->entityId)->equals(999999);
        });

        $this->specify('TYPE_MEMBERSHIP_TO_MEMBER', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_MEMBER;
            $sendMailSet->model = new MemberMaster();
            verify($sendMailSet->entityId)->equals(0);
        });

        $this->specify('TYPE_MEMBERSHIP_TO_ADMIN', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBERSHIP_TO_ADMIN;
            $sendMailSet->model = new MemberMaster();
            verify($sendMailSet->entityId)->equals(0);
        });

        $this->specify('TYPE_ADMN_CREATE', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_ADMN_CREATE;
            $sendMailSet->model = new AdminMaster();
            verify($sendMailSet->entityId)->equals(0);
        });

        $this->specify('TYPE_MANAGE_PASS_RESET', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_MANAGE_PASS_RESET;
            $sendMailSet->model = new AdminMaster();
            verify($sendMailSet->entityId)->equals(0);
        });

        $this->specify('TYPE_MEMBER_PASS_RESET', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_MEMBER_PASS_RESET;
            $sendMailSet->model = new MemberMaster();
            verify($sendMailSet->entityId)->equals(0);
        });

        $this->specify('TYPE_INQUILY_NOTIFICATION', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_INQUILY_NOTIFICATION;
            $sendMailSet->model = new InquiryMaster();
            verify($sendMailSet->entityId)->equals(0);
        });

        $this->specify('TYPE_JOB_REVIEW', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_JOB_REVIEW;
            $sendMailSet->model = new JobReview(['job_master_id' => 999999]);
            verify($sendMailSet->entityId)->equals(999999);
        });

        $this->specify('TYPE_JOB_REVIEW_COMPLETE', function () {
            $sendMailSet = new SendMailSet();
            $sendMailSet->mail_type_id = MailSend::TYPE_JOB_REVIEW_COMPLETE;
            $sendMailSet->model = new JobReview(['job_master_id' => 999999]);
            verify($sendMailSet->entityId)->equals(999999);
        });
    }
}
