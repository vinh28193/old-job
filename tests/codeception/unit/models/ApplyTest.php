<?php

namespace models\manage;

use app\models\Apply;
use app\models\manage\ApplicationMaster;
use app\models\manage\ApplicationMasterSearch;
use tests\codeception\unit\JmTestCase;
use yii;
use tests\codeception\unit\fixtures\ApplicationMasterFixture;

/**
 * @group admin
 */
class ApplyTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $applyId = $this->id(11, 'application_master');
        $apply = Apply::findOne($applyId);
        verify($apply->rules())->notEmpty();

        $this->specify('電話番号', function() use ($apply) {
            $apply->tel_no = 'aaa';
            verify($apply->validate())->false();
        });

        $this->specify('性別', function() use ($apply) {
            $apply->sex = 'test';
            verify($apply->validate())->false();
        });

        $this->specify('no', function() {
            // バックアップが空の時はapplication_masterのnoの最大値+1が入る
            $model = new Apply();
            $model->validate();
            verify($model->application_no)->equals(ApplicationMasterFixture::RECORDS_PER_TENANT + 1);
            // application_noが最大値のレコードを削除する
            $deleteModel = ApplicationMasterSearch::find()->where(['application_no' => ApplicationMasterFixture::RECORDS_PER_TENANT])->one();
            $searchModel = new ApplicationMasterSearch();
            $searchModel->backupAndDelete([$deleteModel]);
            // バックアップのnoの最大値の方が大きい時はバックアップのnoの最大値+1が入る
            $model = new Apply();
            $model->validate();
            verify($model->application_no)->equals(ApplicationMasterFixture::RECORDS_PER_TENANT + 1);
        });

        self::getFixtureInstance('application_master')->load();
        self::getFixtureInstance('application_master_backup')->load();
    }

    /**
     * フィールドテスト
     */
    public function testFields()
    {
        $apply = new Apply();
        $apply->birth_date = '1992-08-09';
        $fileds = $apply->fields();
        verify(count($fileds))->equals(5);
        //生年月日クロージャーテスト
        verify($fileds['birth_date']($apply))->equals(Yii::$app->formatter->format('1992-08-09', 'date'));
    }

    /**
     * ロードテスト
     */
    public function testLoad()
    {
        $apply = new Apply();
        $applyList = $this->getFixture('application_master');
        $birthDates = [
            'birthDateYear' => 1992,
            'birthDateMonth' => 3,
            'birthDateDay' => 1
        ];
        $applyData = [
            'Apply' => yii\helpers\ArrayHelper::merge($applyList[0], $birthDates),
        ];
        verify($apply->load($applyData))->true();
        verify($apply->birth_date)->equals('1992-3-1');
    }

    /**
     * フォーマットテーブルのテスト
     */
    public function testGetFormatTable()
    {
        $apply = new Apply();
        $formatTable = $apply->formatTable;
        verify(count($formatTable))->equals(2);
        verify($formatTable['sex'][Apply::SEX_MALE])->equals('男性');
    }

    /**
     * 氏名セットテスト
     */
    public function testSetFullName()
    {
        $apply = new Apply();
        $apply->fullName = 'test TEST';
        verify($apply->fullName)->equals('test TEST');
        verify($apply->name_sei)->equals('test');
        verify($apply->name_mei)->equals('TEST');
    }

    /**
     * フリガナセットテスト
     */
    public function testSetFullNameKana()
    {
        $apply = new Apply();
        $apply->fullNameKana = 'test TEST';
        verify($apply->fullNameKana)->equals('test TEST');
        verify($apply->kana_sei)->equals('test');
        verify($apply->kana_mei)->equals('TEST');
    }

    /**
     * todo 属性取得テスト
     */
    public function testGetOccupationName()
    {
    }

    /**
     * 応募機器セットテスト
     */
    public function testSetCarrierTypeByUserAgent()
    {
        $userAgentIPad = 'Mozilla/5.0 (iPad; CPU OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1';
        $userAgentIPhone = 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1';
        $userAgentNexus10 = 'Mozilla/5.0 (Linux; Android 4.3; Nexus 10 Build/JSS15Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Safari/537.36';
        $userAgentNexus6 = 'Mozilla/5.0 (Linux; Android 5.1.1; Nexus 6 Build/LYZ28E) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.23 Mobile Safari/537.36';

        $apply = new Apply();
        $this->specify('iPad', function() use ($apply, $userAgentIPad) {
            $apply->setCarrierTypeByUserAgent($userAgentIPad);
            verify($apply->carrier_type)->equals(0);
        });

        $this->specify('iPhone', function() use ($apply, $userAgentIPhone) {
            $apply->setCarrierTypeByUserAgent($userAgentIPhone);
            verify($apply->carrier_type)->equals(1);
        });

        $this->specify('Nexus 10', function() use ($apply, $userAgentNexus10) {
            $apply->setCarrierTypeByUserAgent($userAgentNexus10);
            verify($apply->carrier_type)->equals(0);
        });

        $this->specify('Nexus 6', function() use ($apply, $userAgentNexus6) {
            $apply->setCarrierTypeByUserAgent($userAgentNexus6);
            verify($apply->carrier_type)->equals(1);
        });
    }

}
