<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_job_master_backup`.
 */
class m160505_070129_create_table_job_master_backup extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS job_master_backup');
        $this->createTable('job_master_backup', [
            'id' => $this->integer(11)->notNull() . ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull() . ' COMMENT "テナントID"',
            'job_no' => $this->integer(11)->notNull() . '  COMMENT "仕事ナンバー"',
            'client_master_id' => $this->integer(11)->notNull() . ' COMMENT "テーブルclient_masterのカラムid"',
            'corp_name_disp' => $this->string(255) . ' COMMENT "会社名"',
            'job_pr' => $this->text() . ' COMMENT "メインキャッチ"',
            'main_copy' => $this->text() . ' COMMENT "コメント"',
            'job_comment' => $this->text() . ' COMMENT "PR"',
            'job_pict_0' => $this->string(255) . ' COMMENT "画像０（Aタイプ）"',
            'job_pict_1' => $this->string(255) . ' COMMENT "画像１（Bタイプ）"',
            'job_pict_2' => $this->string(255) . ' COMMENT "画像２（Cタイプ）"',
            'job_pict_3' => $this->string(255) . ' COMMENT "画像３（Cタイプ）"',
            'job_type_text' => $this->text() . ' COMMENT "職種（テキスト）"',
            'work_place' => $this->text() . ' COMMENT "勤務地（テキスト）"',
            'station' => $this->text() . ' COMMENT "最寄り駅"',
            'transport' => $this->text() . ' COMMENT "交通"',
            'wage_text' => $this->text() . ' COMMENT "給与"',
            'requirement' => $this->text() . ' COMMENT "応募資格"',
            'conditions' => $this->text() . ' COMMENT "待遇"',
            'holidays' => $this->text() . ' COMMENT "休日・休暇"',
            'work_period' => $this->text() . ' COMMENT "就労期間"',
            'work_time_text' => $this->text() . ' COMMENT "勤務期間（テキスト）"',
            'application' => $this->text() . ' COMMENT "応募方法"',
            'application_tel_1' => $this->string(255) . ' COMMENT "連絡先電話番号１"',
            'application_tel_2' => $this->string(255) . ' COMMENT "連絡先電話番号２"',
            'application_mail' => $this->string(255) . ' COMMENT "応募先メールアドレス"',
            'application_place' => $this->text() . ' COMMENT "面接地"',
            'application_staff_name' => $this->string(255) . ' COMMENT "受付担当者"',
            'agent_name' => $this->string(255) . ' COMMENT "営業担当者"',
            'disp_start_date' => $this->integer(11)->notNull() . ' COMMENT "掲載開始日"',
            'disp_end_date' => $this->integer(11) . ' COMMENT "掲載終了日"',
            'created_at' => $this->integer(11) . ' COMMENT "登録日時"',
            'valid_chk' => $this->boolean()->notNull()->defaultValue(1) . ' COMMENT "状態"',
            'job_search_number' => $this->string(255) . ' COMMENT "お仕事No"',
            'job_pict_text_2' => $this->text() . ' COMMENT "画像２（キャプション）"',
            'job_pict_text_3' => $this->text() . ' COMMENT "画像３（キャプション）"',
            'map_url' => $this->text() . ' COMMENT "MAPをみる-URL"',
            'mail_body' => $this->text() . ' COMMENT "通知メール文面"',
            'updated_at' => $this->integer(11) . ' COMMENT "更新日時"',
            'job_pict_text_4' => $this->text() . ' COMMENT "画像４（キャプション）"',
            'job_pict_4' => $this->string(255) . ' COMMENT "画像４（Cタイプ）"',
            'main_copy2' => $this->text() . ' COMMENT "コメント2"',
            'job_pr2' => $this->text() . ' COMMENT "メインキャッチ2"',
            'option100' => $this->text() . ' COMMENT "オプション100"',
            'option101' => $this->text() . ' COMMENT "オプション101"',
            'option102' => $this->text() . ' COMMENT "オプション102"',
            'option103' => $this->text() . ' COMMENT "オプション103"',
            'option104' => $this->text() . ' COMMENT "オプション104"',
            'option105' => $this->text() . ' COMMENT "オプション105"',
            'option106' => $this->text() . ' COMMENT "オプション106"',
            'option107' => $this->text() . ' COMMENT "オプション107"',
            'option108' => $this->text() . ' COMMENT "オプション108"',
            'option109' => $this->text() . ' COMMENT "オプション109"',
            'import_site_job_id' => $this->integer(11) . ' COMMENT "インポートサイト仕事ID"',
            'client_charge_plan_id' => $this->smallInteger(6)->notNull() . ' COMMENT "テーブルclient_charge_planのカラムid"',
            'medium_application_pc_url' => $this->text() . ' COMMENT "応募媒体URL（PC)"',
            'medium_application_sm_url' => $this->text() . ' COMMENT "応募媒体URL（スマホ)"',
            'manager_memo' => $this->text() . ' COMMENT "管理者用備考欄"',
            'review_flg' => 'TINYINT DEFAULT 3 COMMENT "審査フラグ(0=改変、1=審査依頼中、2=審査NG、3=審査OK)"',
            'sample_pict_flg_1' => $this->boolean()->defaultValue(0) . ' COMMENT "サンプル画像フラグ1"',
            'sample_pict_flg_2' => $this->boolean()->defaultValue(0) . ' COMMENT "サンプル画像フラグ2"',
            'sample_pict_flg_3' => $this->boolean()->defaultValue(0) . ' COMMENT "サンプル画像フラグ3"',
            'sample_pict_flg_4' => $this->boolean()->defaultValue(0) . ' COMMENT "サンプル画像フラグ4"',
            'sample_pict_flg_5' => $this->boolean()->defaultValue(0) . ' COMMENT "サンプル画像フラグ5"',
            'oiwai_price' => $this->integer(11) . ' COMMENT "お祝い金額"',
            'deleted_at' => $this->integer(11)->notNull() . ' COMMENT "削除日時"',
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="求人情報完全削除前バックアップ"');
        $this->addPrimaryKey('pk_job_master_backup', 'job_master_backup', ['id', 'tenant_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('job_master_backup');
    }
}
