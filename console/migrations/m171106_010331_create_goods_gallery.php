<?php

use yii\db\Migration;

class m171106_010331_create_goods_gallery extends Migration
{
    public function safeUp()
    {
        $this->createTable('goods_gallery', [
            'id' => $this->primaryKey(),
            'goods_id'=>$this->integer()->comment('商品id'),
            'path'=>$this->string()->comment('图片地址'),

        ]);

    }

    public function safeDown()
    {
        echo "m171106_010331_create_goods_gallery cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171106_010331_create_goods_gallery cannot be reverted.\n";

        return false;
    }
    */
}
