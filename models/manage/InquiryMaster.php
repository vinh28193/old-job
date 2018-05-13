<?php

namespace app\models\manage;

use Yii;
use yii\base\Model;
use app\common\Helper\JmUtils;
use yii\helpers\Html;

/**
 * This is the model class for table "inquiry_master".
 *
 * @property  string $company_name
 * @property  string $post_name
 * @property  string $tanto_name
 * @property  string $job_type
 * @property  integer $postal_code
 * @property  string $address
 * @property  integer $tel_no
 * @property  string $fax_no
 * @property  string $mail_address
 * @property  string $option100
 * @property  string $option101
 * @property  string $option102
 * @property  string $option103
 * @property  string $option104
 * @property  string $option105
 * @property  string $option106
 * @property  string $option107
 * @property  string $option108
 * @property  string $option109
 *
 * @property string $additionalText
 */
class InquiryMaster extends Model
{
    public $company_name;
    public $post_name;
    public $tanto_name;
    public $job_type;
    public $postal_code;
    public $address;
    public $tel_no;
    public $fax_no;
    public $mail_address;
    public $option100;
    public $option101;
    public $option102;
    public $option103;
    public $option104;
    public $option105;
    public $option106;
    public $option107;
    public $option108;
    public $option109;

    /**
     * 本文と署名の間に入るテキスト
     * @var string
     */
    private $_additionalText;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(Yii::$app->functionItemSet->inquiry->rules, [
            ['mail_address', 'required'],
            ['postal_code', 'match', 'pattern' => '/^(\d{3}-\d{4}|\d{7})$/', 'message' => Yii::t('app', '郵便番号の書式が間違っています')],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return Yii::$app->functionItemSet->inquiry->attributeLabels;
    }


    /**
     * 追加挿入テキストのgetter
     * @return string
     */
    public function getAdditionalText()
    {
        if (JmUtils::isEmpty($this->_additionalText)) {
            $items = Yii::$app->functionItemSet->inquiry->items;
            $arrayText = [];
            foreach ($items as $attribute => $item) {
                $label = $this->getAttributeLabel($attribute);
                if (is_array($this->$attribute)) {
                    $this->$attribute = implode("\n", $this->$attribute);
                }
                $arrayText[] = "■{$label}\n{$this->$attribute}\n";
            }
            $this->_additionalText = implode("\n", $arrayText);
        }
        return $this->_additionalText;
    }

    /**
     * 表示のために、レコードを変換するための連想配列　(ex:['role_id' => ['0' => 'システム管理者', '1' => '全体管理者']])
     * @return array
     */
    public function getFormatTable()
    {
        return [];
    }

    /**
     * $formatTableに従って、DB上のデータを表示用に整形する
     * @param array $columns 取得する列
     * @return array
     */
    public function formatAsView($columns = null)
    {
        $rawRecord = $this->toArray();

        if (isset($columns)) {
            $record = array_filter($rawRecord, function ($key) use ($columns) {
                return in_array($key, $columns);
            }, ARRAY_FILTER_USE_KEY);
        } else {
            $record = $rawRecord;
        }

        foreach ($this->formatTable as $key => $value) {
            if (array_key_exists($key, $record) && isset($value[$record[$key]])) {
                $record[$key] = $value[$record[$key]];
            }
        }

        return $record;
    }

    /**
     * @param $attribute
     * @return string
     */
    public function subsetString($attribute)
    {
        $subsetItemNames = [];
        $subsetInputs = [];
        foreach ((array)$this->$attribute as $i => $subsetItemName) {
            $subsetItemNames[] = Html::encode($subsetItemName);
            $subsetInputs[] = Html::activeHiddenInput($this, $attribute . '[]', ['value' => $subsetItemName]);
        }
        return implode(', ', $subsetItemNames) . implode('', $subsetInputs);
    }
}
