<?php
/**
 * Created by PhpStorm.
 * User: Duc Thang
 * Date: 6/10/2016
 * Time: 1:02 PM
 */

namespace models\manage;

use app\models\manage\InquiryColumnSet;
use app\models\manage\InquiryMaster;
use tests\codeception\fixtures\InquiryColumnSetFixture;
use tests\codeception\unit\JmTestCase;

class InquiryMasterTest extends JmTestCase
{
    /**
     * fixture設定
     * @return array
     */
    public function fixtures()
    {
        return [
            'inquiry_column_set' => InquiryColumnSetFixture::className(),
        ];
    }

    public function testAttributeLabels()
    {
        $model = new InquiryMaster();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * rules test
     * 見やすさを優先したためこのようになっています。
     * 改行コードをLF(\n)にしないとテストがfailします。
     */
    public function testRules()
    {
        $this->specify('postal_codeは空白ではいけない', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'postal_code' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('postal_code'))->true();
        });

        $this->specify('check max length postal_code', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'postal_code' => '586-00000',
            ]]);
            $model->validate();
            verify($model->hasErrors('postal_code'))->true();
        });

        $this->specify('check valid postal_code', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'postal_code' => '58-60000',
            ]]);
            $model->validate();
            verify($model->hasErrors('postal_code'))->true();
        });

        $this->specify('check format postal_code', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'postal_code' => 'aaaaaaaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('postal_code'))->true();
        });

        $this->specify('check value true postal_code with 7 digital', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'postal_code' => '5860000',
            ]]);
            $model->validate();
            verify($model->hasErrors('postal_code'))->false();
        });

        $this->specify('check value true postal_code with 8 digital', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'postal_code' => '586-0000',
            ]]);
            $model->validate();
            verify($model->hasErrors('postal_code'))->false();
        });

        $this->specify('mail_addressは空白ではいけない', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'mail_address' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('mail_address'))->true();
        });

        $this->specify('check format mail_address', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'mail_address' => 'aaaaaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('mail_address'))->true();
        });

        $this->specify('check value true mail_address', function () {
            $model = new InquiryMaster();
            $model->load(['InquiryMaster' => [
                'mail_address' => 'aaaa@a.a',
            ]]);
            $model->validate();
            verify($model->hasErrors('mail_address'))->false();
        });
    }

    /**
     * InquiryColumnSetのfixtureDataに依存
     * valid_chkとsubsetとのデータ不整合状態に依存します
     */
    public function testGetAdditionalText()
    {
        self::getFixtureInstance('inquiry_column_subset')->load();
        $model = new InquiryMaster();
        $columnSets = \Yii::$app->functionItemSet->inquiry->items;
        $model->load([
            'InquiryMaster' => [
                'company_name' => 'Simple Company Name',
                'post_name' => 'Simple Post Name',
                'tanto_name' => 'Simple Tanto Name',
                'job_type' => 'Programer',
                'postal_code' => '5560000',
                'address' => '大阪府大阪市浪速区',
                'tel_no' => '0120-083-233',
                'fax_no' => '0120-083-233',
                'mail_address' => 'example@example.com',
                'option100' => '',
                'option101' => '',
                'option102' => '',
                'option103' => '',
                'option104' => '',
                'option105' => '',
                'option106' => '',
                'option107' => '',
                'option108' => '',
                'option109' => '',
            ]
        ]);

        $textVerify = "■{$columnSets['company_name']->label}
$model->company_name

■{$columnSets['post_name']->label}
$model->post_name

■{$columnSets['tanto_name']->label}
$model->tanto_name

■{$columnSets['job_type']->label}
$model->job_type

■{$columnSets['postal_code']->label}
$model->postal_code

■{$columnSets['address']->label}
$model->address

■{$columnSets['tel_no']->label}
$model->tel_no

■{$columnSets['fax_no']->label}
$model->fax_no

■{$columnSets['mail_address']->label}
$model->mail_address

■{$columnSets['option100']->label}
$model->option100

■{$columnSets['option101']->label}
$model->option101

■{$columnSets['option102']->label}
$model->option102

■{$columnSets['option104']->label}
$model->option104

■{$columnSets['option105']->label}
$model->option105

■{$columnSets['option106']->label}
$model->option106

■{$columnSets['option107']->label}
$model->option107

■{$columnSets['option108']->label}
$model->option108

■{$columnSets['option109']->label}
$model->option109
";
        verify($model->additionalText)->equals($textVerify);
    }
}
