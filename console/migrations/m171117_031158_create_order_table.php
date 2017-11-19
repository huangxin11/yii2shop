<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m171117_031158_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->comment('用户id'),
            'name'=>$this->string()->comment('收货人'),
            'province'=>$this->string()->comment('省'),
            'city'=>$this->string()->comment('市'),
            'area'=>$this->string()->comment('县'),
            'address'=>$this->string()->comment('详细地址'),
            'tel'=>$this->string()->comment('电话号码'),
            'delivery_id'=>$this->integer()->comment('配送方式id'),
            'delivery_name'=>$this->string()->comment('配送方式名称'),
            'delivery_price'=>$this->decimal(10,2)->comment('配送价格'),
            'payment_id'=>$this->integer()->comment('支付id'),
            'payment_name'=>$this->string()->comment('支付方式名称'),
            'total'=>$this->decimal(10,2)->comment('订单金额'),
            'status'=>$this->integer()->comment('状态'),
            'trade_no'=>$this->string()->comment('第三方支付交易号'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order');
    }
}
