<?php
namespace tests\codeception\unit\common\mail;

use app\common\mail\MailSender;
use proseeds\models\MailSend;
use app\models\MailSend AS AppMailSend;
use tests\codeception\unit\fixtures\AdminMasterFixture;
use tests\codeception\unit\fixtures\SiteMasterFixture;
use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\SendMailSet;
use app\models\Apply;
use app\models\JobMasterDisp;
use app\models\manage\AdminMaster;
use app\models\AdminPasswordSetting;
use app\models\manage\InquiryMaster;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ApplicationMasterFixture;
use yii\helpers\ArrayHelper;

class MailSenderTest extends JmTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * fixture設定
     * @return array
     */
    public function fixtures()
    {
        return [
            'job_master' => JobMasterFixture::className(),
            'site_master' => SiteMasterFixture::className(),
            'application_master' => ApplicationMasterFixture::className(),
            'admin_master' => AdminMasterFixture::className(),
        ];
    }

    public function testMail()
    {
        $mail = [
            'mail_title' => 'タイトル',
            'mail_body' => '本文',
            'mail_type_id' => 1,
            'entity_id' => 11,
        ];

        $this->specify('ログインしていない場合', function () use ($mail) {
            $obj = new MailSender();
            $obj->mail($mail);
            $array = (array)$obj;

            // privateなpropertyをkyeを元に抽出
            $mailProperty = array_filter($array, function ($property) {
                if (key_exists('mail_title', (array)$property)) {
                    return true;
                }
                return false;
            });
            verify(array_shift($mailProperty))->equals([
                'mail_title' => 'タイトル',
                'mail_body' => '本文',
                'mail_type_id' => 1,
                'entity_id' => 11,
                'user_id' => 0,
                'send_pc_chk' => 1,
                'send_mobile_chk' => 0,
            ]);
        });

        $this->setIdentity('client_admin');
        $this->specify('ログインしている場合', function () use ($mail) {
            $obj = new MailSender();
            $obj->mail($mail);
            $array = (array)$obj;

            // privateなpropertyをkyeを元に抽出
            $mailProperty = array_filter($array, function ($property) {
                if (key_exists('mail_title', (array)$property)) {
                    return true;
                }
                return false;
            });
            verify(array_shift($mailProperty))->equals([
                'mail_title' => 'タイトル',
                'mail_body' => '本文',
                'mail_type_id' => 1,
                'entity_id' => 11,
                'user_id' => Yii::$app->user->id,
                'send_pc_chk' => 1,
                'send_mobile_chk' => 0,
            ]);
        });
    }

    public function testUsers()
    {
        $users = [
            ['pc_mail_address' => 'to_pc_address1@pro-seeds.co.jp', 'user_id' => 1],
            ['pc_mail_address' => 'to_pc_address1@pro-seeds.co.jp', 'user_id' => 2],
            ['pc_mail_address' => 'to_pc_address1@pro-seeds.co.jp', 'user_id' => 9999],
        ];
        $obj = new MailSender();
        $obj->users($users);
        $array = (array)$obj;

        // privateなpropertyをkyeを元に抽出
        $usersProperty = array_filter($array, function ($property) {
            foreach ((array)$property as $element) {
                if (key_exists('pc_mail_address', (array)$element)) {
                    return true;
                }
            }
            return false;
        });

        foreach (array_shift($usersProperty) as $index => $user) {
            verify($user)->equals([
                'mobile_mail_address' => '',
                'send_pc_chk' => 1,
                'send_mobile_chk' => 0,
                'pc_mail_address' => $users[$index]['pc_mail_address'],
                'user_id' => $users[$index]['user_id'],
            ]);
        }
    }

    public function testFromName()
    {
        $fromName = [
            'from_mail_address' => 'from_address@pro-seeds.co.jp',
            'from_mail_name' => '存在しないアドレス',
        ];
        $obj = new MailSender();
        $obj->fromName($fromName);
        $array = (array)$obj;

        // privateなpropertyをkyeを元に抽出
        $formNameProperty = array_filter($array, function ($property) {
            if (isset($property->from_mail_address)) {
                return true;
            }
            return false;
        });
        verify(array_shift($formNameProperty))->equals((object)$fromName);
    }

    public function testPreparedInstantSend()
    {
        $obj = new MailSender();
        $mailSend = $obj->mail([
            'mail_title' => 'タイトル',
            'mail_body' => '本文',
            'mail_type_id' => 1,
            'entity_id' => 11,
        ])->users([
            ['pc_mail_address' => 'to_pc_address1@pro-seeds.co.jp', 'user_id' => 55],
            ['pc_mail_address' => 'to_pc_address2@pro-seeds.co.jp', 'user_id' => 66],
        ])->fromName([
            'from_mail_address' => 'from_address@pro-seeds.co.jp',
            'from_mail_name' => '存在しないアドレス',
        ])->preparedInstantSend();
        // prepareMailSendのreturnをそのまま返しているだけなのでMailSendのinstanceを返していることだけ検証する
        verify($mailSend)->isInstanceOf(MailSend::className());
    }

    /**
     * sendAutoMailのtest
     * 実行してエラーが出ないことのみ確認しています。
     */
    public function testSendAutoMail()
    {
        $obj = new MailSender();
        $sendMailSet = new SendMailSet([
            'contents' => str_repeat('a', 3000),
            'default_contents' => str_repeat('a', 3000),
            'mail_sign' => str_repeat('a', 900),
            'mail_name' => str_repeat('a', 15),
            'mail_to' => SendMailSet::MAIL_TO_APPLICATION,
            'valid_chk' => SendMailSet::VALID,
            'from_name' => str_repeat('a', 150),
            'from_address' => 'testEmail@test.test',
            'subject' => str_repeat('a', 200),
            'mail_type_id' => AppMailSend::TYPE_SEND_JOB,
        ]);

        //各メールのタイプに関して、テストしている。
        $array = [
            ['mail_type' => AppMailSend::TYPE_SEND_JOB, 'model' => new JobMasterDisp(['id' => 1, 'mailAddress' => 'testEmail@test.test'])],
            ['mail_type' => AppMailSend::TYPE_ADMN_CREATE, 'model' => AdminMaster::find()->one()],
            ['mail_type' => AppMailSend::TYPE_APPLY_TO_APPLICATION, 'model' => Apply::find()->one()],
            ['mail_type' => AppMailSend::TYPE_APPLY_TO_ADMIN, 'model' => Apply::find()->one()],
//            ['mail_type' => AppMailSend::TYPE_MEMBERSHIP_TO_MEMBER, 'model' => MemberMaster::find()->one()],//todo 会員機能実装次第調整
//            ['mail_type' => MailSend::TYPE_MEMBERSHIP_TO_ADMIN, 'model' => MemberMaster::find()->one()],//todo 会員機能実装次第調整
            ['mail_type' => AppMailSend::TYPE_MANAGE_PASS_RESET, 'model' => AdminPasswordSetting::find()->one()],
//            ['mail_type' => MailSend::TYPE_MEMBER_PASS_RESET, 'model' => MemberMaster::find()->one()],//todo 会員機能実装次第調整
            ['mail_type' => AppMailSend::TYPE_INQUILY_NOTIFICATION, 'model' => new InquiryMaster(['mail_address' => 'testEmail@test.test'])],
        ];

        foreach ($array as $item) {
            $sendMailSet->model = $item['model'];
            $sendMailSet->mail_type_id = $item['mail_type'];
            // 実行して、エラーが出ないことを確認している。
            verify($obj->sendAutoMail($sendMailSet))->null();
        }

        $e = null;
        try {
            $sendMailSet->model = '';
            $obj->sendAutoMail($sendMailSet);
        } catch (\Exception $e) {
        }
        verify($e)->notEmpty();
    }
}