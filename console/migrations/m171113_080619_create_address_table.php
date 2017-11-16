<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m171113_080619_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
//        $this->createTable('address', [
//            'id' => $this->primaryKey(),
//            'name'=>$this->string()->comment('收货人'),
//            'province'=>$this->string()->comment('省份'),
//            'city'=>$this->string()->comment('市'),
//            'county'=>$this->string()->comment('县'),
//            'address'=>$this->string()->comment('详细地址'),
//            'phone'=>$this->string()->comment('手机号码'),
//            'status'=>$this->integer()->comment('是否为默认路径'),
//        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
