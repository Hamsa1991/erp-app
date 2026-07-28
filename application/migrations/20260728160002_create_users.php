<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_users extends CI_Migration {

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
			),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
			),
			'warehouse_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE,
			),
			'is_active' => array(
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
		$this->dbforge->add_key('email', FALSE, TRUE);
		$this->dbforge->create_table('users', TRUE);

		$this->dbforge->add_field(array(
			'user_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
			'role_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
			),
		));
		$this->dbforge->add_key(array('user_id', 'role_id'), TRUE);
		$this->dbforge->create_table('user_roles', TRUE);

		$this->db->query('ALTER TABLE `user_roles` ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `user_roles` ADD CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->dbforge->drop_table('user_roles', TRUE);
		$this->dbforge->drop_table('users', TRUE);
	}
}
