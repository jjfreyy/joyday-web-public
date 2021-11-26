<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->add("/", "Dashboard::index", ["filter" => "check_session"]);
$routes->add("dashboard", "Dashboard::index", ["filter" => "check_session"]);
$routes->add('login', 'Login::index');
$routes->add("login/process", "Login::process");

$routes->group("master", ["filter" => "check_session"], function($routes) {
	$routes->add("barang", "master/Barang::index");
	$routes->group("barang", function($routes) {
		$routes->add("save", "master/Barang::save");
		$routes->add("fetch", "master/Barang::fetch");
	});

	$routes->add("asset", "master/Asset::index");
	$routes->group("asset", function($routes) {
		$routes->add("save", "master/Asset::save");
		$routes->add("fetch", "master/Asset::fetch");
	});

	$routes->add("pelanggan", "master/Pelanggan::index");
	$routes->group("pelanggan", function($routes) {
		$routes->add("save", "master/Pelanggan::save");
		$routes->add("fetch", "master/Pelanggan::fetch");
	});

	$routes->add("distributor", "master/Distributor::index");
	$routes->group("distributor", function($routes) {
		$routes->add("save", "master/Distributor::save");
		$routes->add("fetch", "master/Distributor::fetch");
	});
	
	$routes->add("gudang", "master/Gudang::index");
	$routes->group("gudang", function($routes) {
		$routes->add("save", "master/Gudang::save");
		$routes->add("fetch", "master/Gudang::fetch");
	});
});

$routes->group("input", ["filter" => "check_session"], function($routes) {
	$routes->add("pesanan", "input/Pesanan::index");
	$routes->group("pesanan", function($routes) {
		$routes->add("save", "input/Pesanan::save");
		$routes->add("fetch", "input/Pesanan::fetch");
	});
	
	$routes->add("barang_masuk", "input/Barang_Masuk::index");
	$routes->group("barang_masuk", function($routes) {
		$routes->add("save", "input/Barang_Masuk::save");
		$routes->add("fetch", "input/Barang_Masuk::fetch");
	});
	
	$routes->add("barang_keluar", "input/Barang_Keluar::index");
	$routes->group("barang_keluar", function($routes) {
		$routes->add("save", "input/Barang_Keluar::save");
		$routes->add("fetch", "input/Barang_Keluar::fetch");
	});
	
	$routes->add("mutasi", "input/Mutasi::index");
	$routes->group("mutasi", function($routes) {
		$routes->add("save", "input/Mutasi::save");
		$routes->add("fetch", "input/Mutasi::fetch");
	});
});

$routes->group("daftar", ["filter" => "check_session"], function($routes) {
	$routes->add("barang", "daftar/Barang::index");
	$routes->group("barang", function($routes) {
		$routes->add("fetch", "daftar/Barang::fetch");
		$routes->add("delete", "daftar/Barang::delete");
	});
	
	$routes->add("asset", "daftar/Asset::index");
	$routes->group("asset", function($routes) {
		$routes->add("fetch", "daftar/Asset::fetch");
		$routes->add("delete", "daftar/Asset::delete");
		$routes->add("export_to_excel", "daftar/Asset::export_to_excel");
	});

	$routes->add("pelanggan", "daftar/Pelanggan::index");
	$routes->group("pelanggan", function($routes) {
		$routes->add("fetch", "daftar/Pelanggan::fetch");
		$routes->add("delete", "daftar/Pelanggan::delete");
	});

	$routes->add("distributor", "daftar/Distributor::index");
	$routes->group("distributor", function($routes) {
		$routes->add("fetch", "daftar/Distributor::fetch");
		$routes->add("delete", "daftar/Distributor::delete");
	});

	$routes->add("gudang", "daftar/Gudang::index");
	$routes->group("gudang", function($routes) {
		$routes->add("fetch", "daftar/Gudang::fetch");
		$routes->add("delete", "daftar/Gudang::delete");
	});
});

$routes->group("tampil", ["filter" => "check_session"], function($routes) {
	$routes->add("pesanan", "tampil/Pesanan::index");
	$routes->group("pesanan", function($routes) {
		$routes->add("fetch", "tampil/Pesanan::fetch");
		$routes->add("delete", "tampil/Pesanan::delete");
	});
	
	$routes->add("barang_masuk", "tampil/Barang_Masuk::index");
	$routes->group("barang_masuk", function($routes) {
		$routes->add("fetch", "tampil/Barang_Masuk::fetch");
		$routes->add("delete", "tampil/Barang_Masuk::delete");
	});
	
	$routes->add("barang_keluar", "tampil/Barang_Keluar::index");
	$routes->group("barang_keluar", function($routes) {
		$routes->add("fetch", "tampil/Barang_Keluar::fetch");
		$routes->add("delete", "tampil/Barang_Keluar::delete");
		$routes->add("print", "tampil/Barang_Keluar::print");
	});

	$routes->add("mutasi", "tampil/Mutasi::index");
	$routes->group("mutasi", function($routes) {
		$routes->add("fetch", "tampil/Mutasi::fetch");
		$routes->add("delete", "tampil/Mutasi::delete");
	});
});

$routes->group("laporan", ["filter" => "check_session"], function($routes) {
	$routes->add("barang_keluar", "laporan/Barang_Keluar::index");
	$routes->group("barang_keluar", function($routes) {
		$routes->add("print", "laporan/Barang_Keluar::print");
	});
	$routes->add("barang_masuk", "laporan/Barang_Masuk::index");
	$routes->group("barang_masuk", function($routes) {
		$routes->add("print", "laporan/Barang_Masuk::print");
	});
	$routes->add("mutasi", "laporan/Mutasi::index");
	$routes->group("mutasi", function($routes) {
		$routes->add("print", "laporan/Mutasi::print");
	});
	$routes->add("asset", "laporan/Asset::index");
	$routes->group("asset", function($routes) {
		$routes->add("print", "laporan/Asset::print");
	});
	$routes->add("penggantian_freezer", "laporan/Penggantian_Freezer::index");
	$routes->group("penggantian_freezer", function($routes) {
		$routes->add("print", "laporan/Penggantian_Freezer::print");
	});
});

$routes->group("sistem", ["filter" => "check_session"], function($routes) {
	$routes->add("tambah_user", "sistem/Tambah_User::index");
	$routes->group("tambah_user", function($routes) {
		$routes->add("save", "sistem/Tambah_User::save");
	});

	$routes->add("edit_akun", "sistem/Edit_Akun::index");
	$routes->group("edit_akun", function($routes) {
		$routes->add("save", "sistem/Edit_Akun::save");
	});
	
	$routes->add("hak_akses", "sistem/Hak_Akses::index");
	$routes->group("hak_akses", function($routes) {
		$routes->add("save", "sistem/Hak_Akses::save");
	});
});

$routes->add("logout", "Logout::index");
$routes->add("404", "Unknown::index");

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
