<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_clients extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'first_name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
			),
			'last_name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			),
			'phone' => array(
				'type' => 'VARCHAR',
				'constraint' => 30,
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
		$this->dbforge->create_table('clients', TRUE);
	}

	public function down()
	{
		$this->dbforge->drop_table('clients', TRUE);
	}
}
