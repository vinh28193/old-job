<?php
/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:19
 */

namespace models\manage;

use app\models\PasswordReminder;
use tests\codeception\unit\JmTestCase;

class PasswordReminderTest extends JmTestCase
{
    public function testRules()
    {
        $this->specify('必須項目検証', function () {
            $model = new PasswordReminder();
            $model->validate();
            verify($model->hasErrors('key_id'))->true();
            verify($model->hasErrors('collation_key'))->true();
            verify($model->hasErrors('created_at'))->true();
        });
        $this->specify('整数項目検証', function () {
            $model = new PasswordReminder();
            $model->tenant_id = 3.14;
            $model->key_id = 'aaaa';
            $model->created_at = 'aaaaa';
            $model->key_flg = 'aaaaa';
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('key_id'))->true();
            verify($model->hasErrors('key_flg'))->true();
            verify($model->hasErrors('created_at'))->true();
        });
        $this->specify('文字列項目検証', function () {
            $model = new PasswordReminder();
            $model->collation_key = 111;
            $model->validate();
            verify($model->hasErrors('collation_key'))->true();
        });
        $this->specify('文字数上限検証', function () {
            $model = new PasswordReminder();
            $model->collation_key = str_repeat('a', 201);
            $model->validate();
            verify($model->hasErrors('collation_key'))->true();
        });
        $this->specify('正常な値', function () {
            $model = new PasswordReminder();
            $model->key_id = 111;
            $model->created_at = 111;
            $model->key_flg = 1;
            $model->collation_key = str_repeat('a', 200);
            verify($model->validate())->true();
        });
    }
}
