<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/login', 'PagesController@login')->name('login');
Route::post('/doLogin', 'LoginController@doLogin')->name('doLogin');
Route::get('/doLogout', 'LoginController@doLogout')->name('doLogout');

Route::get('/getDebugInfo', 'DebugController@getDebugInfo')->name('getDebugInfo');
Route::post('/storeAppSettings', 'AppSettingsController@storeAppSettings')->name('storeAppSettings');

Route::get('/', 'PagesController@index')->name('index');
Route::get('/dashboard', 'PagesController@dashboard')->name('dashboard');
Route::get('/search', 'PagesController@search')->name('search');
Route::get('/sales', 'PagesController@sales')->name('sales');
Route::get('/purchases', 'PagesController@purchases')->name('purchases');

Route::get('/handset-colors', 'PagesController@handsetColors')->name('handset-colors');
Route::group(['prefix' => 'handset-colors'], function ($router) {
    $router->post('change-active-status', 'DBObjects\HandsetColorsController@changeActiveStatus')->name('handset-colors.change-active-status');
    $router->post('save', 'DBObjects\HandsetColorsController@save')->name('handset-colors.save');
    $router->post('get-single', 'DBObjects\HandsetColorsController@getSingle')->name('handset-colors.get-single');
    $router->post('delete', 'DBObjects\HandsetColorsController@delete')->name('handset-colors.delete');
    $router->post('check-duplicate-name', 'DBObjects\HandsetColorsController@checkDuplicateName')->name('handset-colors.check-duplicate-name');
});

Route::get('/handset-models', 'PagesController@handsetModels')->name('handset-models');
Route::group(['prefix' => 'handset-models'], function ($router) {
    $router->post('change-active-status', 'DBObjects\HandsetModelsController@changeActiveStatus')->name('handset-models.change-active-status');
    $router->post('save', 'DBObjects\HandsetModelsController@save')->name('handset-models.save');
    $router->post('get-single', 'DBObjects\HandsetModelsController@getSingle')->name('handset-models.get-single');
    $router->post('delete', 'DBObjects\HandsetModelsController@delete')->name('handset-models.delete');
    $router->post('check-duplicate-name', 'DBObjects\HandsetModelsController@checkDuplicateName')->name('handset-models.check-duplicate-name');
});

Route::get('/handset-manufacturers', 'PagesController@handsetManufacturers')->name('handset-manufacturers');
Route::group(['prefix' => 'handset-manufacturers'], function ($router) {
    $router->post('change-active-status', 'DBObjects\HandsetManufacturersController@changeActiveStatus')->name('handset-manufacturers.change-active-status');
    $router->post('save', 'DBObjects\HandsetManufacturersController@save')->name('handset-manufacturers.save');
    $router->post('get-single', 'DBObjects\HandsetManufacturersController@getSingle')->name('handset-manufacturers.get-single');
    $router->post('delete', 'DBObjects\HandsetManufacturersController@delete')->name('handset-manufacturers.delete');
    $router->post('check-duplicate-name', 'DBObjects\HandsetManufacturersController@checkDuplicateName')->name('handset-manufacturers.check-duplicate-name');
});

Route::get('/handsets', 'PagesController@handsets')->name('handsets');
Route::group(['prefix' => 'handsets'], function ($router) {
    $router->post('change-active-status', 'DBObjects\HandsetsController@changeActiveStatus')->name('handsets.change-active-status');
    $router->post('save', 'DBObjects\HandsetsController@save')->name('handsets.save');
    $router->post('get-single', 'DBObjects\HandsetsController@getSingle')->name('handsets.get-single');
    $router->post('delete', 'DBObjects\HandsetsController@delete')->name('handsets.delete');
    $router->post('check-duplicate-name', 'DBObjects\HandsetsController@checkDuplicateName')->name('handsets.check-duplicate-name');
});

Route::get('/users', 'PagesController@users')->name('users');
Route::group(['prefix' => 'users'], function ($router) {
    $router->post('change-active-status', 'DBObjects\UsersController@changeActiveStatus')->name('users.change-active-status');
    $router->post('change-admin-status', 'DBObjects\UsersController@changeAdminStatus')->name('users.change-admin-status');
    $router->post('save', 'DBObjects\UsersController@save')->name('users.save');
    $router->post('get-single', 'DBObjects\UsersController@getSingle')->name('users.get-single');
    $router->post('delete', 'DBObjects\UsersController@delete')->name('users.delete');
    $router->post('check-duplicate-name', 'DBObjects\UsersController@checkDuplicateName')->name('users.check-duplicate-name');
});

Route::get('/customers', 'PagesController@customers')->name('customers');
Route::group(['prefix' => 'customers'], function ($router) {
    $router->post('change-active-status', 'DBObjects\CustomersController@changeActiveStatus')->name('customers.change-active-status');
    $router->post('save', 'DBObjects\CustomersController@save')->name('customers.save');
    $router->post('get-single', 'DBObjects\CustomersController@getSingle')->name('customers.get-single');
    $router->post('delete', 'DBObjects\CustomersController@delete')->name('customers.delete');
});

Route::get('/suppliers', 'PagesController@suppliers')->name('suppliers');
Route::group(['prefix' => 'suppliers'], function ($router) {
    $router->post('change-active-status', 'DBObjects\SuppliersController@changeActiveStatus')->name('suppliers.change-active-status');
    $router->post('save', 'DBObjects\SuppliersController@save')->name('suppliers.save');
    $router->post('get-single', 'DBObjects\SuppliersController@getSingle')->name('suppliers.get-single');
    $router->post('delete', 'DBObjects\SuppliersController@delete')->name('suppliers.delete');
});

Route::get('/phonestock', 'PagesController@phoneStock')->name('phonestock');
Route::group(['prefix' => 'phonestock'], function ($router) {
    $router->post('get-single', 'DBObjects\PhoneStockController@getSingle')->name('phonestock.get-single');
    $router->post('check-duplicate-imei', 'DBObjects\PhoneStockController@checkDuplicateIMEI')->name('phonestock.check-duplicate-imei');
});

Route::get('/purchases', 'PagesController@purchases')->name('purchases');
Route::group(['prefix' => 'purchase'], function ($router) {
    $router->post('save', 'DBObjects\PurchaseController@save')->name('purchase.save');
    $router->get('get-single', 'DBObjects\PurchaseController@getSingle')->name('purchase.get-single');
    $router->post('delete', 'DBObjects\PurchaseController@delete')->name('purchase.delete');
});

Route::get('/sales', 'PagesController@sales')->name('sales');
Route::group(['prefix' => 'sale'], function ($router) {
    $router->post('save', 'DBObjects\SalesController@save')->name('sales.save');
    $router->get('get-single', 'DBObjects\SalesController@getSingle')->name('sale.get-single');
    $router->post('delete', 'DBObjects\SalesController@delete')->name('sale.delete');
    $router->post('return-item', 'DBObjects\SalesController@returnItem')->name('sale.return-item');
});

Route::get('/app-settings', 'PagesController@appSettings')->name('app-settings');

Route::group(['prefix' => 'datatables'], function ($router) {
    Route::group(['prefix' => 'users'], function ($router) {
        $router->post('data', 'Datatables\UsersController@getData')->name('datatable.users.data');
    });

    Route::group(['prefix' => 'handset-colors'], function ($router) {
        $router->post('data', 'Datatables\HandsetColorsController@getData')->name('datatable.handset-colors.data');
    });

    Route::group(['prefix' => 'handset-models'], function ($router) {
        $router->post('data', 'Datatables\HandsetModelsController@getData')->name('datatable.handset-models.data');
    });

    Route::group(['prefix' => 'customers'], function ($router) {
        $router->post('data', 'Datatables\CustomersController@getData')->name('datatable.customers.data');
    });

    Route::group(['prefix' => 'handset-manufacturers'], function ($router) {
        $router->post('data', 'Datatables\HandsetManufacturersController@getData')->name('datatable.handset-manufacturers.data');
    });

    Route::group(['prefix' => 'handsets'], function ($router) {
        $router->post('data', 'Datatables\HandsetsController@getData')->name('datatable.handsets.data');
    });

    Route::group(['prefix' => 'suppliers'], function ($router) {
        $router->post('data', 'Datatables\SuppliersController@getData')->name('datatable.suppliers.data');
    });

    Route::group(['prefix' => 'phonestock'], function ($router) {
        $router->post('data', 'Datatables\PhoneStockController@getData')->name('datatable.phonestock.data');
        $router->post('available', 'Datatables\PhoneStockController@getAvailablePhoneData')->name('datatable.phonestock.available');
    });

    Route::group(['prefix' => 'purchases'], function ($router) {
        $router->post('data', 'Datatables\PurchasesController@getData')->name('datatable.purchases.data');
    });

    Route::group(['prefix' => 'sales'], function ($router) {
        $router->post('data', 'Datatables\SalesController@getData')->name('datatable.sales.data');
        $router->get('data', 'Datatables\SalesController@getData')->name('datatable.sales.data');
    });
});
//Auth::routes();
