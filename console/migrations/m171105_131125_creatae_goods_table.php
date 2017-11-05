<?php

use yii\db\Migration;

class m171105_131125_creatae_goods_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->comment('商品名称'),
            'sn'=>$this->string()->comment('货号'),
            'logo'=>$this->string()->comment('logo图片'),
            'goods_category_id'=>$this->integer()->comment('商品分类id'),
            'brand_id'=>$this->integer()->comment('品牌分类'),
            'market_price'=>$this->decimal(10,2)->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->comment('商品价格'),
            'stock'=>$this->integer()->comment('库存'),
            'is_on_sale'=>$this->integer(1)->comment('是否在售，1在售，0下架'),
            'status'=>$this->integer()->comment('状态，0回收站，1正常'),
            'sort'=>$this->integer()->comment('排序'),
            'create_time'=>$this->integer()->comment('添加时间'),
            'view_times'=>$this->integer()->comment('浏览次数'),
        ]);

    }

    public function safeDown()
    {
        echo "m171105_131125_creatae_goods_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171105_131125_creatae_goods_table cannot be reverted.\n";

        return false;
    }
    */
}
