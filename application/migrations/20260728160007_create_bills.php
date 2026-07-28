<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_bills extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'client_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
			'warehouse_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
			'total' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'default' => '0.00',
			),
			'discount' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'default' => '0.00',
			),
			'total_after_discount' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'default' => '0.00',
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
		$this->dbforge->create_table('bills', TRUE);

		$this->db->query('ALTER TABLE `bills` ADD CONSTRAINT `fk_bills_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `bills` ADD CONSTRAINT `fk_bills_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->dbforge->drop_table('bills', TRUE);
	}
}
