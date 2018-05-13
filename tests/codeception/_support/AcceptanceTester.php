<?php
use app\models\manage\AdminMaster;


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    public function seeMatches($pattern, $value)
    {
        \PHPUnit_Framework_Assert::assertRegExp($pattern, $value);
    }

    public function getAdminId(){
        $admin_id = AdminMaster::findOne([
            'tenant_id' => 2,
            'login_id' => 'admin01'
        ])->id;
        return $admin_id;
    }
}
