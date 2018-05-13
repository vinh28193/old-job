<?php
use yii\db\Query;
use yii\db\Migration;
use proseeds\models\Tenant;
use app\models\manage\InquiryColumnSet;

class m161102_023601_insert_into_inquiry_column_set extends Migration
{
    const VALID = 1;
    private $records = [
        [
            'column_no' => 1,
            'column_name' => 'company_name',
            'label' => '企業名',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 1,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 2,
            'column_name' => 'post_name',
            'label' => 'ご担当者部署名',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 3,
            'column_name' => 'tanto_name',
            'label' => 'ご担当者名',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 1,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 4,
            'column_name' => 'job_type',
            'label' => '職種',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 1,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 5,
            'column_name' => 'postal_code',
            'label' => '郵便番号',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 8,
            'is_must' => 1,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 6,
            'column_name' => 'address',
            'label' => 'ご住所',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 1,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 7,
            'column_name' => 'tel_no',
            'label' => 'ご連絡先電話番号',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 1,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 8,
            'column_name' => 'fax_no',
            'label' => 'ご連絡先FAX番号',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 9,
            'column_name' => 'mail_address',
            'label' => 'ご連絡先メールアドレス',
            'data_type' => InquiryColumnSet::DATA_TYPE_MAIL,
            'max_length' => 254,
            'is_must' => null,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 10,
            'column_name' => 'option100',
            'label' => 'お問い合わせ内容',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 1000,
            'is_must' => 1,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 11,
            'column_name' => 'option101',
            'label' => '募集内容',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 500,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 12,
            'column_name' => 'option102',
            'label' => 'その他、ご質問など',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 500,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 13,
            'column_name' => 'option103',
            'label' => '当サイトをどこでお知りになりましたか',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 100,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 14,
            'column_name' => 'option104',
            'label' => 'オプション4',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 500,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 15,
            'column_name' => 'option105',
            'label' => 'オプション5',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 16,
            'column_name' => 'option106',
            'label' => 'オプション6',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 2000,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 17,
            'column_name' => 'option107',
            'label' => 'オプション7',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 18,
            'column_name' => 'option108',
            'label' => 'オプション8',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 60,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ],
        [
            'column_no' => 19,
            'column_name' => 'option109',
            'label' => 'オプション9',
            'data_type' => InquiryColumnSet::DATA_TYPE_TEXT,
            'max_length' => 200,
            'is_must' => 0,
            'is_in_list' => null,
            'is_in_search' => null,
        ]
    ];

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->truncateTable(InquiryColumnSet::tableName());
        $tenants = (new Query)
            ->select('tenant_id')
            ->from(Tenant::tableName())
            ->all();
        foreach ($tenants as $tenant) {
            foreach ($this->records as $row) {
                $this->insert(InquiryColumnSet::tableName(), [
                    'tenant_id' => $tenant['tenant_id'],
                    'column_no' => $row['column_no'],
                    'column_name' => $row['column_name'],
                    'label' => $row['label'],
                    'data_type' => $row['data_type'],
                    'max_length' => $row['max_length'],
                    'is_must' => $row['is_must'],
                    'is_in_list' => $row['is_in_list'],
                    'is_in_search' => $row['is_in_search'],
                    'valid_chk' => self::VALID,
                ]);
            }
        }
    }

    public function safeDown()
    {
        $this->truncateTable(InquiryColumnSet::tableName());
    }
}
