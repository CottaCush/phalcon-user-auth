<?php

use Phinx\Migration\AbstractMigration;

class ResetPasswordTableAlter extends AbstractMigration
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
        if ($this->hasTable('user_password_resets')) {
            $table = $this->table('user_password_resets');

            $table->addColumn('date_of_expiry', 'integer', [
                'length' => 11,
                'after' => 'date_requested',
                'null' => true,
                'comment' => 'if value is null, then the token never expires',
            ])->update();
        }
    }
}
