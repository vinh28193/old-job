<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use app\models\AdminPasswordSetting;
use app\models\Apply;
use app\models\JobMasterDisp;
use app\models\MailSend;
use app\modules\manage\models\JobReview;
use Exception;
use proseeds\models\BaseModel;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "send_mail_set".
 *
 * @property integer $id
 * @property string $from_name
 * @property string $from_address
 * @property string $subject
 * @property string $contents
 * @property string $default_contents
 * @property string $mail_sign
 * @property string $mail_to
 * @property integer $valid_chk
 * @property string $mail_name
 * @property integer $mail_type_id
 * @property string $notification_address
 * @property string $mail_to_description
 *
 * @property array $sendUserProperties
 * @property string $replacedTitle
 * @property string $replacedBody
 * @property array $optionNeedles
 * @property array $needles
 * @property array $replaces
 * @property string $additionalText
 * @property int $entityId
 */
class SendMailSet extends BaseModel
{
    /** メール送信先種別 */
    const MAIL_TO_APPLICATION = 0;
    const MAIL_TO_OWNER = 1;
    const MAIL_TO_CORP = 2;
    const MAIL_TO_CLIENT = 3;
    const MAIL_TO_MEMBER = 4;
    /** 有効or無効 */
    const INVALID = 0;
    const VALID = 1;
    /** 置換文字 */
    const NEEDLE_ADMIN_NAME = '[ADMIN_NAME]';
    const NEEDLE_ADMIN_SITE_URL = '[ADMIN_SITE_URL]';
    const NEEDLE_LOGIN_ID = '[LOGIN_ID]';
    const NEEDLE_LOGIN_PASS = '[LOGIN_PASS]';
    const NEEDLE_JOB_URL = '[JOB_URL]';
    const NEEDLE_SITE_URL = '[SITE_URL]';
    const NEEDLE_SITE_NAME = '[SITE_NAME]';
    const NEEDLE_MEMBER_NAME = '[MEMBER_NAME]';
    const NEEDLE_APPLICATION_NAME = '[APPLICATION_NAME]';
    const NEEDLE_ENTRY_HISTORY_URL = '[ENTRY_HISTORY_URL]';
    const NEEDLE_APPLICATION_NO = '[APPLICATION_NO]';
    const NEEDLE_PASS_SETTING_URL = '[PASS_SETTING_URL]';
    const NEEDLE_REPRESENTATIVE_NAME = '[REPRESENTATIVE_NAME]';
    const NEEDLE_COMPANY_NAME = '[COMPANY_NAME]';
    const NEEDLE_JOB_ID = '[JOB_ID]';
    const NEEDLE_JOB_REVIEW_STATUS = '[JOB_REVIEW_STATUS]';
    const NEEDLE_JOB_REVIEW_COMMENT = '[JOB_REVIEW_COMMENT]';
    /** 全ての自動メールで使われる置換文字 */
    const COMMON_NEEDLES = [
        self::NEEDLE_SITE_URL,
        self::NEEDLE_SITE_NAME,
    ];
    /** 管理者用メール設定 */
    const ADMIN_MAIL_SET = 2;
    /** メール種別 - 応募通知メール */
    const MAIL_TYPE_APPLY_MAIL = 'APPLY_MAIL';
    /** メール種別 - 管理者登録通知メール */
    const MAIL_TYPE_ADMIN_MAIL = 'ADMIN_MAIL';
    /** メール種別 - 会員登録通知 */
    const MAIL_TYPE_MEMBER_MAIL = 'MEMBER_MAIL';
    /** メール種別 - 仕事転送メール */
    const MAIL_TYPE_JOB_TRANSFER_MAIL = 'JOB_TRANSFER_MAIL';
    /** メール種別 - パスワード再設定メール */
    const MAIL_TYPE_PASS_RESET_MAIL = 'PASS_RESET_MAIL';
    /** メール種別 - 求人審査メール */
    const MAIL_TYPE_JOB_REVIEW_MAIL = 'JOB_REVIEW_MAIL';
    /** メール種別シナリオ */
    const SCENARIO_INQUILY_NOTIFICATION = 'inquily_notification';

    /** @var string 本文と署名の間に入るテキスト */
    private $_additionalText = '';
    /** @var string 送り先アドレス */
    public $toMailAddress;
    /** @var AdminMAster|ApplicationMaster|MemberMaster|JobReview 関連モデル */
    public $model;
    /** @var array 置換用needleの配列 */
    private $_needles;
    /** @var array 置換用replaceの配列 */
    private $_replaces;

    /**
     * テーブル名
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'send_mail_set';
    }

    public function afterFind()
    {
        parent::afterFind();

        if ($this->mail_type_id == MailSend::TYPE_INQUILY_NOTIFICATION) {
            $this->scenario = self::SCENARIO_INQUILY_NOTIFICATION;
        }
    }

    /**
     * ルール設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['from_name', 'from_address', 'subject', 'contents', 'mail_sign', 'mail_name'], 'required'],
            [['mail_to', 'valid_chk', 'tenant_id', 'mail_type_id'], 'integer'],
            [['contents'], 'string', 'max' => 4000],
            ['mail_sign', 'string', 'max' => 1000],
            [['from_name', 'subject'], 'string', 'max' => 255],
            [['mail_name', 'mail_type'], 'string', 'max' => 20],
            [['from_address', 'notification_address', 'mail_to_description'], 'string', 'max' => 254],
            [['from_address', 'notification_address'], 'email'],
            // 掲載のお問い合わせメールの時は必須
            ['notification_address', 'required', 'on' => self::SCENARIO_INQUILY_NOTIFICATION],
        ];
    }

    /**
     * 要素のラベル名を設定。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'メール送信文面セットID'),
            'from_name' => Yii::t('app', '差出人名'),
            'from_address' => Yii::t('app', '差出人メールアドレス'),
            'subject' => Yii::t('app', '件名'),
            'contents' => Yii::t('app', 'メール文面'),
            'mail_sign' => Yii::t('app', '署名'),
            'mail_to' => Yii::t('app', 'メール受信者'),
            'valid_chk' => Yii::t('app', '状態'),
            'mail_name' => Yii::t('app', 'メール種別'),
            'mail_type_id' => Yii::t('app', 'メールのタイプ'),
            'notification_address' => Yii::t('app', '通知先メールアドレス'),
            'mail_to_description' => Yii::t('app', '受信対象メールアドレス'),
        ];
    }

    /**
     * メール送信先ラベル
     * @return array
     */
    public static function getMailToLabel()
    {
        return [
            self::MAIL_TO_APPLICATION => Yii::t('app', '求職者'),
            self::MAIL_TO_OWNER => Yii::t('app', '運営元'),
            self::MAIL_TO_CORP => Yii::t('app', '代理店'),
            self::MAIL_TO_CLIENT => Yii::t('app', '掲載企業')
        ];
    }

    /**
     * 置換文字ラベル
     * @return array
     */
    public static function needleLabels()
    {
        return [
            self::NEEDLE_ADMIN_NAME => Yii::t('app', '管理者氏名'),
            self::NEEDLE_ADMIN_SITE_URL => Yii::t('app', '管理画面URL'),
            self::NEEDLE_LOGIN_ID => Yii::t('app', '管理者ログインID'),
            self::NEEDLE_LOGIN_PASS => Yii::t('app', '管理者パスワード'),
            self::NEEDLE_JOB_URL => Yii::t('app', '求人詳細URL'),
            self::NEEDLE_SITE_URL => Yii::t('app', '求職者画面URL'),
            self::NEEDLE_SITE_NAME => Yii::t('app', 'サイト名'),
            self::NEEDLE_MEMBER_NAME => Yii::t('app', '登録者名'),
            self::NEEDLE_APPLICATION_NAME => Yii::t('app', '応募者名'),
            self::NEEDLE_ENTRY_HISTORY_URL => Yii::t('app', '応募履歴確認画面URL'),
            self::NEEDLE_APPLICATION_NO => Yii::$app->functionItemSet->application->items['application_no']->label,
            self::NEEDLE_PASS_SETTING_URL => Yii::t('app', 'パスワード再設定画面URL'),
            self::NEEDLE_REPRESENTATIVE_NAME => Yii::t('app', '担当者名'),
            self::NEEDLE_COMPANY_NAME => Yii::t('app', '会社名'),
            self::NEEDLE_JOB_ID => Yii::t('app', '求人ID'),
            self::NEEDLE_JOB_REVIEW_STATUS => Yii::t('app', '審査ステータス'),
            self::NEEDLE_JOB_REVIEW_COMMENT => Yii::t('app', '審査コメント'),
        ];
    }

    /**
     * @return array
     */
    public function getFormatTable()
    {
        return [
            'mail_to' => $this->getMailToLabel(),
        ];
    }

    /**
     * 項目別でSendUserに渡すpropertyを出力する
     * @return array
     * @throws Exception
     */
    public function getSendUserProperties()
    {
        switch ($this->mail_type_id) {
            case MailSend::TYPE_SEND_JOB:
                // 仕事送信メールでは入力されたアドレス宛でidは無し
                if ($this->model instanceof JobMasterDisp) {
                    return [['pc_mail_address' => $this->model->mailAddress]];
                }
                break;
            case MailSend::TYPE_ADMN_CREATE:
                // 管理者登録メール
                // 登録された管理者（admin_master.mail_address）宛でidはadmin_master.id
                if ($this->model instanceof AdminMaster) {
                    return [['pc_mail_address' => $this->model->mail_address, 'user_id' => $this->model->id]];
                }
                break;
            case MailSend::TYPE_APPLY_TO_APPLICATION:
                // 応募登録完了メール
                // 登録された応募者（application_master.mail_address）宛でidはapplication_master.id
                if ($this->model instanceof Apply) {
                    return [['pc_mail_address' => $this->model->mail_address, 'user_id' => $this->model->id]];
                }
                break;
            case MailSend::TYPE_APPLY_TO_ADMIN:
                // 応募登録通知メール
                // 仕事情報に登録されたアドレス（job_master.application_mail）宛でidはjob_master.id
                // 運営元（notification_address）宛でidは無し
                if ($this->model instanceof Apply) {
                    if ($this->notification_address) {
                        return [
                            [
                                'pc_mail_address' => $this->model->jobMaster->application_mail,
                                'user_id' => $this->model->job_master_id
                            ],
                            ['pc_mail_address' => $this->notification_address],
                        ];
                    } else {
                        return [
                            [
                                'pc_mail_address' => $this->model->jobMaster->application_mail,
                                'user_id' => $this->model->job_master_id
                            ]
                        ];
                    }
                }
                break;
            case MailSend::TYPE_MEMBERSHIP_TO_MEMBER:
                // 会員登録完了メール todo 会員機能実装次第調整
                // 登録された会員（member_master.mail_address）宛でidはmember_master.id
                if ($this->model instanceof MemberMaster) {
                    return [['pc_mail_address' => $this->model->mail_address, 'user_id' => $this->model->id]];
                }
                break;
            case MailSend::TYPE_MEMBERSHIP_TO_ADMIN:
                // 会員登録通知メール todo 会員機能実装次第調整
                // 運営元（site_master.application_mail_address）宛でidは無し
                if ($this->model instanceof MemberMaster) {
                    return [['pc_mail_address' => Yii::$app->site->info->application_mail_address]];
                }
                break;
            case MailSend::TYPE_MANAGE_PASS_RESET:
                // 管理者パスワード再設定メール
                // 再発行申請した管理者（admin_master.mail_address）宛でidはadmin_master.id
                if ($this->model instanceof AdminPasswordSetting) {
                    return [['pc_mail_address' => $this->model->mail_address, 'user_id' => $this->model->id]];
                }
                break;
            case MailSend::TYPE_MEMBER_PASS_RESET:
                // 会員パスワード再設定メール todo 会員機能実装次第調整
                // 再発行申請した会員（member_master.mail_address）宛でidはmember_master.id
                if ($this->model instanceof MemberMaster) {
                    return [['pc_mail_address' => $this->model->mail_address, 'user_id' => $this->model->id]];
                }
                break;
            case MailSend::TYPE_INQUILY_NOTIFICATION:
                if ($this->model instanceof InquiryMaster) {
                    return [
                        ['pc_mail_address' => $this->model->mail_address, 'user_id' => 0],
                        // todo primary keyにされてしまっているのでこの対応にするが、別メール化する
                        ['pc_mail_address' => $this->notification_address, 'user_id' => -1],
                    ];
                }
                break;
            case MailSend::TYPE_JOB_REVIEW:
                // 審査状況更新通知メール
                // 代理店管理者宛        ：idはadmin_master.id
                // 応募先メールアドレス宛：idはjob_master.id
                // 運営元（notification_address）宛でidは無し
                if ($this->model instanceof JobReview) {
                    return $this->notificationReview($this->model);
                }
                break;
            case MailSend::TYPE_JOB_REVIEW_COMPLETE:
                // 審査完了メール
                // idはjob_review.job_master_id
                if ($this->model instanceof JobReview) {
                    return $this->notificationJobMail($this->model->jobMaster);
                }
                break;
            default:
                throw new Exception('mail config is invalid');
        }
    }

    /**
     * 審査の通知先を返す
     * @param JobReview $jobReview
     * @return array
     * @throws Exception
     */
    private function notificationReview(JobReview $jobReview)
    {
        // 審査依頼／審査OK／審査NG
        $reviewReq = $jobReview->scenario === JobReview::SCENARIO_DEFAULT;
        $reviewOk = $jobReview->scenario === JobReview::SCENARIO_REVIEW && $jobReview->review;
        $reviewNg = $jobReview->scenario === JobReview::SCENARIO_REVIEW && !$jobReview->review;

        if ($reviewReq || $reviewOk) {
            // 審査OK、審査依頼の場合
            $users = $this->notificationReviewOk($jobReview);
        } elseif ($reviewNg) {
            // 審査NGの場合
            $users = $this->notificationReviewNg($jobReview);
        } else {
            // 念のための処理
            $users = $this->notNotification();
        }

        return $users;
    }

    /**
     * 審査OK／審査依頼時の通知先を返す
     * @param JobReview $jobReview
     * @return array
     */
    private function notificationReviewOk(JobReview $jobReview)
    {
        /** Manager $identity */
        $identity = Yii::$app->user->identity;
        if ($identity->isCorp()) {
            // 代理店管理者の場合
            // 運営元に通知する
            $users = $this->notificationSendMailSet();
        } elseif ($identity->isClient()) {
            // 掲載企業管理者の場合
            // 代理店審査あり／なしで通知先を振り分ける
            if ($jobReview->corpReviewFlg) {
                $users = $this->notificationCorpAdmin($jobReview->jobMaster->corpMasterId);
            } else {
                $users = $this->notificationSendMailSet();
            }
        } else {
            // 上記以外はどこにも通知しない
            $users = $this->notNotification();;
        }

        return $users;
    }

    /**
     * 審査NG時の通知先を返す
     * @param JobReview $jobReview
     * @return array
     */
    private function notificationReviewNg(JobReview $jobReview)
    {
        /** Manager $identity */
        $identity = Yii::$app->user->identity;
        if ($identity->isOwner()) {
            // 運営元管理者の場合
            // 応募先メールアドレスに通知する
            $users = $this->notificationJobMail($jobReview->jobMaster);

            // 代理店審査あり場合、代理店にも通知する
            if ($jobReview->corpReviewFlg) {
                $users = array_merge($users, $this->notificationCorpAdmin($jobReview->jobMaster->corpMasterId));
            }
        } elseif ($identity->isCorp()) {
            // 代理店管理者の場合
            // 応募先メールアドレスに通知する
            $users = $this->notificationJobMail($jobReview->jobMaster);
        } else {
            // 上記以外はどこにも通知しない
            $users = $this->notNotification();
        }

        return $users;
    }


    /**
     * 運営元への通知  ※SendMailSetに設定されているアドレスへの通知
     * @return array
     */
    private function notificationSendMailSet()
    {
        return [['pc_mail_address' => $this->notification_address ?? '']];
    }

    /**
     * 代理店管理者へ通知
     * @param integer $corpMasterId
     * @return array
     */
    private function notificationCorpAdmin($corpMasterId)
    {
        $users = [];
        $admins = AdminMaster::find()->addCorpAdminQuery($corpMasterId)->all();
        foreach ($admins as $admin) {
            /** @var AdminMaster $admin */
            $users[] = ['pc_mail_address' => $admin->mail_address ?? '', 'user_id' => $admin->id];
        }
        return $users;
    }

    /**
     * 原稿の応募先メールアドレスへ通知
     * @param JobMaster $jobMaster
     * @return array
     */
    private function notificationJobMail($jobMaster)
    {
        return [['pc_mail_address' => $jobMaster->application_mail ?? '', 'user_id' => $jobMaster->id]];
    }

    /**
     * 通知なし
     * @return array
     */
    private function notNotification()
    {
        return [['pc_mail_address' => '']];
    }

    /**
     * 置換されたメールタイトルを取得
     * @return mixed
     * @throws Exception
     */
    public function getReplacedTitle()
    {
        return $this->replace($this->subject);
    }

    /**
     * 置換されたメール本文を取得
     * @return mixed
     * @throws Exception
     */
    public function getReplacedBody()
    {
        if ($this->additionalText) {
            $this->additionalText .= "\n\n";
        }
        return $this->replace($this->contents)
        . "\n\n"
        . $this->additionalText
        . $this->replace($this->mail_sign);
    }

    /**
     * テキストを置換文字列で置換する
     * @param $text
     * @return mixed
     */
    private function replace($text)
    {
        return str_replace($this->needles, $this->replaces, $text);
    }

    /**
     * 置換用のneedleを取得
     * @return array
     */
    public function getNeedles()
    {
        if (!$this->_needles) {
            $this->_needles = array_merge(self::COMMON_NEEDLES, $this->optionNeedles);
        }
        return $this->_needles;
    }

    /**
     * 置換用のreplaceを取得
     * @return array
     * @throws Exception
     */
    public function getReplaces()
    {
        if (!$this->_replaces) {
            foreach ($this->needles as $needle) {
                $this->_replaces[] = $this->getReplace($needle);
            }
        }
        return $this->_replaces;
    }

    /**
     * 追加挿入テキストのgetter
     * @return string
     */
    public function getAdditionalText()
    {
        if (JmUtils::isEmpty($this->_additionalText) && $this->mail_type_id == MailSend::TYPE_APPLY_TO_APPLICATION) {
            $this->_additionalText = $this->model->jobMaster->mail_body;
        }
        return $this->_additionalText;
    }

    /**
     * 追加挿入テキストのsetter
     * @param $v
     */
    public function setAdditionalText($v)
    {
        $this->_additionalText = $v;
    }

    /**
     * 項目別の追加置換文字列を取得する
     * @return array
     * @throws Exception
     */
    public function getOptionNeedles()
    {
        switch ($this->mail_type_id) {
            // 仕事情報転送メールには仕事情報URL
            case MailSend::TYPE_SEND_JOB:
                return [self::NEEDLE_JOB_URL];
                break;
            // 管理者登録メールには管理者氏名、管理者ログインID、管理者ログインパスワード、管理者ログインページURL
            case MailSend::TYPE_ADMN_CREATE:
                return [
                    self::NEEDLE_ADMIN_NAME,
                    self::NEEDLE_LOGIN_ID,
                    self::NEEDLE_LOGIN_PASS,
                    self::NEEDLE_ADMIN_SITE_URL
                ];
                break;
            // 応募登録メール(応募者宛)には応募者氏名、応募No、応募確認画面URL、求人原稿詳細画面URL
            case MailSend::TYPE_APPLY_TO_APPLICATION:
                return [
                    self::NEEDLE_APPLICATION_NAME,
                    self::NEEDLE_APPLICATION_NO,
                    self::NEEDLE_ENTRY_HISTORY_URL,
                    self::NEEDLE_JOB_URL
                ];
                break;
            // 応募登録メール(管理者宛)には応募者氏名、応募No、応募確認画面URL、求人原稿詳細画面URL、管理者ログインページURL
            case MailSend::TYPE_APPLY_TO_ADMIN:
                return [
                    self::NEEDLE_APPLICATION_NAME,
                    self::NEEDLE_APPLICATION_NO,
                    self::NEEDLE_ENTRY_HISTORY_URL,
                    self::NEEDLE_JOB_URL,
                    self::NEEDLE_ADMIN_SITE_URL
                ];
                break;
            // 会員登録メールには登録者氏名
            case MailSend::TYPE_MEMBERSHIP_TO_MEMBER:
            case MailSend::TYPE_MEMBERSHIP_TO_ADMIN:
                return [self::NEEDLE_MEMBER_NAME];
                break;
            // 管理者パスワード再設定には管理者ログインID、管理者氏名、パスワード再設定画面URL、管理者ログインページURL
            case MailSend::TYPE_MANAGE_PASS_RESET:
                return [
                    self::NEEDLE_LOGIN_ID,
                    self::NEEDLE_ADMIN_NAME,
                    self::NEEDLE_PASS_SETTING_URL,
                    self::NEEDLE_ADMIN_SITE_URL
                ];
                break;
            // 会員パスワード再設定には会員ログインID、会員ログインパスワード、会員氏名
            case MailSend::TYPE_MEMBER_PASS_RESET:
                return [
                    self::NEEDLE_LOGIN_ID,
                    self::NEEDLE_LOGIN_PASS,
                    self::NEEDLE_MEMBER_NAME,
                    self::NEEDLE_PASS_SETTING_URL
                ];
                break;
            case MailSend::TYPE_INQUILY_NOTIFICATION:
                return [
                    self::NEEDLE_COMPANY_NAME,
                    self::NEEDLE_REPRESENTATIVE_NAME,
                ];
                break;
            // 審査には更新後審査ステータス、審査コメント
            case MailSend::TYPE_JOB_REVIEW:
            case MailSend::TYPE_JOB_REVIEW_COMPLETE:
                return [
                    self::NEEDLE_ADMIN_SITE_URL,
                    self::NEEDLE_JOB_ID,
                    self::NEEDLE_JOB_REVIEW_STATUS,
                    self::NEEDLE_JOB_REVIEW_COMMENT,
                ];
                break;
            default:
                break;
        }
        throw new Exception('mail config is invalid');
    }

    /**
     * needleをもとにreplaceを取得する
     * @param $needle
     * @return string
     * @throws Exception
     */
    private function getReplace($needle)
    {
        switch ($needle) {
            case self::NEEDLE_ADMIN_NAME:
                if ($this->model instanceof AdminMaster) {
                    return $this->model->fullName;
                }
                break;
            case self::NEEDLE_ADMIN_SITE_URL:
                return Url::toRoute('/manage/login/', true);
                break;
            case self::NEEDLE_LOGIN_ID:
                if ($this->model instanceof AdminMaster || $this->model instanceof MemberMaster) {
                    return $this->model->login_id;
                }
                break;
            case self::NEEDLE_LOGIN_PASS:
                if ($this->model instanceof AdminMaster || $this->model instanceof MemberMaster) {
                    return $this->model->password;
                }
                break;
            case self::NEEDLE_JOB_URL:
                if ($this->model instanceof JobMaster) {
                    return Url::toRoute('/' . Yii::$app->tenant->tenant->kyujin_detail_dir . '/' . $this->model->job_no, true);
                } elseif ($this->model instanceof Apply) {
                    return Url::toRoute('/' . Yii::$app->tenant->tenant->kyujin_detail_dir . '/' . $this->model->jobMaster->job_no, true);
                }
                break;
            case self::NEEDLE_SITE_URL:
                return Url::toRoute('/', true);
                break;
            case self::NEEDLE_SITE_NAME:
                return Yii::$app->site->info->site_name;
                break;
            case self::NEEDLE_MEMBER_NAME:
                if ($this->model instanceof MemberMaster) {
                    return $this->model->fullName;
                }
                break;
            case self::NEEDLE_APPLICATION_NAME:
                if ($this->model instanceof ApplicationMaster) {
                    return $this->model->fullName;
                }
                break;
            case self::NEEDLE_ENTRY_HISTORY_URL:
                return Url::toRoute('/mypage/entry-history', true);
                break;
            case self::NEEDLE_APPLICATION_NO:
                if ($this->model instanceof ApplicationMaster) {
                    return $this->model->application_no;
                }
                break;
            case self::NEEDLE_PASS_SETTING_URL:
                if ($this->model instanceof AdminPasswordSetting) {
                    return Url::toRoute(['/pass/entry', 'key' => $this->model->passwordReminder->collation_key], true);
                }
                break;
            case self::NEEDLE_COMPANY_NAME:
                if ($this->model instanceof InquiryMaster) {
                    return $this->model->company_name;
                }
                break;
            case self::NEEDLE_REPRESENTATIVE_NAME:
                if ($this->model instanceof InquiryMaster) {
                    return $this->model->tanto_name;
                }
                break;
            case self::NEEDLE_JOB_ID:
                if ($this->model instanceof JobReview) {
                    return $this->model->jobMaster->job_no;
                }
                break;
            case self::NEEDLE_JOB_REVIEW_STATUS:
                if ($this->model instanceof JobReview) {
                    return $this->model->jobReviewStatus->name;
                }
                break;
            case self::NEEDLE_JOB_REVIEW_COMMENT:
                if ($this->model instanceof JobReview) {
                    return $this->model->comment;
                }
                break;
            default:
                break;
        }
        throw new Exception('mail config is invalid');
    }

    /**
     * メール送信に関連するidを出力する
     * @return int
     * @throws Exception
     */
    public function getEntityId()
    {
        switch ($this->mail_type_id) {
            case MailSend::TYPE_SEND_JOB:
                // 仕事送信メールではjob_master.id
                if ($this->model instanceof JobMasterDisp) {
                    return $this->model->id;
                }
                break;
            case MailSend::TYPE_APPLY_TO_APPLICATION:
                // 応募登録完了メール（応募者宛）ではjob_master.id
                if ($this->model instanceof Apply) {
                    return $this->model->job_master_id;
                }
                break;
            case MailSend::TYPE_APPLY_TO_ADMIN:
                // 応募登録通知メール（管理者宛）ではapplication_master.id
                if ($this->model instanceof Apply) {
                    return $this->model->id;
                }
                break;
            case MailSend::TYPE_MEMBERSHIP_TO_MEMBER:
                // 会員登録完了メール todo 会員機能実装次第調整
                if ($this->model instanceof MemberMaster) {
                    return 0;
                }
                break;
            case MailSend::TYPE_MEMBERSHIP_TO_ADMIN:
                // 会員登録通知メール todo 会員機能実装次第調整
                if ($this->model instanceof MemberMaster) {
                    return 0;
                }
                break;
            case MailSend::TYPE_ADMN_CREATE:
            case MailSend::TYPE_MANAGE_PASS_RESET:
                // 管理者登録メールと管理者パスワード再設定メールでは関連id無し
                if ($this->model instanceof AdminMaster) {
                    return 0;
                }
                break;
            case MailSend::TYPE_MEMBER_PASS_RESET:
                // 会員パスワード再設定メール todo 会員機能実装次第調整
                if ($this->model instanceof MemberMaster) {
                    return 0;
                }
                break;
            case MailSend::TYPE_INQUILY_NOTIFICATION:
                if ($this->model instanceof InquiryMaster) {
                    return 0;
                }
                break;
            case MailSend::TYPE_JOB_REVIEW:
            case MailSend::TYPE_JOB_REVIEW_COMPLETE:
                // 審査系メールではjob_review_history.job_master_id
                if ($this->model instanceof JobReview) {
                    return $this->model->job_master_id;
                }
                break;
            default:
                break;
        }
        throw new Exception('mail config is invalid');
    }
}
