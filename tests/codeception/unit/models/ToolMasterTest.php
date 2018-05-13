<?php

namespace models;

use app\models\ToolMaster;
use tests\codeception\unit\fixtures\ToolMasterFixture;
use Codeception\Specify;
use Yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

class ToolMasterTest extends JmTestCase
{
    use Specify;

    private $params = [];

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        parent::_before();

        $this->params = [
            'tenant_id' => '1',
            'tool_no' => '1',
            'page_name' => 'page name',
            'title' => str_repeat('t', ToolMaster::TITLE_MAX_LENGTH),
            'description' => str_repeat('あ', ToolMaster::DESCRIPTION_MAX_LENGTH),
            'keywords' => str_repeat('０', ToolMaster::KEYWORDS_MAX_LENGTH),
            'h1' => str_repeat('1', ToolMaster::H1_MAX_LENGTH),
        ];
    }

    public function fixtures()
    {
        return [
            'tool_master' => ToolMasterFixture::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function testTableName()
    {
        $model = new ToolMaster();
        verify($model->tableName())->equals('tool_master');
    }

    /**
     * @inheritdoc
     */
    public function testRules()
    {
        $model = new ToolMaster();
        $params = $this->params;

        $this->specify('必須項目のチェックができること', function() use ($model) {
            $model->validate();

            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('tool_no'))->true();
            verify($model->hasErrors('page_name'))->true();

            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('description'))->true();
            verify($model->hasErrors('keywords'))->true();
            verify($model->hasErrors('h1'))->true();
        });

        $this->specify('半角数字入力のチェックができること', function() use ($model, $params) {
            // 全角数字
            $params['tenant_id'] = '１';
            // 文字列
            $params['tool_no'] = 'foo';
            $model->load(['ToolMaster' => $params]);
            $model->validate();

            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('tool_no'))->true();

            verify($model->hasErrors('page_name'))->false();
            verify($model->hasErrors('title'))->false();
            verify($model->hasErrors('description'))->false();
            verify($model->hasErrors('keywords'))->false();
            verify($model->hasErrors('h1'))->false();
        });

        $this->specify('最大文字数のチェックができること', function() use ($model, $params) {
            $fixtures = ArrayHelper::toArray($this->getFixture('tool_master'));
            $params = array_merge($params, [
                    'tool_no' => $fixtures[0]['tool_no'],
                    'page_name' => $fixtures[0]['page_name'],
                    'title' => str_repeat('t', ToolMaster::TITLE_MAX_LENGTH) + 1,
                    'description' => str_repeat('あ', ToolMaster::DESCRIPTION_MAX_LENGTH) + 1,
                    'keywords' => str_repeat('０', ToolMaster::KEYWORDS_MAX_LENGTH) + 1,
                    'h1' => str_repeat('1', ToolMaster::H1_MAX_LENGTH) + 1,
                ]
            );

            $model->load(['ToolMaster' => $params]);
            $model->validate();

            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('description'))->true();
            verify($model->hasErrors('keywords'))->true();
            verify($model->hasErrors('h1'))->true();

            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('tool_no'))->false();
            verify($model->hasErrors('page_name'))->false();
        });

        $this->specify('tool_noとページ名が存在しない場合はエラーになること', function() use ($model, $params) {
            $model->load(['ToolMaster' => $params]);
            $model->validate();

            verify($model->hasErrors('tool_no'))->true();
            verify($model->hasErrors('page_name'))->false();

            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('title'))->false();
            verify($model->hasErrors('description'))->false();
            verify($model->hasErrors('keywords'))->false();
            verify($model->hasErrors('h1'))->false();
        });

        $this->specify('tool_noとページ名が存在する場合はエラーにならないこと', function() use ($model, $params) {
            $fixtures = ArrayHelper::toArray($this->getFixture('tool_master'));
            $params['tool_no'] = $fixtures[0]['tool_no'];
            $params['page_name'] = $fixtures[0]['page_name'];
            $model->load(['ToolMaster' => $params]);
            $model->validate();

            verify($model->hasErrors())->false();
        });

        $this->specify('tenant_idのチェックは行わないこと', function() use ($model, $params) {
            $params['tenant_id'] = '';
            $params['tool_no'] = '';
            $model->load(['ToolMaster' => $params]);
            $model->validate();

            verify($model->hasErrors('tenant_id'))->false();
            verify($model->hasErrors('tool_no'))->true();
        });
    }

    /**
     * @inheritdoc
     */
    public function testAttributeLabels()
    {
        $model = new ToolMaster();
        verify($model->attributeLabels())->notEmpty();
    }

}
