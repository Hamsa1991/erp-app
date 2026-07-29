<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* Web UI routes */
$route['login'] = 'auth/login_page';
$route['login/submit'] = 'auth/login';
$route['logout'] = 'auth/logout_page';
$route['products'] = 'products/browse';
$route['products/data'] = 'products/browse_data';
$route['products/manage/data'] = 'products/manage_data';
$route['products/toggle/(:num)'] = 'products/toggle/$1';
$route['dashboard/products'] = 'products/manage';
$route['warehouses'] = 'warehouses/browse';
$route['warehouses/data'] = 'warehouses/browse_data';
$route['bills'] = 'bills/browse';
$route['bills/data'] = 'bills/browse_data';
$route['bills/detail/(:num)'] = 'bills/detail/$1';
$route['dashboard/bills/create'] = 'bills/create_form';

$route['/dashboard/bills/detail/(:num)'] = 'bills/detail/$1';

/* Web UI routes for Clients */
$route['clients'] = 'clients/index';
$route['dashboard/clients'] = 'clients/index';

/* Web UI routes for Warehouses */
$route['warehouses/manage'] = 'warehouses/manage';
$route['dashboard/warehouses'] = 'warehouses/manage';

/* Web UI routes for Reports */
$route['reports/low-stock'] = 'reports/low_stock';
$route['dashboard/reports/low-stock'] = 'reports/low_stock';

/* API routes for Reports */
$route['api/reports/low-stock'] = 'reports/low_stock_data';
$route['api/reports/low-stock/export'] = 'reports/low_stock_export';


$route['api/auth/login'] = 'auth/login';
$route['api/auth/logout'] = 'auth/logout';
$route['api/auth/me'] = 'auth/me';

$route['api/products'] = 'products/index';
$route['api/products/create'] = 'products/create';
$route['api/products/(:num)'] = 'products/show/$1';
$route['api/products/update/(:num)'] = 'products/update/$1';
$route['api/products/delete/(:num)'] = 'products/delete/$1';

$route['api/warehouses'] = 'warehouses/index';
$route['api/warehouses/accessible'] = 'warehouses/accessible';
$route['api/warehouses/create'] = 'warehouses/create';
$route['api/warehouses/(:num)'] = 'warehouses/show/$1';
$route['api/warehouses/update/(:num)'] = 'warehouses/update/$1';
$route['api/warehouses/delete/(:num)'] = 'warehouses/delete/$1';

$route['api/inventory'] = 'product_warehouse/index';
$route['api/inventory/low-stock'] = 'product_warehouse/low_stock';
$route['api/inventory/create'] = 'product_warehouse/create';
$route['api/inventory/upsert'] = 'product_warehouse/upsert';
$route['api/inventory/(:num)'] = 'product_warehouse/show/$1';
$route['api/inventory/update/(:num)'] = 'product_warehouse/update/$1';

$route['api/clients'] = 'clients/index';
$route['api/clients/create'] = 'clients/create';
$route['api/clients/(:num)'] = 'clients/show/$1';
$route['api/clients/update/(:num)'] = 'clients/update/$1';
$route['api/clients/delete/(:num)'] = 'clients/delete/$1';

$route['api/users'] = 'users/index';
$route['api/users/create'] = 'users/create';
$route['api/users/(:num)'] = 'users/show/$1';
$route['api/users/update/(:num)'] = 'users/update/$1';
$route['api/users/delete/(:num)'] = 'users/delete/$1';

$route['api/bills'] = 'bills/index';
$route['api/bills/create'] = 'bills/create';
$route['api/bills/(:num)'] = 'bills/show/$1';
$route['api/bills/delete/(:num)'] = 'bills/delete/$1';

$route['api/roles'] = 'roles/index';
$route['api/roles/permissions'] = 'roles/permissions';
