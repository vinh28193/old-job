<?php
use app\modules\manage\models\Manager;
use app\modules\manage\models\MenuCategory;
use tests\codeception\unit\JmTestCase;

/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:35
 */
class ManagerTest extends JmTestCase
{
    public function testFindIdentity()
    {
        $model = new Manager();
        verify($model->findIdentity(1))->equals(Manager::findOne(1));
    }

    // 不要
//    public function testGetAuthKey(){}
//    public function testGenerateAuthKey(){}
//    public function testFindIdentityByAccessToken(){}
//    public function testGenerateAccessToken(){}

    public function testValidatePassword()
    {
        $model = new Manager();
        $model->password = 'aaaaaaa';
        verify($model->validatePassword('bbbbbbb'))->false();
        verify($model->validatePassword('aaaaaaa'))->true();
    }

    public function testGetId()
    {
        $this->setIdentity('owner_admin');
        $model = new Manager();
        $model->id = $this->getIdentity()->id;
        verify($model->getId())->equals($this->getIdentity()->id);
    }

    public function testFindByLoginId()
    {
        $this->specify('管理者共通及び運営元管理者', function () {
            $admin = $this->getManager('owner_admin');
            $this->checkCorrectCase($admin);

            verify('ログインIDが不正', Manager::findByLoginId('aaaaaaaaaa'))->null();
            $admin->valid_chk = Manager::INVALID;
            $admin->save(false);
            verify('管理者が無効', Manager::findByLoginId($admin->login_id))->null();
        });

        $this->specify('掲載企業管理者権限', function () {
            $admin = $this->getManager('client_admin');
            $this->checkCorrectCase($admin);

            // データ不整合
            $clientId = $admin->client_master_id;
            $admin->client_master_id = 2147483647;
            $admin->save(false);
            verify('掲載企業IDが不正なレコード', Manager::findByLoginId($admin->login_id))->null();
            $admin->client_master_id = $clientId;
            $admin->corp_master_id = 2147483647;
            $admin->save(false);
            verify('代理店IDが不正なレコード', Manager::findByLoginId($admin->login_id))->null();
        });

        $this->specify('代理店管理者権限', function () {
            $admin = $this->getManager('corp_admin');
            $this->checkCorrectCase($admin);

            // データ不整合
            $admin->corp_master_id = 2147483647;
            $admin->save(false);
            verify('代理店IDが不正なレコード', Manager::findByLoginId($admin->login_id))->null();
        });
    }

    /**
     * $adminが有効かつ正常な場合のfindByLoginIdのtest case
     * @param Manager $admin
     */
    private function checkCorrectCase($admin)
    {
        $actual = Manager::findByLoginId($admin->login_id);
        verify('Managerインスタンスが返ってくる', $actual)->isInstanceOf(Manager::className());
        verify('idが正しい', $actual->id)->equals($admin->id);
    }

    public function testValidateAuthKey()
    {
        $model = new Manager();
        verify($model->validateAuthKey('jm2'))->true();
        verify($model->validateAuthKey('jm22'))->false();
    }

    public function testGetUsername()
    {
        $model = new Manager();
        $model->name_mei = 'Mei';
        $model->name_sei = 'Sei';
        verify($model->getUsername())->equals('Sei Mei');
    }

    public function testGetMyRole()
    {
        $this->setIdentity('owner_admin');
        $model = new Manager();
        $model->id = $this->getIdentity()->id;
        verify($model->getMyRole())->equals('owner_admin');
    }

    // 不要
//    public static function testGetRoles(){}

    public function testGetMyMenu()
    {
        $this->setIdentity('owner_admin');
        $model = new Manager();
        $model->id = $this->getIdentity()->id;
        verify($model->getMyMenu())->equals(MenuCategory::getMyMenu($this->getIdentity()->id));
    }

    /**
     * isOwner, IsCorp, isClientのtest
     */
    public function testIsAuthors()
    {
        $this->setIdentity('owner_admin');
        verify($this->getIdentity()->isOwner())->true();
        verify($this->getIdentity()->isCorp())->false();
        verify($this->getIdentity()->isClient())->false();

        $this->setIdentity('corp_admin');
        verify($this->getIdentity()->isOwner())->false();
        verify($this->getIdentity()->isCorp())->true();
        verify($this->getIdentity()->isClient())->false();

        $this->setIdentity('client_admin');
        verify($this->getIdentity()->isOwner())->false();
        verify($this->getIdentity()->isCorp())->false();
        verify($this->getIdentity()->isClient())->true();
    }
}
