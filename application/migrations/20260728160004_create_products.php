<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_products extends CI_Migration {

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
			'code' => array(
				'type' => 'VARCHAR',
				'constraint' => 50,
			),
			'price' => array(
				'type' => 'DECIMAL',
				'constraint' => '10,2',
				'default' => '0.00',
			),
			'is_available' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
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
		$this->dbforge->add_key('code', FALSE, TRUE);
		$this->dbforge->create_table('products', TRUE);
	}

	public function down()
	{
		$this->dbforge->drop_table('products', TRUE);
	}
}
