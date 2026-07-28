<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_bill_details extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'bill_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
			'product_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
			'quantity' => array(
				'type' => 'INT',
				'constraint' => 11,
				'default' => 1,
			),
			'price' => array(
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
		$this->dbforge->create_table('bill_details', TRUE);

		$this->db->query('ALTER TABLE `bill_details` ADD CONSTRAINT `fk_bill_details_bill` FOREIGN KEY (`bill_id`) REFERENCES `bills`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `bill_details` ADD CONSTRAINT `fk_bill_details_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->dbforge->drop_table('bill_details', TRUE);
	}
}
