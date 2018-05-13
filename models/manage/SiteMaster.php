<?php

namespace app\models\manage;

use Yii;
use proseeds\models\BaseModel;
/**
 * This is the model class for table "site_master".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $site_master_id
 * @property string $site_name
 * @property string $company_name
 * @property string $tanto_name
 * @property string $support_tel_no
 * @property string $site_url
 * @property string $site_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $support_mail_name
 * @property string $support_mail_address
 * @property string $support_mail_subject
 * @property string $application_mail_name
 * @property string $application_mail_address
 * @property string $application_mail_subject
 * @property string $regist_mail_name
 * @property string $regist_mail_address
 * @property string $regist_mail_subject
 * @property string $password_mail_name
 * @property string $password_mail_address
 * @property string $password_mail_subject
 * @property string $expo_mail_name
 * @property string $expo_mail_address
 * @property string $expo_mail_subject
 * @property string $job_mail_name
 * @property string $job_mail_address
 * @property string $job_mail_subject
 * @property string $friend_mail_name
 * @property string $friend_mail_address
 * @property string $friend_mail_subject
 * @property string $mail_sign
 * @property integer $review_required
 * @property string $review_mail_name
 * @property string $review_mail_address
 * @property string $review_mail_subject
 * @property integer $application_required
 * @property integer $webmail_required
 * @property integer $scout_use
 * @property integer $member_use
 * @property string $smart_site_title
 * @property string $smart_meta_description
 * @property string $smart_meta_keywords
 * @property integer $adoption_reminder_day
 * @property integer $auto_adoption_day
 * @property integer $area_pref_flg
 * @property integer $encryption_flg
 * @property integer $login_ssl_required
 * @property integer $alert_job_num_flg
 * @property integer $alert_job_num_limit
 */
// TODO:取り急ぎの作成なので、修正が必要。
class SiteMaster extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'site_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'site_master_id', 'site_name', 'company_name', 'tanto_name', 'support_tel_no', 'site_url', 'site_title', 'meta_description', 'meta_keywords', 'support_mail_name', 'support_mail_subject', 'application_mail_name', 'application_mail_subject', 'regist_mail_name', 'regist_mail_subject', 'password_mail_name', 'password_mail_subject', 'expo_mail_name', 'expo_mail_subject', 'job_mail_name', 'job_mail_subject', 'friend_mail_name', 'friend_mail_subject', 'mail_sign', 'review_mail_name', 'review_mail_subject', 'smart_site_title', 'smart_meta_description', 'smart_meta_keywords'], 'required'],
            [['tenant_id', 'site_master_id', 'review_required', 'application_required', 'webmail_required', 'scout_use', 'member_use', 'auto_admit_required', 'adoption_reminder_day', 'auto_adoption_day', 'auto_admit_day', 'area_pref_flg', 'medium_application_flg', 'encryption_flg', 'login_ssl_required', 'alert_job_num_flg', 'alert_job_num_limit'], 'integer'],
            [['mail_sign'], 'string'],
            [['site_name', 'company_name', 'tanto_name', 'support_tel_no', 'site_url', 'site_title', 'meta_description', 'meta_keywords', 'support_mail_name', 'support_mail_subject', 'application_mail_name', 'application_mail_subject', 'regist_mail_name', 'regist_mail_subject', 'password_mail_name', 'password_mail_subject', 'expo_mail_name', 'expo_mail_subject', 'job_mail_name', 'job_mail_subject', 'friend_mail_name', 'friend_mail_subject', 'review_mail_name', 'review_mail_subject', 'smart_site_title', 'smart_meta_description', 'smart_meta_keywords'], 'string', 'max' => 255],
            [['support_mail_address', 'application_mail_address', 'regist_mail_address', 'password_mail_address', 'expo_mail_address', 'job_mail_address', 'friend_mail_address', 'review_mail_address'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tenant_id' => 'Tenant ID',
            'site_master_id' => 'Site Master ID',
            'site_name' => 'Site Name',
            'company_name' => 'Company Name',
            'tanto_name' => 'Tanto Name',
            'support_tel_no' => 'Support Tel No',
            'site_url' => 'Site Url',
            'site_title' => 'Site Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'support_mail_name' => 'Support Mail Name',
            'support_mail_address' => 'Support Mail Address',
            'support_mail_subject' => 'Support Mail Subject',
            'application_mail_name' => 'Application Mail Name',
            'application_mail_address' => 'Application Mail Address',
            'application_mail_subject' => 'Application Mail Subject',
            'regist_mail_name' => 'Regist Mail Name',
            'regist_mail_address' => 'Regist Mail Address',
            'regist_mail_subject' => 'Regist Mail Subject',
            'password_mail_name' => 'Password Mail Name',
            'password_mail_address' => 'Password Mail Address',
            'password_mail_subject' => 'Password Mail Subject',
            'expo_mail_name' => 'Expo Mail Name',
            'expo_mail_address' => 'Expo Mail Address',
            'expo_mail_subject' => 'Expo Mail Subject',
            'job_mail_name' => 'Job Mail Name',
            'job_mail_address' => 'Job Mail Address',
            'job_mail_subject' => 'Job Mail Subject',
            'friend_mail_name' => 'Friend Mail Name',
            'friend_mail_address' => 'Friend Mail Address',
            'friend_mail_subject' => 'Friend Mail Subject',
            'mail_sign' => 'Mail Sign',
            'review_required' => 'Review Required',
            'review_mail_name' => 'Review Mail Name',
            'review_mail_address' => 'Review Mail Address',
            'review_mail_subject' => 'Review Mail Subject',
            'application_required' => 'Application Required',
            'webmail_required' => 'Webmail Required',
            'scout_use' => 'Scout Use',
            'member_use' => 'Member Use',
            'smart_site_title' => 'Smart Site Title',
            'smart_meta_description' => 'Smart Meta Description',
            'smart_meta_keywords' => 'Smart Meta Keywords',
            'auto_admit_required' => 'Auto Admit Required',
            'adoption_reminder_day' => 'Adoption Reminder Day',
            'auto_adoption_day' => 'Auto Adoption Day',
            'auto_admit_day' => 'Auto Admit Day',
            'area_pref_flg' => 'Area Pref Flg',
            'medium_application_flg' => 'Medium Application Flg',
            'encryption_flg' => 'Encryption Flg',
            'login_ssl_required' => 'Login Ssl Required',
            'alert_job_num_flg' => 'Alert Job Num Flg',
            'alert_job_num_limit' => 'Alert Job Num Limit',
        ];
    }
}
