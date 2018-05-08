<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product`.
 */
class m180508_062101_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('product', [
            'id' => $this->primaryKey(),
            'title'=>$this->string(255),
            'price'=>$this->integer(),
            'created_at'=>$this->dateTime(),
            'updated_at'=>$this->dateTime(),
            'user_id'=>$this->integer()
        ]);
        $this->createIndex(
            'idx-product-user_id',
            'product',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-product-user_id',
            'product',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-product-user_id',
            'product'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            'idx-product-user_id',
            'product'
        );
        $this->dropTable('product');
    }
}
