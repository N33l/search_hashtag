<?php

use Phinx\Migration\AbstractMigration;

class CreateTweetTable extends AbstractMigration
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
        $table = $this->table('tweet');
        $table->addColumn('tweet_id', 'string', array('limit' => 256,'null' => false))
            ->addColumn('tweet', 'string', array('limit' => 1024,'null' => true))
            ->addColumn('created_at', 'timestamp', array('null' => true))
            ->addIndex(array('tweet_id'), array('unique' => true))
           ->save();
    }
}
