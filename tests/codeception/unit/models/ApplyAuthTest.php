<?php
/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/02
 * Time: 17:18
 */

namespace models\manage;


use app\models\ApplyAuth;
use tests\codeception\unit\JmTestCase;

class ApplyAuthTest extends JmTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testRules()
    {
        $this->specify('required', function () {
            $model = new ApplyAuth();
            $model->load(['ApplyAuth' => [
                'applicationId' => null,
                'fullName' => null,
                'nameSei' => null,
                'nameMei' => null,
                'mailAddress' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('applicationId'))->true();
            verify($model->hasErrors('fullName'))->true();
            verify($model->hasErrors('nameSei'))->true();
            verify($model->hasErrors('nameMei'))->true();
            verify($model->hasErrors('mailAddress'))->true();
        });

         $this->specify('mail format', function () {
             $model = new ApplyAuth();
             $model->load(['ApplyAuth' => [
                 'applicationId' => 1,
                 'fullName' => 'aaaa',
                 'nameSei' => 'aaa',
                 'nameMei' => 'aaaa',
                 'mailAddress' => "aaaaaa",
             ]]);
             $model->validate();
             verify($model->hasErrors('mailAddress'))->true();
         });

        $this->specify('true value', function () {
            $model = new ApplyAuth();
            $model->load(['ApplyAuth' => [
                'applicationId' => 1,
                'fullName' => 'aaaa',
                'nameSei' => 'aaa',
                'nameMei' => 'aaaa',
                'mailAddress' => "aaaaaa@aaaa.aaa",
            ]]);
            verify($model->validate())->true();
        });
    }

    // 不要
//    public function testAttributeLabels(){}
}
