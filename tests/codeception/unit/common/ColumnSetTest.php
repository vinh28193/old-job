<?php


use app\common\ColumnSet;
use app\common\Site;
use app\models\manage\ApplicationColumnSet;
use app\models\manage\BaseColumnSet;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

class ColumnSetTest extends JmTestCase
{
    public $rules;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var ColumnSet[] */
    private $columnSets;

    const MENUS = [
        'corp',
        'admin',
        'client',
        'application',
//        'member',
        'job',
        'inquiry',
    ];

    const MASTER_MODEL_NAMES = [
        'corp' => 'app\models\manage\CorpColumnSet',
        'admin' => 'app\models\manage\AdminColumnSet',
        'client' => 'app\models\manage\ClientColumnSet',
        'application' => 'app\models\manage\ApplicationColumnSet',
//        'member' => 'app\models\manage\MemberColumnSet',
        'job' => 'app\models\manage\JobColumnSet',
        'inquiry' => 'app\models\manage\InquiryColumnSet',
    ];

    protected function setUp()
    {
        parent::setUp();
        foreach (self::MENUS as $menu) {
            $this->columnSets[$menu] = Yii::$app->functionItemSet->$menu;
        }
    }

    /**
     * todo column_setのtest recordにdata_typeチェックボックスでsubsetを持たないものを用意する
     */
    public function testGetItems()
    {
        foreach (self::MENUS as $menu) {
            verify($this->columnSets[$menu]->items)->notEmpty();
            foreach ($this->columnSets[$menu]->items as $item) {
                /* @var BaseColumnSet $item */
                verify($item->valid_chk)->equals(1);
                verify($item->column_name)->notEmpty();
                if ($menu == 'job') {
                    verify($item->isRelationPopulated('mainDisps'))->true();
                    verify($item->isRelationPopulated('listDisps'))->true();
                }
                if (strpos($item->column_name, 'option') !== false && ($item->data_type == BaseColumnSet::DATA_TYPE_CHECK || $item->data_type == BaseColumnSet::DATA_TYPE_RADIO)) {
                    verify($item->subsetItems)->notEmpty();
                }
            }
        }
    }

    public function testGetOptionItems()
    {
        foreach (self::MENUS as $menu) {
            verify($this->columnSets[$menu]->optionItems)->notEmpty();
            foreach ($this->columnSets[$menu]->optionItems as $item) {
                /* @var BaseColumnSet $item */
                verify($item->column_name)->contains('option');
            }
        }
    }

    public function testGetDefaultItems()
    {
        foreach (self::MENUS as $menu) {
            verify($this->columnSets[$menu]->defaultItems)->notEmpty();
            foreach ($this->columnSets[$menu]->defaultItems as $item) {
                /* @var BaseColumnSet $item */
                verify($item->column_name)->notContains('option');
            }
        }
    }

    public function testGetSearchMenuItems()
    {
        foreach (self::MENUS as $menu) {
            if ($menu == 'inquiry') {
                continue;
            }
            verify($this->columnSets[$menu]->searchMenuItems)->notEmpty();
            foreach ($this->columnSets[$menu]->searchMenuItems as $item) {
                /* @var BaseColumnSet $item */
                verify($item->is_in_search)->equals(1);
            }
        }
    }

    public function testGetListItems()
    {
        foreach (self::MENUS as $menu) {
            if ($menu == 'inquiry') {
                continue;
            }
            verify($this->columnSets[$menu]->listItems)->notEmpty();
            foreach ($this->columnSets[$menu]->listItems as $item) {
                /* @var BaseColumnSet $item */
                verify($item->is_in_list)->equals(BaseColumnSet::IN_LIST);
            }
        }
    }

    public function testGetAttributes()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->items as $key => $items) {
                /* @var BaseColumnSet $item */
                verify($this->columnSets[$menu]->attributes[$key])->equals($items->column_name);
            }
        }
    }

    public function testGetAttributeLabels()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->items as $key => $items) {
                /* @var BaseColumnSet $item */
                verify($this->columnSets[$menu]->attributeLabels[$key])->equals($items->label);
            }
        }
    }

    public function testGetOptionAttributes()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->optionItems as $key => $items) {
                verify($this->columnSets[$menu]->optionAttributes[$key])->equals($items->column_name);
            }
        }
    }

    public function testGetOptionAttributeLabels()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->optionItems as $key => $items) {
                verify($this->columnSets[$menu]->optionAttributeLabels[$key])->equals($items->label);
            }
        }
    }

    public function testGetSearchMenuAttributes()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->searchMenuItems as $key => $items) {
                verify($this->columnSets[$menu]->searchMenuAttributes[$key])->equals($items->column_name);
            }
        }
    }

    public function testGetSearchMenuAttributeLabels()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->searchMenuItems as $key => $items) {
                verify($this->columnSets[$menu]->searchMenuAttributeLabels[$key])->equals($items->label);
            }
        }
    }

    /**
     * getSearchableByKeyWordのtest
     * jobのfullNameとapplicationのfullNameとfullNameKanaが有効かつキーワード検索項目じゃないと通りません
     */
    public function testGetSearchableByKeyWord()
    {
        foreach (self::MENUS as $menu) {
            $searchMenuAttributes = $this->columnSets[$menu]->searchMenuAttributes;
            // columnにはないattributeが検索項目に含まれていることを検証
            if ($menu == 'application') {
                verify($searchMenuAttributes)->contains('fullName');
                verify($searchMenuAttributes)->contains('fullNameKana');
            } elseif ($menu == 'admin') {
                verify($searchMenuAttributes)->contains('fullName');
            } else {
                continue;
            }
            // columnにはないattributeが検索カラムには無いことを検証
            verify($this->columnSets[$menu]->searchableByKeyWord)->notEmpty();
            verify($this->columnSets[$menu]->searchableByKeyWord)->notContains('fullName');
            verify($this->columnSets[$menu]->searchableByKeyWord)->notContains('fullNameKana');
        }
    }

    public function testGetListAttributes()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->listItems as $key => $items) {
                verify($this->columnSets[$menu]->listAttributes[$key])->equals($items->column_name);
            }
        }
    }

    // todo 使われていないので削除検討
    public function testGetListAttributeLabels()
    {
        foreach (self::MENUS as $menu) {
            foreach ($this->columnSets[$menu]->listItems as $key => $items) {
                verify($this->columnSets[$menu]->listAttributeLabels[$key])->equals($items->label);
            }
        }
    }

    /**
     * getRulesのtest(job)
     * 各column_set、main_disp、list_dispのデータに強く依存します
     */
    public function testGetJobRules()
    {
        self::getFixtureInstance('admin_column_set')->load();
        self::getFixtureInstance('application_column_set')->load();
        self::getFixtureInstance('client_column_set')->load();
        self::getFixtureInstance('corp_column_set')->load();
        self::getFixtureInstance('inquiry_column_set')->load();
        self::getFixtureInstance('job_column_set')->load();

        $this->rules = Yii::$app->functionItemSet->job->rules;

        verify($this->isNum('job_no', null))->true();
        verify($this->isRequired('job_no'))->false();

        verify($this->isText('job_search_number', 200))->true();
        $this->isRequiredOn('job_search_number', []);

        verify($this->filterRules($this->rules, 'corpLabel'))->isEmpty();

        verify($this->isNum('client_charge_plan_id', null))->true();
        verify($this->isRequired('client_charge_plan_id'))->false();

        verify($this->isNum('client_master_id', null))->true();
        verify($this->isRequired('client_master_id'))->false();

        verify($this->filterRules($this->rules, 'disp_start_date'))->isEmpty();
        verify($this->filterRules($this->rules, 'disp_end_date'))->isEmpty();

        verify($this->isText('corp_name_disp', 50))->true();
        $this->isRequiredOn('corp_name_disp', [1, 2, 3]);

        verify($this->isText('job_pr', 200))->true();
        $this->isRequiredOn('job_pr', []);

        verify($this->isText('main_copy', 1000))->true();
        $this->isRequiredOn('main_copy', []);

        verify($this->isText('job_pr2', 500))->true();
        $this->isRequiredOn('job_pr2', []);

        verify($this->isText('main_copy2', 500))->true();
        $this->isRequiredOn('main_copy2', []);

        verify($this->isText('job_type_text', 100))->true();
        $this->isRequiredOn('job_type_text', []);

        verify($this->isText('work_place', 500))->true();
        $this->isRequiredOn('work_place', []);

        verify($this->isText('station', 200))->true();
        $this->isRequiredOn('station', []);

        verify($this->isUrl('map_url', 2000))->true();
        $this->isRequiredOn('map_url', []);

        verify($this->isNum('wage_text', 300))->true();
        $this->isRequiredOn('wage_text', []);

        verify($this->isText('transport', 60))->true();
        $this->isRequiredOn('transport', []);

        verify($this->isText('transport', 60))->true();
        $this->isRequiredOn('transport', []);

        verify($this->isText('work_period', 200))->true();
        $this->isRequiredOn('work_period', []);

        verify($this->isText('work_time_text', 60))->true();
        $this->isRequiredOn('work_time_text', []);

        verify($this->isText('requirement', 500))->true();
        $this->isRequiredOn('requirement', []);

        verify($this->isText('conditions', 500))->true();
        $this->isRequiredOn('conditions', []);

        verify($this->isText('holidays', 500))->true();
        $this->isRequiredOn('holidays', []);

        verify($this->isText('job_comment', 500))->true();
        $this->isRequiredOn('job_commentt', []);

        verify($this->isText('application', 500))->true();
        $this->isRequiredOn('application', []);

        verify($this->isTelNum('application_tel_1'))->true();
        verify($this->isRequired('application_tel_1'))->false();

        verify($this->isTelNum('application_tel_2'))->true();
        verify($this->isRequired('application_tel_2'))->false();

        verify($this->isText('application_place', 60))->true();
        $this->isRequiredOn('application_place', []);

        verify($this->isText('application_staff_name', 60))->true();
        $this->isRequiredOn('application_staff_name', []);

        verify($this->isMail('application_mail', 254))->true();
        verify($this->isRequired('application_mail'))->false();

        verify($this->isText('agent_name', 60))->true();
        verify($this->isRequired('agent_name'))->false();

        verify($this->isText('mail_body', 2000))->true();
        verify($this->isRequired('mail_body'))->false();

        verify($this->isText('option100', 500))->true();
        $this->isRequiredOn('option100', []);

        verify($this->isUrl('option101', 2000))->true();
        $this->isRequiredOn('option101', []);

        verify($this->isMail('option102', 254))->true();
        $this->isRequiredOn('option102', []);

        verify($this->isSafe('option103'))->true();
        $this->isRequiredOn('option103', []);

        verify($this->isSafe('option104'))->true();
        $this->isRequiredOn('option104', []);

        verify($this->filterRules($this->rules, 'option105'))->isEmpty();

        verify($this->filterRules($this->rules, 'option106'))->isEmpty();

        verify($this->filterRules($this->rules, 'option107'))->isEmpty();

        verify($this->filterRules($this->rules, 'option108'))->isEmpty();

        verify($this->filterRules($this->rules, 'option109'))->isEmpty();

    }

    public function testGetCorpRules()
    {
        $this->rules = Yii::$app->functionItemSet->corp->rules;

        verify($this->isNum('corp_no', null))->true();
        verify($this->isRequired('corp_no'))->false();

        verify($this->isText('corp_name', 200))->true();
        $this->isRequiredOn('corp_name', []);

        verify($this->isTelNum('tel_no'))->true();
        verify($this->isRequired('tel_no'))->false();

        verify($this->isText('tanto_name', 200))->true();
        $this->isRequiredOn('tanto_name', []);

        verify($this->isText('option100', 500))->true();
        verify($this->isRequired('option100'))->true();

        verify($this->isNum('option101', 5))->true();
        $this->isRequiredOn('option101', []);

        verify($this->isMail('option102', 254))->true();
        verify($this->isRequired('option102'))->true();

        verify($this->filterRules($this->rules, 'option103'))->isEmpty();

        verify($this->filterRules($this->rules, 'option104'))->isEmpty();

        verify($this->isText('option105', 200))->true();
        $this->isRequiredOn('option105', []);

        verify($this->filterRules($this->rules, 'option106'))->isEmpty();

        verify($this->filterRules($this->rules, 'option107'))->isEmpty();

        verify($this->filterRules($this->rules, 'option108'))->isEmpty();

        verify($this->isMail('option109', 254))->true();
        verify($this->isRequired('option109'))->true();
    }

    public function testGetClientRules()
    {
        $this->rules = Yii::$app->functionItemSet->client->rules;

        verify($this->isNum('client_no', null))->true();
        verify($this->isRequired('client_no'))->false();

        verify($this->isNum('corp_master_id', null))->true();
        verify($this->isRequired('corp_master_id'))->false();

        verify($this->isText('client_name', 200))->true();
        verify($this->isRequired('client_name'))->false();

        verify($this->isText('client_name_kana', 200))->true();
        $this->isRequiredOn('client_name_kana', []);

        verify($this->isText('address', 200))->true();
        $this->isRequiredOn('address', []);

        verify($this->isText('tanto_name', 200))->true();
        $this->isRequiredOn('tanto_name', []);

//        verify($this->isNum('tel_no', 30))->true();
//        $this->isRequiredOn('tel_no', []);

        verify($this->isText('client_business_outline', 200))->true();
        $this->isRequiredOn('client_business_outline', []);

        verify($this->isUrl('client_corporate_url', 2000))->true();
        $this->isRequiredOn('client_corporate_url', []);

        verify($this->isText('option100', 30))->true();
        $this->isRequiredOn('option100', []);

        verify($this->isText('option101', 100))->true();
        $this->isRequiredOn('option101', []);

        verify($this->isText('option102', 30))->true();
        $this->isRequiredOn('option102', []);

        verify($this->isText('option103', 500))->true();
        $this->isRequiredOn('option103', []);

        verify($this->isMail('option104', 254))->true();
        $this->isRequiredOn('option104', []);

        verify($this->filterRules($this->rules, 'option105'))->isEmpty();

        verify($this->isText('option106', 200))->true();
        verify($this->isRequired('option106'))->true();

        verify($this->filterRules($this->rules, 'option107'))->isEmpty();

        verify($this->filterRules($this->rules, 'option108'))->isEmpty();

        verify($this->filterRules($this->rules, 'option109'))->isEmpty();

    }

    public function testGetAdminRules()
    {
        $this->rules = Yii::$app->functionItemSet->admin->rules;

        verify($this->isNum('admin_no', null))->true();
        verify($this->isRequired('admin_no'))->false();

        verify($this->isNum('corp_master_id', null))->true();
        verify($this->isRequired('corp_master_id'))->false();

        verify($this->isNum('client_master_id', null))->true();
        verify($this->isRequired('client_master_id'))->false();

        verify($this->isText('fullName', 401))->true();
        verify($this->isRequired('fullName'))->false();

        verify($this->isText('name_sei', 200))->true();
        verify($this->isRequired('name_sei'))->false();

        verify($this->isText('name_mei', 200))->true();
        verify($this->isRequired('name_mei'))->false();

        verify($this->isText('login_id', 20))->true();
        verify($this->isRequired('login_id'))->false();

        verify($this->isText('password', 20))->true();
        verify($this->isRequired('password'))->false();

        verify($this->isTelNum('tel_no'))->true();
        $this->isRequiredOn('tel_no', []);

        verify($this->isSafe('exceptions'))->true();
        verify($this->isRequired('exceptions'))->false();

        verify($this->isMail('mail_address', 254))->true();
        verify($this->isRequired('mail_address'))->false();

        verify($this->isText('option100', 300))->true();
        verify($this->isRequired('option100'))->true();

        verify($this->isNum('option101', 30))->true();
        $this->isRequiredOn('option101', []);

        verify($this->isMail('option102', 254))->true();
        $this->isRequiredOn('option102', []);

        verify($this->filterRules($this->rules, 'option103'))->isEmpty();

        verify($this->filterRules($this->rules, 'option104'))->isEmpty();

        verify($this->filterRules($this->rules, 'option105'))->isEmpty();

        verify($this->filterRules($this->rules, 'option106'))->isEmpty();

        verify($this->filterRules($this->rules, 'option107'))->isEmpty();

        verify($this->filterRules($this->rules, 'option108'))->isEmpty();

        verify($this->filterRules($this->rules, 'option109'))->isEmpty();

    }

    public function testGetApplicationRules()
    {
        $this->rules = Yii::$app->functionItemSet->application->rules;

        verify($this->isNum('application_no', null))->true();
        verify($this->isRequired('application_no'))->false();

        verify($this->filterRules($this->rules, 'corpLabel'))->isEmpty();

        verify($this->filterRules($this->rules, 'clientLabel'))->isEmpty();

        verify($this->isText('fullName', 401))->true();
        verify($this->isRequired('fullName'))->true();

        verify($this->isText('name_sei', 200))->true();
        verify($this->isRequired('name_sei'))->false();

        verify($this->isText('name_mei', 200))->true();
        verify($this->isRequired('name_mei'))->false();

        verify($this->isText('fullNameKana', 401))->true();
        verify($this->isRequired('fullNameKana'))->true();

        verify($this->isText('kana_sei', 200))->true();
        verify($this->isRequired('kana_sei'))->false();

        verify($this->isText('kana_mei', 200))->true();
        verify($this->isRequired('kana_mei'))->false();

        verify($this->isSafe('sex'))->true();
        verify($this->isRequired('sex'))->true();

        verify($this->isText('birth_date', null))->true();
        verify($this->isRequired('birth_date'))->true();

        verify($this->isNum('pref_id', null))->true();
        verify($this->isRequired('pref_id'))->true();

        verify($this->isText('address', 50))->true();
        $this->isRequiredOn('address', []);

        verify($this->isTelNum('tel_no'))->true();
        verify($this->isRequired('tel_no'))->false();

        verify($this->isMail('mail_address', 254))->true();
        verify($this->isRequired('mail_address'))->false();

        verify($this->isNum('occupation_id', null))->true();
        $this->isRequiredOn('occupation_id', []);

        verify($this->isText('self_pr', 500))->true();
        verify($this->isRequired('self_pr'))->true();

        verify($this->isSafe('carrier_type'))->true();
        verify($this->isRequired('carrier_type'))->false();

        verify($this->filterRules($this->rules, 'created_at'))->isEmpty();

        verify($this->isNum('application_status_id', null))->true();
        verify($this->isRequired('application_status_id'))->false();

        verify($this->isSafe('option100'))->true();
        $this->isRequiredOn('option100', []);

        verify($this->isSafe('option101'))->true();
        $this->isRequiredOn('option101', []);

        verify($this->filterRules($this->rules, 'option102'))->isEmpty();

        verify($this->filterRules($this->rules, 'option103'))->isEmpty();

        verify($this->filterRules($this->rules, 'option104'))->isEmpty();

        verify($this->filterRules($this->rules, 'option105'))->isEmpty();

        verify($this->filterRules($this->rules, 'option106'))->isEmpty();

        verify($this->filterRules($this->rules, 'option107'))->isEmpty();

        verify($this->filterRules($this->rules, 'option108'))->isEmpty();

        verify($this->filterRules($this->rules, 'option109'))->isEmpty();

    }

    public function testGetInquiryRules()
    {
        $this->rules = Yii::$app->functionItemSet->inquiry->rules;

        verify($this->isText('company_name', 200))->true();
        verify($this->isRequired('tanto_name'))->true();

        verify($this->isText('post_name', 200))->true();
        $this->isRequiredOn('post_name', []);

        verify($this->isText('tanto_name', 200))->true();
        verify($this->isRequired('tanto_name'))->true();

        verify($this->isText('job_type', 200))->true();
        verify($this->isRequired('job_type'))->true();

        verify($this->isText('postal_code', 50))->true();
        verify($this->isRequired('postal_code'))->true();

        verify($this->isText('address', 200))->true();
        verify($this->isRequired('address'))->true();

        verify($this->isTelNum('tel_no'))->true();
        verify($this->isRequired('tel_no'))->false();

        verify($this->isText('fax_no', 200))->true();
        $this->isRequiredOn('fax_no', []);

        verify($this->isText('mail_address', 200))->true();
        verify($this->isRequired('mail_address'))->false();

        verify($this->isText('option100', 1000))->true();
        verify($this->isRequired('option100'))->true();

        verify($this->isText('option101', 500))->true();
        $this->isRequiredOn('option101', []);

        verify($this->isText('option102', 500))->true();
        $this->isRequiredOn('option102', []);

        verify($this->filterRules($this->rules, 'option103'))->isEmpty();

        verify($this->isSafe('option104'))->true();
        $this->isRequiredOn('option104', []);

        verify($this->isText('option105', 200))->true();
        $this->isRequiredOn('option105', []);

        verify($this->isText('option106', 2000))->true();
        $this->isRequiredOn('option106', []);

        verify($this->isText('option107', 200))->true();
        $this->isRequiredOn('option107', []);

        verify($this->isText('option108', 60))->true();
        $this->isRequiredOn('option108', []);

        verify($this->isText('option109', 200))->true();
        $this->isRequiredOn('option109', []);

    }

    /**
     * StringValidatorが指定のmaxで使われていればtrueを返す
     * @param $attribute
     * @param $max
     * @return bool
     */
    private function isText($attribute, $max)
    {
        foreach ($this->rules as $rule) {
            if ($rule[0] == $attribute && $rule[1] == 'string') {
                if (
                    ($max && $rule['max'] == $max)
                    || (!$max && !isset($rule['max']))
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * NumberValidatorが指定のmaxで使われていればtrueを返す
     * @param $attribute
     * @param $max
     * @return bool
     */
    private function isNum($attribute, $max)
    {
        foreach ($this->rules as $rule) {
            if ($rule[0] == $attribute && $rule[1] == 'number') {
                if (
                    ($max && $rule['max'] == $max)
                    || (!$max && !isset($rule['max']))
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * UrlValidatorが指定のmaxで使われていればtrueを返す
     * @param $attribute
     * @param $max
     * @return bool
     */
    private function isUrl($attribute, $max)
    {
        verify($this->isText($attribute, $max))->true();
        foreach ($this->rules as $rule) {
            if ($rule[1] == 'url' && in_array($attribute, $rule[0])) {
                return true;
            }
        }
        return false;
    }

    /**
     * MailValidatorが指定のmaxで使われていればtrueを返す
     * @param $attribute
     * @param $max
     * @return bool
     */
    private function isMail($attribute, $max)
    {
        verify($this->isText($attribute, $max))->true();
        foreach ($this->rules as $rule) {
            if ($rule[1] == 'email' && in_array($attribute, $rule[0])) {
                return true;
            }
        }
        return false;
    }

    /**
     * SafeValidatorが使われていればtrueを返す
     * @param $attribute
     * @return bool
     */
    private function isSafe($attribute)
    {
        foreach ($this->rules as $rule) {
            if ($rule[1] == 'safe' && in_array($attribute, $rule[0])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 電話番号のMatchValidatorが使われていればtrueを返す
     * @param $attribute
     * @return bool
     */
    private function isTelNum($attribute)
    {
        foreach ($this->rules as $rule) {
            if ($rule == [$attribute, 'match', 'pattern' => '/^[0-9-]+$/', 'message' => '{attribute}は半角数字で入力してください。']) {
                return true;
            }
        }
        return false;
    }

    /**
     * RequiredValidatorが使われていればtrueを返す
     * @param $attribute
     * @return bool
     */
    private function isRequired($attribute)
    {
        foreach ($this->rules as $rule) {
            if ($rule[1] == 'required' && in_array($attribute, $rule[0]) && !isset($rule['on'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * RequiredValidatorが指定のdispTypeで使われていればtrueを返す
     * @param $attribute
     * @param $dispTypes
     */
    private function isRequiredOn($attribute, $dispTypes)
    {
        verify($this->isRequired($attribute))->false();
        $types = [];
        foreach ($this->rules as $rule) {
            if ($rule[1] == 'required' && in_array($attribute, $rule[0]) && isset($rule['on'])) {
                $types[] = str_replace('type', '', $rule['on'][2]);
            }
        }
        verify($types)->equals($dispTypes);
    }

    /**
     * attributeが含まれるrulesを返す
     * @param $rules
     * @param $attribute
     * @return array
     */
    private function filterRules($rules, $attribute)
    {
        return array_filter($rules, function ($element) use ($attribute) {
            return $element[0] == $attribute || (is_array($element[0]) && ArrayHelper::isIn($attribute, $element[0]));
        });
    }

////// jobでのみ使用されるmethod ///////////////////////////////////////////////////////////////////////////////////////
    /**
     * getShortDisplayItemsのtest
     * short_displayが前のものより大きいことを検査してfilterとsortの検証としている
     */
    public function testGetShortDisplayItems()
    {
        foreach (self::MENUS as $menu) {
            if ($menu == 'job') {
                verify($this->columnSets[$menu]->shortDisplayItems)->notEmpty();
                $i = 1;
                foreach ($this->columnSets[$menu]->shortDisplayItems as $items) {
                    /* @var \app\models\manage\JobColumnSet $items */
                    verify($items->short_display)->greaterOrEquals($i);
                    $i = $items->short_display;
                }
            } else {
                verify($this->columnSets[$menu]->getShortDisplayItems())->equals([]);
            }
        }
    }

    /**
     * getSearchResultDisplayItemsのtest
     * short_displayが前のものより大きいことを検査してfilterとsortの検証としている
     */
    public function testGetSearchResultDisplayItems()
    {
        foreach (self::MENUS as $menu) {
            if ($menu == 'job') {
                verify($this->columnSets[$menu]->searchResultDisplayItems)->notEmpty();
                $i = 1;
                foreach ($this->columnSets[$menu]->searchResultDisplayItems as $items) {
                    /* @var \app\models\manage\JobColumnSet $items */
                    verify($items->search_result_display)->greaterOrEquals($i);
                    $i = $items->search_result_display;
                }
            } else {
                verify($this->columnSets[$menu]->searchResultDisplayItems)->equals([]);
            }
        }
    }

    public function testGetTagLabels()
    {
        foreach (self::MENUS as $menu) {
            verify($this->columnSets[$menu]->tagLabels)->notEmpty();
            foreach ($this->columnSets[$menu]->tagLabels as $item) {
                /* @var BaseColumnSet $item */
                verify(ArrayHelper::isIn($item->column_name, Site::TAG_CONVERSION_MAP))->true();
            }
        }
    }

////// applicationでのみ使用されるmethod ///////////////////////////////////////////////////////////////////////////////
    public function testGetApplyDispItems()
    {
        foreach (self::MENUS as $menu) {
            if ($menu == 'application') {
                verify($this->columnSets[$menu]->applyDispItems)->notEmpty();
                foreach ($this->columnSets[$menu]->applyDispItems as $item) {
                    verify(ArrayHelper::isIn($item->column_name, ApplicationColumnSet::ITEMS_NOT_REGISTERED))->false();
                }
            } else {
                verify($this->columnSets[$menu]->applyDispItems)->equals($this->columnSets[$menu]->items);
            }

        }
    }

    public function testGetApplyItemColumns()
    {
        foreach (self::MENUS as $menu) {
            if ($menu == 'application') {
                $attributes = $this->columnSets[$menu]->applyItemColumns;
                verify($attributes)->notEmpty();
                verify($attributes)->contains('birth_date:date');
                verify($attributes)->contains('prefName');
                verify($attributes)->contains('occupationName');
                verify($attributes)->contains('sex:sex');
            } else {
                foreach ($this->columnSets[$menu]->applyItemColumns as $attribute) {
                    verify($attribute)->equals($this->columnSets[$menu]->applyDispItems[$attribute]->column_name);
                }
            }

        }
    }
}