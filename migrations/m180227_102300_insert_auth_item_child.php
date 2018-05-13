<?php

use yii\db\Migration;
use yii\db\Query;

class m180227_102300_insert_auth_item_child extends Migration
{
    public function safeUp()
    {
        $ownerAuthExists = (new Query())->select('name')->from('auth_item')->where(['name' => 'owner_admin'])->exists();
        $corpAuthExists = (new Query())->select('name')->from('auth_item')->where(['name' => 'corp_admin'])->exists();
        $clientAuthExists = (new Query())->select('name')->from('auth_item')->where(['name' => 'client_admin'])->exists();

        // 追加
        if($ownerAuthExists && $corpAuthExists) {
            $this->insert('auth_item_child', [
                'parent' => 'owner_admin',
                'child' => 'corp_admin',
            ]);
        }
        if ($corpAuthExists && $clientAuthExists) {
            $this->insert('auth_item_child', [
                'parent' => 'corp_admin',
                'child' => 'client_admin',
            ]);
        }

        // 削除
        $this->delete('auth_item_child', [
            'parent' => 'corp_admin',
            'child' => 'isOwnApplication',
        ]);
        $this->delete('auth_item_child', [
            'parent' => 'corp_admin',
            'child' => 'isOwnClient',
        ]);
        $this->delete('auth_item_child', [
            'parent' => 'corp_admin',
            'child' => 'isOwnJob',
        ]);
    }

    public function safeDown()
    {
        $corpAuthExists = (new Query())->select('name')->from('auth_item')->where(['name' => 'corp_admin'])->exists();
        $ownAppAuthExists = (new Query())->select('name')->from('auth_item')->where(['name' => 'isOwnApplication'])->exists();
        $ownClientAuthExists = (new Query())->select('name')->from('auth_item')->where(['name' => 'isOwnClient'])->exists();
        $ownJobAuthExists = (new Query())->select('name')->from('auth_item')->where(['name' => 'isOwnJob'])->exists();

        // 削除
        $this->delete('auth_item_child',[
            'parent' => 'owner_admin',
            'child' => 'corp_admin',
        ]);
        $this->delete('auth_item_child',[
            'parent' => 'corp_admin',
            'child' => 'client_admin',
        ]);

        //追加
        if ($corpAuthExists) {
            if ($ownAppAuthExists) {
                $this->insert('auth_item_child', [
                    'parent' => 'corp_admin',
                    'child' => 'isOwnApplication',
                ]);
            }
            if ($ownClientAuthExists) {
                $this->insert('auth_item_child', [
                    'parent' => 'corp_admin',
                    'child' => 'isOwnClient',
                ]);
            }
            if ($ownJobAuthExists) {
                $this->insert('auth_item_child', [
                    'parent' => 'corp_admin',
                    'child' => 'isOwnJob',
                ]);
            }
        }
    }
}
