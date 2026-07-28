<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_roles_and_permissions extends CI_Migration {

	public function up()
	{
		$now = date('Y-m-d H:i:s');

		$this->db->insert_batch('roles', array(
			array(
				'name' => 'admin',
				'description' => 'Full system administrator',
				'created_at' => $now,
				'updated_at' => $now,
			),
			array(
				'name' => 'user_warehouse',
				'description' => 'Warehouse operator with limited access',
				'created_at' => $now,
				'updated_at' => $now,
			),
		));

		$permissions = array(
			array('name' => 'Manage Users', 'slug' => 'manage_users', 'description' => 'Create, update and delete users'),
			array('name' => 'Manage Roles', 'slug' => 'manage_roles', 'description' => 'Assign roles and permissions'),
			array('name' => 'View Products', 'slug' => 'view_products', 'description' => 'View product catalog'),
			array('name' => 'Manage Products', 'slug' => 'manage_products', 'description' => 'Create, update and delete products'),
			array('name' => 'View Warehouses', 'slug' => 'view_warehouses', 'description' => 'View warehouses'),
			array('name' => 'Manage Warehouses', 'slug' => 'manage_warehouses', 'description' => 'Create, update and delete warehouses'),
			array('name' => 'Manage Inventory', 'slug' => 'manage_inventory', 'description' => 'Manage product stock per warehouse'),
			array('name' => 'View Clients', 'slug' => 'view_clients', 'description' => 'View clients'),
			array('name' => 'Manage Clients', 'slug' => 'manage_clients', 'description' => 'Create, update and delete clients'),
			array('name' => 'View Bills', 'slug' => 'view_bills', 'description' => 'View bills'),
			array('name' => 'Manage Bills', 'slug' => 'manage_bills', 'description' => 'Create, update and delete bills'),
		);

		foreach ($permissions as &$permission)
		{
			$permission['created_at'] = $now;
			$permission['updated_at'] = $now;
		}
		unset($permission);

		$this->db->insert_batch('permissions', $permissions);

		$admin_role = $this->db->get_where('roles', array('name' => 'admin'))->row();
		$warehouse_role = $this->db->get_where('roles', array('name' => 'user_warehouse'))->row();
		$all_permissions = $this->db->get('permissions')->result();

		$role_permissions = array();
		foreach ($all_permissions as $permission)
		{
			$role_permissions[] = array(
				'role_id' => $admin_role->id,
				'permission_id' => $permission->id,
			);
		}

		$warehouse_slugs = array(
			'view_products',
			'manage_products',
			'view_warehouses',
			'manage_inventory',
			'view_clients',
			'manage_clients',
			'view_bills',
			'manage_bills',
		);

		foreach ($all_permissions as $permission)
		{
			if (in_array($permission->slug, $warehouse_slugs, TRUE))
			{
				$role_permissions[] = array(
					'role_id' => $warehouse_role->id,
					'permission_id' => $permission->id,
				);
			}
		}

		$this->db->insert_batch('role_permissions', $role_permissions);

		$this->db->insert('users', array(
			'first_name' => 'System',
			'last_name' => 'Admin',
			'email' => 'admin@erp.local',
			'password' => password_hash('admin123', PASSWORD_BCRYPT),
			'is_active' => 1,
			'created_at' => $now,
			'updated_at' => $now,
		));

		$this->db->insert('user_roles', array(
			'user_id' => $this->db->insert_id(),
			'role_id' => $admin_role->id,
		));
	}

	public function down()
	{
		$this->db->empty_table('user_roles');
		$this->db->empty_table('users');
		$this->db->empty_table('role_permissions');
		$this->db->empty_table('permissions');
		$this->db->empty_table('roles');
	}
}
