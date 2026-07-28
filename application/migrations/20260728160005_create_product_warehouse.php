<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_product_warehouse extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'product_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
			'warehouse_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
			'quantity' => array(
				'type' => 'INT',
				'constraint' => 11,
				'default' => 0,
			),
			'alert_quantity' => array(
				'type' => 'INT',
				'constraint' => 11,
				'default' => 0,
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
		$this->dbforge->add_key(array('product_id', 'warehouse_id'), FALSE, TRUE);
		$this->dbforge->create_table('product_warehouse', TRUE);

		$this->db->query('ALTER TABLE `product_warehouse` ADD CONSTRAINT `fk_product_warehouse_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `product_warehouse` ADD CONSTRAINT `fk_product_warehouse_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->dbforge->drop_table('product_warehouse', TRUE);
	}
}
