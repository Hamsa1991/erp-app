<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_warehouses extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 150,
			),
			'address' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('warehouses', TRUE);

		$this->db->query('ALTER TABLE `users` ADD CONSTRAINT `fk_users_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->db->query('ALTER TABLE `users` DROP FOREIGN KEY `fk_users_warehouse`');
		$this->dbforge->drop_table('warehouses', TRUE);
	}
}
