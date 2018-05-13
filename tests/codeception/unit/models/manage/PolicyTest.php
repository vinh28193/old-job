<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\Policy;
use tests\codeception\fixtures\PolicyFixture;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class PolicyTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new Policy();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {

        $this->specify('必須チェック', function () {
            $model = new Policy();
            $model->load([$model->formName() => [
                'policy_name' => null,
                'policy' => null,
                'valid_chk' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('policy_name'))->true();
            verify($model->hasErrors('policy'))->true();
            verify($model->hasErrors('valid_chk'))->true();

        });
        $this->specify('数字チェック', function () {
            $model = new Policy();
            $model->load([$model->formName() => [
                'policy_no' => '文字列',
                'page_type' => '文字列',
                'from_type' => '文字列',
                'tenant_id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('policy_no'))->true();
            verify($model->hasErrors('page_type'))->true();
            verify($model->hasErrors('from_type'))->true();
            verify($model->hasErrors('tenant_id'))->true();
        });

        $this->specify('booleanチェック', function () {
            $model = new Policy();
            $model->load([$model->formName() => [
                'valid_chk' => 3,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('文字列の最大', function () {
            $model = new Policy();

            $model->load([$model->formName() => [
                'policy_name' => str_repeat('a', 31),
                'description' => str_repeat('a', 51),
            ]]);
            $model->validate();
            verify($model->hasErrors('policy_name'))->true();
            verify($model->hasErrors('description'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = new Policy();
            $model->load([$model->formName() => [
                'policy' => (int)1,
                'policy_name' => (int)1,
                'description' => (int)1,
            ]]);
            $model->validate();
            verify($model->hasErrors('policy'))->true();
            verify($model->hasErrors('policy_name'))->true();
            verify($model->hasErrors('description'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new Policy();
            $model->load([$model->formName() => [
                'policy_no' => 1,
                'page_type' => 1,
                'from_type' => 1,
                'tenant_id' => 1,
                'valid_chk' => 1,
                'policy_name' => str_repeat('a', 30),
                'description' => str_repeat('a', 50),
                'policy' => 'test',
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * テキスト取得テスト
     */
    public function testGetValidChkLabel()
    {
        $model = new Policy();
        verify($model->getValidChkLabel()[Policy::VALID])->equals('公開');
        verify($model->getValidChkLabel()[Policy::INVALID])->equals('非公開');
    }

    /**
     * テキスト取得テスト
     */
    public function testGetUrl()
    {
        $model = new Policy();
        $model->policy_no = 123;
        verify($model->url)->equals('http://jm2.yii/policy?policy_no=123');
    }

}