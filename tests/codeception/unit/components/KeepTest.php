<?php
namespace components;


class KeepTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // Adding Test
    public function testAddJobId()
    {
        $keepComp = new \app\common\Keep();

        // クリアしておく
        $keepComp->clear();

        // IDが数字以外の場合はfalseを返す
        verify($keepComp->addJobId('abc'))->equals(false);
        verify($keepComp->addJobId('ABC'))->equals(false);
        verify($keepComp->addJobId('1.04'))->equals(false);

        // IDが数字のときはtrueを返す
        verify($keepComp->addJobId('1'))->equals(true);
        verify($keepComp->addJobId(2))->equals(true);

        // 既にリストに入っている場合はfalseを返す
        verify($keepComp->addJobId('1'))->equals(false);
        verify($keepComp->addJobId(2))->equals(false);

        // {maxKeepLimitCount}件以上はリストに入れることが出来ない ↑ので既に2件入っている
        for($i=3; $i<=$keepComp::KEEP_LIMIT; $i++){
            $keepComp->addJobId($i);
        }
        verify($keepComp->addJobId($keepComp::KEEP_LIMIT+1))->equals(false);

        // 上記実行により全{maxKeepLimitCount}件登録されている
        verify(count($keepComp->keepJobNos))->equals($keepComp::KEEP_LIMIT);

        // 最終結果
        verify($keepComp->keepJobNos)->equals([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]);
    }

    // Removing Test
    public function testRemoveJobId()
    {
        $keepComp = new \app\common\Keep();

        // クリアしておく
        $keepComp->clear();

        // IDが数字以外の場合はfalseを返す
        verify($keepComp->removeKeepJobId('abc'))->equals(false);
        verify($keepComp->removeKeepJobId('ABC'))->equals(false);
        verify($keepComp->removeKeepJobId('1.04'))->equals(false);
        verify($keepComp->removeKeepJobId())->equals(false);

        //10件登録しておく
        for($i=1; $i<=10; $i++){
            $keepComp->addJobId($i);
        }

        // 登録した件数が10件 一応チェック
        verify(count($keepComp->keepJobNos))->equals(10);

        // 登録されているIDを1件削除
        verify($keepComp->removeKeepJobId(5))->equals(true);

        // 1件削除したので9件になっている
        verify(count($keepComp->keepJobNos))->equals(9);

        // 削除したID:5を再度登録できるか
        verify($keepComp->addJobId(5))->equals(true);

        // 存在しないIDを指定しても何もなし
        verify($keepComp->removeKeepJobId(999))->equals(true);

        // 最終結果
        verify($keepComp->keepJobNos)->equals([1, 2, 3, 4, 6, 7, 8, 9, 10, 5]);

    }

}