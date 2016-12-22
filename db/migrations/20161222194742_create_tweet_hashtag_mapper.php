<?php

use Phinx\Migration\AbstractMigration;

class CreateTweetHashtagMapper extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('hash_tag_tweet_mapper');
        $table->addColumn('hash_id', 'integer', array('null' => false))
            ->addColumn('tweet_id', 'integer', array('null' => false))
            ->save();
    }
}
