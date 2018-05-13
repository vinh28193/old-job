<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/09/02
 * Time: 18:51
 */

namespace tests\codeception;

use Yii;
use yii\test\ActiveFixture;

class JmFixture extends ActiveFixture
{
    /**
     * DBの接続先はそのままに、connection classだけ変更してます
     */
    public function init()
    {
        $db = Yii::$app->db;
        $this->db = [
            'class' => 'yii\db\Connection',
            'dsn' => $db->dsn,
            'username' => $db->username,
            'password' => $db->password,
            'charset' => $db->charset,
        ];
        parent::init();
    }

    public function data()
    {
        return parent::getData();
    }

    /**
     * テーブルを初期化する
     */
    public function initTable()
    {
        $this->resetTable();
        $this->load();
    }
}
