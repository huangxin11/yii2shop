<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m171103_035855_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name'=>$this->string('50')->comment('名称'),
            'intro'=>$this->text()->comment('简介'),
            'logo'=>$this->string('255')->comment('logo图片'),
            'sort'=>$this->integer()->comment('排序'),
            'start'=>$this->integer()->comment('状态，-1删除，0隐藏，1显示'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
