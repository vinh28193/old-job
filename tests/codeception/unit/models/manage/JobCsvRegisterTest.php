<?php
namespace models\manage;

use app\modules\manage\components\JobCsvLoader;
use yii;
use yii\helpers\ArrayHelper;
use tests\codeception\unit\JmTestCase;
use app\modules\manage\models\JobCsvRegister;
use app\models\manage\JobMaster;
use app\models\manage\ClientMaster;
use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\MediaUpload;
use app\models\manage\JobColumnSet;
use app\models\manage\JobColumnSubset;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\SearchkeyItem1;
use app\models\manage\searchkey\SearchkeyItem11;

/**
 * Class JobCsvRegisterTest
 * @package models\manage
 */
class JobCsvRegisterTest extends JmTestCase
{
    /** @var int 状態 - 有効 */
    const FLAG_VALID = 1;

    /** @var int 状態 - 無効 */
    const FLAG_INVALID = 0;

//    /** 自明の内容のテストのため、省略 */
//    public function testInit(){}

    /** testRulesで合わせて検証できるため不要 */
//    public function test__set(){}
//    public function test__get(){}

    /**
     * ルールテスト
     * getter・setterのテストも兼ねてます。
     */
    public function testRules()
    {
        $this->setIdentity('owner_admin');

        /** -- 準備 -- */
        // 掲載タイプIDを準備
        $dispTypeId = $this->id(1, 'disp_type');

        /** 有効な料金プランを新規登録 */
        $plan = new ClientChargePlan([
            'price' => 1000,
            'valid_chk' => ClientChargePlan::VALID,
            'disp_type_id' => $dispTypeId,
            'client_charge_plan_no' => ClientChargePlan::find()->max('client_charge_plan_no') + 1,
        ]);
        $plan->save(false);

        /** 掲載企業を新規登録 */
        $client = new ClientMaster();
        $client->corp_master_id = $this->id(1, 'corp_master');
        $client->client_name = 'aaa';
        $client->save(false);

        /** 新規登録した掲載企業に新規登録したプランを1枠割り当て */
        $charge = new ClientCharge();
        $charge->load([
            $charge->formName() => [
                'client_charge_plan_id' => $plan->id,
                'client_master_id' => $client->id,
                'limit_num' => 1,
            ],
        ]);
        $charge->save(false);

        $this->specify('正常系チェック', function () use ($charge) {
            /** @var Dist[] $dists */
            // 有効なDist(市区町村)を取り出し、JobCsvRegisterの検索キーに入れる
            $dists = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'pref')->itemModels;
            $model = self::loadedJobCsvRegister();
            // job_column_setのis_mustが必須固定のもの以外全部任意の時しか通りません
            $model->load([
                $model->formName() => [
                    'valid_chk' => '1', //有効な適当な値を入れている
                    'disp_start_date' => '2016/10/01', //有効な適当な値を入れている
                    'disp_type_sort' => 1, //有効な適当な値を入れている
                    'clientNo' => $charge->clientMaster->client_no,
                    'clientChargePlanNo' => $charge->clientChargePlan->client_charge_plan_no,
                    'dist' => implode('|', [$dists[0]->dist_cd, $dists[1]->dist_cd]),
                ],
            ]);
            verify($model->validate())->true();
        });

        $this->specify('必須チェック', function () {
            $model = self::loadedJobCsvRegister();
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('disp_start_date'))->true();
            verify($model->hasErrors('clientChargePlanNo'))->true();
            verify($model->hasErrors('clientNo'))->true();
            verify($model->hasErrors('dist'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = self::loadedJobCsvRegister();
            $model->load([
                $model->formName() => [
                    'created_at' => '文字列',
                    'updated_at' => '文字列',
                    'disp_type_sort' => '文字列',
                    'media_upload_id_1' => '文字列',
                    'media_upload_id_2' => '文字列',
                    'media_upload_id_3' => '文字列',
                    'media_upload_id_4' => '文字列',
                    'media_upload_id_5' => '文字列',
                    'clientChargePlanNo' => '文字列',
                    'clientNo' => '文字列',
                    'stationCd1' => '文字列',
                    'transportTime1' => '文字列',
                    'stationCd2' => '文字列',
                    'transportTime2' => '文字列',
                    'stationCd3' => '文字列',
                    'transportTime3' => '文字列',
                    'import_site_job_id' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('created_at'))->true();
            verify($model->hasErrors('updated_at'))->true();
            verify($model->hasErrors('disp_type_sort'))->true();
            verify($model->hasErrors('media_upload_id_1'))->true();
            verify($model->hasErrors('media_upload_id_2'))->true();
            verify($model->hasErrors('media_upload_id_3'))->true();
            verify($model->hasErrors('media_upload_id_4'))->true();
            verify($model->hasErrors('media_upload_id_5'))->true();
            verify($model->hasErrors('clientChargePlanNo'))->true();
            verify($model->hasErrors('clientNo'))->true();
            verify($model->hasErrors('stationCd1'))->true();
            verify($model->hasErrors('transportTime1'))->true();
            verify($model->hasErrors('stationCd2'))->true();
            verify($model->hasErrors('transportTime2'))->true();
            verify($model->hasErrors('stationCd3'))->true();
            verify($model->hasErrors('transportTime3'))->true();
            verify($model->hasErrors('import_site_job_id'))->true();
        });

        $this->specify('boolチェック', function () {
            $model = self::loadedJobCsvRegister();
            $model->load([
                $model->formName() => [
                    'valid_chk' => 50,
                    'transportType1' => 50,
                    'transportType2' => 50,
                    'transportType3' => 50,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('transportType1'))->true();
            verify($model->hasErrors('transportType2'))->true();
            verify($model->hasErrors('transportType3'))->true();

            $model = self::loadedJobCsvRegister();
            $model->load([
                $model->formName() => [
                    'valid_chk' => 1,
                    'transportType1' => 1,
                    'transportType2' => 1,
                    'transportType3' => 1,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('transportType1'))->false();
            verify($model->hasErrors('transportType2'))->false();
            verify($model->hasErrors('transportType3'))->false();

            $model = self::loadedJobCsvRegister();
            $model->load([
                $model->formName() => [
                    'valid_chk' => false,
                    'transportType1' => false,
                    'transportType2' => false,
                    'transportType3' => false,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('transportType1'))->false();
            verify($model->hasErrors('transportType2'))->false();
            verify($model->hasErrors('transportType3'))->false();
        });

        $model = self::loadedJobCsvRegister();
        //今後他の権限でのCSV一括登録を見越して、別メソッドにしている。
        $this->validateDate($model);

        $this->specify('仕事No存在チェック', function () {
            $jobCsv = self::loadedJobCsvRegister();
            $jobCsv->load([$jobCsv->formName() => ['job_no' => JobMaster::find()->max('job_no') + 1]]);//存在しない仕事No
            $jobCsv->validate();
            verify($jobCsv->hasErrors('job_no'))->true();

            $jobCsv = self::loadedJobCsvRegister();
            $jobCsv->load([$jobCsv->formName() => ['job_no' => JobMaster::find()->max('job_no')]]);//存在する仕事No
            $jobCsv->validate();
            verify($jobCsv->hasErrors('job_no'))->false();
        });

        $this->specify('掲載企業No存在チェック', function () {
            $jobCsv = self::loadedJobCsvRegister();
            $jobCsv->load([$jobCsv->formName() => ['clientNo' => ClientMaster::find()->max('client_no') + 1]]);//存在しない掲載企業No
            $jobCsv->validate();
            verify($jobCsv->hasErrors('clientNo'))->true();

            $maxNo = ClientMaster::find()->max('client_no');
            $jobCsv = self::loadedJobCsvRegister();
            $jobCsv->load([$jobCsv->formName() => ['clientNo' => $maxNo]]);//存在する掲載企業No
            $jobCsv->validate();
            verify($jobCsv->hasErrors('clientNo'))->false();
            verify($jobCsv->client_master_id)->equals(ClientMaster::findOne(['client_no' => $maxNo])->id);   //掲載企業IDがセットされているか
        });

        $this->specify('料金プランチェック', function () use ($plan, $client, $charge) {
            /* 料金プランIDの存在チェック */
            //そもそも料金プランが存在していない場合はエラー
            self::validClientChargePlanNoInvalid($client->client_no, $plan->client_charge_plan_no + 1);
            //存在はしているが、掲載企業が持たない場合はエラー
            self::validClientChargePlanNoInvalid($client->client_no, $plan->client_charge_plan_no - 1);

            //料金プランが無効の場合はエラー
            self::validChange($plan, false);
            self::validClientChargePlanNoInvalid($client->client_no, $plan->client_charge_plan_no);

            //料金プランが有効(かつ枠数が1の場合)の場合、1件目はエラーなし
            self::validChange($plan, true);
            self::validClientChargePlanNoValid($client->client_no, $plan->client_charge_plan_no, $plan->id);

            //料金プランが有効(かつ枠数上限なし)の場合、2件目でもエラーなし
            $charge->load([$charge->formName() => ['limit_num' => null]]);
            $charge->save(false);
            self::validClientChargePlanNoValid($client->client_no, $plan->client_charge_plan_no, $plan->id);
        });

        $this->specify('画像ファイル存在チェック', function () {
            /* 画像ファイル名が存在しない場合、エラー*/
            $jobCsv = self::loadedJobCsvRegister();
            $jobCsv->load([
                $jobCsv->formName() => [
                    'dispFileName1' => 'aaa.jpg',
                    'dispFileName2' => 'aaa.jpg',
                    'dispFileName3' => 'aaa.jpg',
                    'dispFileName4' => 'aaa.jpg',
                    'dispFileName5' => 'aaa.jpg',
                ],
            ]);
            $jobCsv->validate();
            verify($jobCsv->hasErrors('dispFileName1'))->true();
            verify($jobCsv->hasErrors('dispFileName2'))->true();
            verify($jobCsv->hasErrors('dispFileName3'))->true();
            verify($jobCsv->hasErrors('dispFileName4'))->true();
            verify($jobCsv->hasErrors('dispFileName5'))->true();

            /*
             * 画像ファイル名を登録し、その画像ファイルが参照できることを確認。
             * 画像ファイル名が存在する場合はエラーを返さない
             */
            $media = new MediaUpload();
            $media->load([
                $media->formName() => [
                    'disp_file_name' => 'aaa.jpg',
                ],
            ]);
            $media->save_file_name = 'save.jpg';
            $media->save(false);

            $jobCsv = self::loadedJobCsvRegister();
            $jobCsv->load([
                $jobCsv->formName() => [
                    'dispFileName1' => $media->disp_file_name,
                    'dispFileName2' => $media->disp_file_name,
                    'dispFileName3' => $media->disp_file_name,
                    'dispFileName4' => $media->disp_file_name,
                    'dispFileName5' => $media->disp_file_name,
                ],
            ]);
            $jobCsv->validate();
            verify($jobCsv->hasErrors('dispFileName1'))->false();
            verify($jobCsv->hasErrors('dispFileName2'))->false();
            verify($jobCsv->hasErrors('dispFileName3'))->false();
            verify($jobCsv->hasErrors('dispFileName4'))->false();
            verify($jobCsv->hasErrors('dispFileName5'))->false();
            verify($jobCsv->media_upload_id_1)->equals($media->id);
            verify($jobCsv->media_upload_id_2)->equals($media->id);
            verify($jobCsv->media_upload_id_3)->equals($media->id);
            verify($jobCsv->media_upload_id_4)->equals($media->id);
            verify($jobCsv->media_upload_id_5)->equals($media->id);
        });

        $this->specify('オプション選択肢存在チェック(ラジオボタン)', function () {
            $columnSet = JobColumnSet::findOne(['data_type' => JobColumnSet::DATA_TYPE_RADIO]);
            //空文字の場合、オプション項目が必須ならばエラー、任意ならエラーを返さない
            self::validateOption(false, $columnSet, '');
            //無効な文字列の場合、エラーを返す
            self::validateOption(true, $columnSet, 'aaaaa');

            //オプション選択項目として設定されているものの任意のどれかの場合、エラーを返さない
            /** @var JobColumnSubset $columnSubset */
            $columnSubsets = JobColumnSubset::find()->where(['column_name' => $columnSet->column_name])->all();
            foreach ($columnSubsets as $columnSubset) {
                self::validateOption(false, $columnSet, $columnSubset->subset_name);
            }
        });

        $this->specify('オプション選択肢存在チェック(チェックボックス)', function () {
            $columnSet = JobColumnSet::findOne(['data_type' => JobColumnSet::DATA_TYPE_CHECK]);
            //空文字の場合、オプション項目が必須ならばエラー、任意ならエラーを返さない
            self::validateOption(false, $columnSet, '');
            //無効な文字列の場合、エラーを返す
            self::validateOption(true, $columnSet, 'aaaaa');

            //オプション選択項目として設定されているものの任意のどれかの場合、エラーを返さない
            /** @var JobColumnSubset $columnSubset */
            $columnSubsets = JobColumnSubset::find()->where(['column_name' => $columnSet->column_name])->all();
            foreach ($columnSubsets as $columnSubset) {
                self::validateOption(false, $columnSet, $columnSubset->subset_name);
            }
            //オプション選択項目として設定されているものを","でつないでいた場合、エラーを返さない
            self::validateOption(
                false,
                $columnSet,
                implode(',', ArrayHelper::getColumn($columnSubsets, 'subset_name'))
            );
        });

        $this->specify('勤務地検索キー存在チェック', function () {
            //勤務地検索キーに存在しない検索キーNoが入った場合エラーになる。
            self::validateNotExistKey('dist');

            /** @var Dist[] $items */
            //有効なDist(市区町村)を取り出し、JobCsvRegisterの検索キーに入れる
            $items = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'pref')->itemModels;
            $item1 = $items[0];
            $item2 = $items[1];

            //勤務地検索キーが有効の場合、エラーにならない
            self::validateModelValidChk($item1, $item2, 'dist', 'dist_cd');

            //勤務地検索キーに紐づくエリアが無効の場合、エラーになる
            $area = $item1->pref->area;
            self::validateRelationalModelInvalidChk($item1, $item2, 'dist', 'dist_cd', $area);
        });

        $this->specify('駅コード検索キー存在チェック', function () {
            //検索キーが存在しない場合
            self::validateStation(false, null, null, null);
            //検索キーが間違っている場合
            self::validateStation(true, '999999', null, null);

            $stationNo = Station::find()->select(['station_no'])->groupBy(['station_no'])->scalar();

            //検索キーが存在するが、所要時間がない場合
            self::validateStation(true, $stationNo, 1, null);

            //検索キーが存在するが、交通手段がない場合
            self::validateStation(true, $stationNo, null, 10);

            //検索キー・交通手段・所要時間が存在する場合
            self::validateStation(false, $stationNo, 1, 10);
        });

        $this->specify('給与検索キー存在チェック', function () {
            //給与検索キーが空でもエラーにならない。
            self::validateNotRequireKey('maxWage');

            /** @var WageCategory[] $cates */
            //有効な給与検索キーを取り出し、そのモデルの有効・無効を切り替えてテストする
            $cates = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'wage_category')->searchkeyModels;

            //給与検索キーに存在しない検索キーNoが入った場合、エラーになる。
            $loadCate['wageItem_' . $cates[0]->id] = '999999';
            self::validateWage($loadCate, true);

            //検索キーNoに対応する給与検索キーが無効の場合、エラーになる。
            self::validChange($cates[0]->wageItem[0], false);
            $loadCate['wageItem_' . $cates[0]->id] = $cates[0]->wageItem[0]->wage_item_name;
            self::validateWage($loadCate, true);
            self::validChange($cates[0]->wageItem[0], true);

            //給与検索キーに存在する有効な検索キーNoが入った場合、エラーにならない。
            $loadCate['wageItem_' . $cates[0]->id] = $cates[0]->wageItem[0]->wage_item_name;
            self::validateWage($loadCate, false);
        });

        $this->specify('汎用検索キー(2階層)存在チェック', function () {
            //汎用検索キーが空でもエラーにならない。
            self::validateNotRequireKey('searchkeyItem1');
            //汎用検索キーに存在しない検索キーNoが入った場合エラーになる。
            self::validateNotExistKey('searchkeyItem1');

            /** @var SearchkeyItem1[] $items */
            //有効な汎用検索キー(二階層)を取り出し、そのモデル・親モデルの有効・無効を切り替えてテストする
            $items = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'searchkey_category1')->itemModels;
            $item1 = $items[0];
            $item2 = $items[1];

            //汎用検索キーが有効の場合、エラーにならない
            self::validateModelValidChk($item1, $item2, 'searchkeyItem1', 'searchkey_item_no');

            //汎用検索キーの親階層が無効の場合
            $cate1 = $item1->category;
            self::validateRelationalModelInvalidChk($item1, $item2, 'searchkeyItem1', 'searchkey_item_no', $cate1);

            //汎用検索キーが無効の場合
            self::validateModelInvalidChk($item1, $item2, 'searchkeyItem1', 'searchkey_item_no');
        });

        $this->specify('汎用検索キー(1階層)存在チェック', function () {
            //汎用検索キーが空でもエラーにならない。
            self::validateNotRequireKey('searchkeyItem11');
            //汎用検索キーに存在しない検索キーNoが入った場合エラーになる。
            self::validateNotExistKey('searchkeyItem11');

            /** @var SearchkeyItem11[] $items */
            //有効な汎用検索キー(一階層)を取り出し、そのモデルの有効・無効を切り替えてテストする
            $items = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'searchkey_item11')->itemModels;
            $item1 = $items[0];
            $item2 = $items[1];

            //汎用検索キーが有効の場合、エラーにならない
            self::validateModelValidChk($item1, $item2, 'searchkeyItem11', 'searchkey_item_no');
            //汎用検索キーが無効の場合、エラーになる
            self::validateModelInvalidChk($item1, $item2, 'searchkeyItem1', 'searchkey_item_no');
        });

        // 書き換えたdataを元に戻す
        self::getFixtureInstance('client_charge_plan')->initTable();
        self::getFixtureInstance('client_master')->initTable();
        self::getFixtureInstance('client_charge')->initTable();
        self::getFixtureInstance('media_upload')->initTable();
        self::getFixtureInstance('searchkey_item1')->initTable();
        self::getFixtureInstance('searchkey_category1')->initTable();
        self::getFixtureInstance('searchkey_item11')->initTable();
    }

    /**
     * 料金プランNoに関するバリデーションチェック（エラー時）
     * @param $clientNo int
     * @param $planNo int
     */
    private static function validClientChargePlanNoInvalid($clientNo, $planNo)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([
            $jobCsv->formName() => [
                'clientNo' => $clientNo,
                'clientChargePlanNo' => $planNo,
            ],
        ]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors('clientChargePlanNo'))->equals(true);
    }

    /**
     * 料金プランNoに関するバリデーションチェック（エラーなしの時。合わせて、料金プランIDのチェックも行う）
     * @param $clientNo int
     * @param $planNo int
     * @param $planId null|int
     */
    private static function validClientChargePlanNoValid($clientNo, $planNo, $planId)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([
            $jobCsv->formName() => [
                'clientNo' => $clientNo,
                'clientChargePlanNo' => $planNo,
                'disp_start_date' => 0,
            ],
        ]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors('clientChargePlanNo'))->equals(false);
        $jobCsv->save(false);
        verify($jobCsv->client_charge_plan_id)->equals($planId);
    }

    /**
     * オプション項目の選択式データタイプ（プルダウン|チェックボックス）で設定されている選択肢に関するバリデーションチェック
     * @param $result true:エラーあり | false:エラーなし
     * @param $columnSet JobColumnSet
     * @param $val string
     */
    private static function validateOption($result, $columnSet, $val)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([$jobCsv->formName() => [$columnSet->column_name => $val]]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors($columnSet->column_name))->equals($result);
    }

    /**
     * 検索キーNoのバリデーションチェック
     * （勤務地を除いて、必須ではない）
     * @param $property string バリデーションするプロパティ
     */
    private static function validateNotRequireKey($property)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->validate();
        verify($jobCsv->hasErrors($property))->false();
    }

    /**
     * 検索キーNoの検索キーが存在しない場合のバリデーションチェック
     * @param $property string バリデーションするプロパティ
     */
    private static function validateNotExistKey($property)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([$jobCsv->formName() => [$property => '999999']]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors($property))->true();
    }

    /**
     * 検索キーNoが存在するとき、エラーを返さないことを確認するバリデーションチェック
     * @param $model1 yii\db\ActiveRecord
     * @param $model2 yii\db\ActiveRecord
     * @param $validProperty string バリデーションするプロパティ
     * @param $modelProperty string 検索キーNoのプロパティ
     */
    private static function validateModelValidChk($model1, $model2, $validProperty, $modelProperty)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([$jobCsv->formName() => [$validProperty => $model1->$modelProperty . '|' . $model2->$modelProperty]]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors($validProperty))->false();
    }

    /**
     * 検索キーNoが存在するが、親のモデルが無効なとき、エラーを返すことを確認するバリデーションチェック
     * @param $model1 yii\db\ActiveRecord
     * @param $model2 yii\db\ActiveRecord
     * @param $validProperty string バリデーションするプロパティ
     * @param $modelProperty string 検索キーNoのプロパティ
     * @param $relation yii\db\ActiveRecord
     */
    private static function validateRelationalModelInvalidChk(
        $model1,
        $model2,
        $validProperty,
        $modelProperty,
        $relation
    ) {
        self::validChange($relation, self::FLAG_INVALID);
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([$jobCsv->formName() => [$validProperty => $model1->$modelProperty . '|' . $model2->$modelProperty]]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors($validProperty))->true();
        self::validChange($relation, self::FLAG_VALID);
    }

    /**
     * 検索キーが無効なとき、エラーを返すかチェック
     * @param $model1 yii\db\ActiveRecord
     * @param $model2 yii\db\ActiveRecord
     * @param $validProperty string バリデーションするプロパティ
     * @param $modelProperty string 検索キーNoのプロパティ
     */
    private static function validateModelInvalidChk($model1, $model2, $validProperty, $modelProperty)
    {
        self::validChange($model1, self::FLAG_INVALID);
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([$jobCsv->formName() => [$validProperty => $model1->$modelProperty . '|' . $model2->$modelProperty]]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors($validProperty))->true();
        self::validChange($model1, self::FLAG_VALID);
    }

    /**
     * 給与検索キー用のバリデーションテスト
     * @param $loadCate array
     * @param $result true:エラーあり | false:エラーなし
     */
    private function validateWage($loadCate, $result)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $jobCsv->load([$jobCsv->formName() => $loadCate]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors('maxWage'))->equals($result);
    }

    /**
     * 駅・路線検索キー用のバリデーションテスト
     * @param $result bool true:エラーあり | false:エラーなし
     * @param $stationNo int
     * @param $type int
     * @param $time int
     */
    private function validateStation($result, $stationNo, $type, $time)
    {
        $jobCsv = self::loadedJobCsvRegister();
        $param = [];
        if ($stationNo) {
            $param = array_merge($param, [
                'stationCd1' => $stationNo,
                'stationCd2' => $stationNo,
                'stationCd3' => $stationNo,
            ]);
        }
        if ($type) {
            $param = array_merge($param, [
                'transportType1' => $type,
                'transportType2' => $type,
                'transportType3' => $type,
            ]);
        }
        if ($time) {
            $param = array_merge($param, [
                'transportTime1' => $time,
                'transportTime2' => $time,
                'transportTime3' => $time,
            ]);
        }
        $jobCsv->load([$jobCsv->formName() => $param]);
        $jobCsv->validate();
        verify($jobCsv->hasErrors('stationCd1'))->equals($result);
        verify($jobCsv->hasErrors('stationCd2'))->equals($result);
        verify($jobCsv->hasErrors('stationCd3'))->equals($result);
    }

    /**
     * 対象モデルの有効・無効を切り替える
     * @param $model yii\db\ActiveRecord
     * @param $valid bool
     */
    private static function validChange($model, $valid)
    {
        $model->load([$model->formName() => ['valid_chk' => $valid]]);
        //TODO:SearchkeyItem11～20でモデルそのものに欠陥があるためsave(false)にしている
        $model->save(false);
        Yii::$app->clear('searchkey');
        Yii::$app->setComponents(['searchKey' => ['class' => 'app\common\SearchKey']]);
    }

    /**
     * 掲載開始日・終了日に関する検証
     * @param JobCsvRegister $model
     */
    private function validateDate($model)
    {
        $invalidStartValues = [
            '空白' => '',
            '不適切な文字列' => '文字列',
            '先過ぎる日付' => '2038/01/20',
            '過去過ぎる日付' => '1901/12/13',
        ];
        $invalidEndValues = [
            '不適切な文字列' => '文字列',
            '先過ぎる日付' => '2038/01/20',
            '過去過ぎる日付' => '1901/12/13',
        ];
        $validValue = '2016/08/31';
        // 両方invalid
        foreach ($invalidStartValues as $startValue) {
            foreach ($invalidEndValues as $endValue) {
                $model->load([
                    $model->formName() => [
                        'disp_start_date' => $startValue,
                        'disp_end_date' => $endValue,
                    ],
                ]);
                $model->validate();
                verify($model->hasErrors('disp_start_date'))->true();
                verify($model->hasErrors('disp_end_date'))->true();
            }
        }
        // 開始日時のみinvalid
        foreach ($invalidStartValues as $startValue) {
            $model->load([
                $model->formName() => [
                    'disp_start_date' => $startValue,
                    'disp_end_date' => $validValue,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_start_date'))->true();
            if ($startValue == '2038/01/20') {
                // compareValidationが走るのでこれだけエラーが出る
                verify($model->hasErrors('disp_end_date'))->true();
            } else {
                verify($model->hasErrors('disp_end_date'))->false();
            }
        }
        // 終了日時のみinvalid
        foreach ($invalidEndValues as $endValue) {
            $model->load([
                $model->formName() => [
                    'disp_start_date' => $validValue,
                    'disp_end_date' => $endValue,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_start_date'))->false();
            verify($model->hasErrors('disp_end_date'))->true();
        }
        // compare
        $model->load([
            $model->formName() => [
                'disp_start_date' => '2016/06/28',
                'disp_end_date' => '2016/06/27',
            ],
        ]);
        $model->validate();
        verify($model->hasErrors('disp_end_date'))->true();
        // 正しい値
        $model->load([
            $model->formName() => [
                'disp_start_date' => $validValue,
                'disp_end_date' => $validValue,
            ],
        ]);
        $model->validate();
        verify($model->hasErrors('disp_start_date'))->false();
        verify($model->hasErrors('disp_end_date'))->false();
    }

    /**
     * afterSave()のtest
     */
    public function testAfterSave()
    {
        //運営元管理者のみでテストするので
        $this->setIdentity('owner_admin');

        /** @var ClientCharge $charge */
        // 実際の登録の内容を見たいので、Model::save(false)を使わないようにするため
        // 有効なClientChargeを取り出し、掲載企業Noと料金プランNoを取得し、JobCsvRegisterにロードする。
        $charge = ClientCharge::find()->innerJoinWith(['clientChargePlan'])->where([ClientChargePlan::tableName() . '.valid_chk' => ClientChargePlan::VALID])->one();

        /** @var Dist[] $dists */
        //有効なDist(市区町村)を取り出し、JobCsvRegisterの検索キーに入れる
        $dists = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'pref')->itemModels;

        /** @var Station[] $stations */
        //有効なStation(駅コード)を取り出し、JobCsvRegisterの検索キーに入れる
        $stations = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'station')->itemModels;

        /** @var JobTypeSmall[] $cates */
        //有効なWageCate(職種コード)を取り出し、JobCsvRegisterの検索キーに入れる
        $cates = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'wage_category')->searchkeyModels;
        $maxWage = ['wageItem_' . $cates[0]->id => $cates[0]->wageItemValid[1]->wage_item_name];

        /** @var SearchkeyItem1[] $cateItems */
        //有効なSearchkeyItem1(汎用検索キー(二階層)コード)を取り出し、JobCsvRegisterの検索キーに入れる
        $cateItems = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'searchkey_category1')->itemModels;

        /** @var SearchkeyItem11[] $items */
        //有効なSearchkeyItem11(汎用検索キー(一階層)コード)を取り出し、JobCsvRegisterの検索キーに入れる
        $items = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, 'searchkey_item11')->itemModels;

        $model = self::loadedJobCsvRegister();
        $model->load([
            $model->formName() => array_merge([
                'valid_chk' => '1', //有効な適当な値を入れている
                'disp_start_date' => '2016/10/01', //有効な適当な値を入れている
                'clientNo' => $charge->clientMaster->client_no,
                'clientChargePlanNo' => $charge->clientChargePlan->client_charge_plan_no,
                'dist' => implode('|', [$dists[0]->dist_cd, $dists[1]->dist_cd]),
                'stationCd1' => $stations[0]->station_no,
                'transportType1' => '0',
                'transportTime1' => '10',
                'stationCd2' => $stations[1]->station_no,
                'transportType2' => '1',
                'transportTime2' => '20',
                'stationCd3' => $stations[2]->station_no,
                'transportType3' => '0',
                'transportTime3' => '30',
                'searchkeyItem1' => implode('|', [$cateItems[0]->searchkey_item_no, $cateItems[1]->searchkey_item_no]),
                'searchkeyItem11' => implode('|', [$items[0]->searchkey_item_no, $items[1]->searchkey_item_no]),
            ], $maxWage),
        ]);
        verify($model->save())->true();

        /** 勤務地(市区町村)検索キーのデータが入っているか */
        $this->tester->canSeeInDatabase('job_dist', ['job_master_id' => $model->id, 'dist_id' => $dists[0]->id]);
        $this->tester->canSeeInDatabase('job_dist', ['job_master_id' => $model->id, 'dist_id' => $dists[1]->id]);

        /** 駅・路線の検索キーのデータが入っているか */
        $this->tester->canSeeInDatabase('job_station_info', [
            'job_master_id' => $model->id,
            'station_id' => $stations[0]->station_no,
            'transport_type' => '0',
            'transport_time' => '10',
        ]);
        $this->tester->canSeeInDatabase('job_station_info', [
            'job_master_id' => $model->id,
            'station_id' => $stations[1]->station_no,
            'transport_type' => '1',
            'transport_time' => '20',
        ]);
        $this->tester->canSeeInDatabase('job_station_info', [
            'job_master_id' => $model->id,
            'station_id' => $stations[2]->station_no,
            'transport_type' => '0',
            'transport_time' => '30',
        ]);

        /** 給与検索キーのデータが入っているか */
        $this->tester->canSeeInDatabase(
            'job_wage',
            ['job_master_id' => $model->id, 'wage_item_id' => $cates[0]->wageItemValid[0]->id]
        );
        $this->tester->canSeeInDatabase(
            'job_wage',
            ['job_master_id' => $model->id, 'wage_item_id' => $cates[0]->wageItemValid[1]->id]
        );

        /** 汎用検索キー(2階層)のデータが入っているか */
        $this->tester->canSeeInDatabase(
            'job_searchkey_item1',
            ['job_master_id' => $model->id, 'searchkey_item_id' => $cateItems[0]->id]
        );
        $this->tester->canSeeInDatabase(
            'job_searchkey_item1',
            ['job_master_id' => $model->id, 'searchkey_item_id' => $cateItems[1]->id]
        );

        /** 汎用検索キー(1階層)のデータが入っているか */
        $this->tester->canSeeInDatabase(
            'job_searchkey_item11',
            ['job_master_id' => $model->id, 'searchkey_item_id' => $items[0]->id]
        );
        $this->tester->canSeeInDatabase(
            'job_searchkey_item11',
            ['job_master_id' => $model->id, 'searchkey_item_id' => $items[1]->id]
        );
    }

    /**
     * JobCsvLoaderで最初にJobCsvRegisterに負荷対策用データをロードする
     * （詳しくはJobCsvLoader::beforeCsvLoadを参照。）
     * @return JobCsvRegister
     */
    private static function loadedJobCsvRegister()
    {
        $loader = new JobCsvLoader();

        // 仕事情報を一時保存
        $jobs = JobMaster::find()->select([
            'job_no',
            'id',
            'client_charge_plan_id',
            'client_master_id',
        ])->asArray()->all();
        foreach ($jobs as $job) {
            $loader->jobNos2Ids[$job['job_no']] = $job['id'];
            if (isset($loader->plans[$job['client_master_id']][$job['client_charge_plan_id']])) {
                $loader->plans[$job['client_master_id']][$job['client_charge_plan_id']] += 1;
            } else {
                $loader->plans[$job['client_master_id']][$job['client_charge_plan_id']] = 1;
            }
            $loader->jobIds2Plans[$job['id']] = [
                'client_master_id' => $job['client_master_id'],
                'client_charge_plan_id' => $job['client_charge_plan_id'],
            ];
        }

        $loader->maxJobNo = end($jobs)['job_no'];
        unset($jobs);
        $loader->newJobNo = $loader->maxJobNo + 1;
        // 掲載企業情報を一時保存
        $loader->clientNos2Ids = ArrayHelper::map(
            ClientMaster::find()->select(['client_no', 'id'])->asArray()->all(),
            'client_no',
            'id'
        );
        // 企業ごとのプラン割り当て情報を一時保存
        $charges = ClientCharge::find()->select([
            'client_charge_plan_id',
            'client_master_id',
            'limit_num',
        ])->asArray()->all();
        foreach ($charges as $charge) {
            $loader->planLimits[$charge['client_master_id']][$charge['client_charge_plan_id']] = $charge['limit_num'];
        }
        unset($charges);
        // プラン情報を一時保存
        $loader->planNos2Ids = ArrayHelper::map(
            ClientChargePlan::find()->select([
                'id',
                'client_charge_plan_no',
            ])->where([ClientChargePlan::tableName() . '.valid_chk' => ClientChargePlan::VALID])->asArray()->all(),
            'client_charge_plan_no',
            'id'
        );
        // 画像情報を一時保存
        $loader->fileNames2Ids = ArrayHelper::map(MediaUpload::find()->select([
            'disp_file_name',
            'id',
        ])->asArray()->all(), 'disp_file_name', 'id');

        $model = new JobCsvRegister();
        $model->loader = $loader;
        return $model;
    }

//    /**
//     * csvAttributesのテスト
//     * 配列の形式のみのテストなので、手動テストでカバー
//     * このメソッドに関する修正をした場合、必ず手動テストを行うこと
//     */
//    public function testCsvAttributes(){}
}
