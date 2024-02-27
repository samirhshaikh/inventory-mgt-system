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
Route::get("/login", "PagesController@login")->name("login");
Route::post("/doLogin", "LoginController@doLogin")->name("doLogin");
Route::get("/doLogout", "LoginController@doLogout")->name("doLogout");

Route::get("/getDebugInfo", "DebugController@getDebugInfo")->name(
    "getDebugInfo"
);
Route::post(
    "/storeAppSettings",
    "AppSettingsController@storeAppSettings"
)->name("storeAppSettings");

Route::get("/", "PagesController@index")->name("index");
Route::get("/dashboard", "PagesController@dashboard")->name("dashboard");
Route::get("/search", "PagesController@search")->name("search");

Route::group(["prefix" => "handset-colors"], function ($router) {
    $router->get("", "PagesController@handsetColors")->name("handset-colors");
    $router
        ->get("data", "DBObjects\HandsetColorsController@getData")
        ->name("handset-colors.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\HandsetColorsController@changeActiveStatus"
        )
        ->name("handset-colors.change-active-status");
    $router
        ->post("save", "DBObjects\HandsetColorsController@save")
        ->name("handset-colors.save");
    $router
        ->get("get-single", "DBObjects\HandsetColorsController@getSingle")
        ->name("handset-colors.get-single");
    $router
        ->post("delete", "DBObjects\HandsetColorsController@delete")
        ->name("handset-colors.delete");
    $router
        ->post(
            "check-duplicate-name",
            "DBObjects\HandsetColorsController@checkDuplicateName"
        )
        ->name("handset-colors.check-duplicate-name");
});

Route::group(["prefix" => "handset-models"], function ($router) {
    $router->get("", "PagesController@handsetModels")->name("handset-models");
    $router
        ->get("data", "DBObjects\HandsetModelsController@getData")
        ->name("handset-models.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\HandsetModelsController@changeActiveStatus"
        )
        ->name("handset-models.change-active-status");
    $router
        ->post("save", "DBObjects\HandsetModelsController@save")
        ->name("handset-models.save");
    $router
        ->get("get-single", "DBObjects\HandsetModelsController@getSingle")
        ->name("handset-models.get-single");
    $router
        ->post("delete", "DBObjects\HandsetModelsController@delete")
        ->name("handset-models.delete");
    $router
        ->post(
            "check-duplicate-name",
            "DBObjects\HandsetModelsController@checkDuplicateName"
        )
        ->name("handset-models.check-duplicate-name");
});

Route::group(["prefix" => "handset-manufacturers"], function ($router) {
    $router
        ->get("", "PagesController@handsetManufacturers")
        ->name("handset-manufacturers");
    $router
        ->get("data", "DBObjects\HandsetManufacturersController@getData")
        ->name("handset-manufacturers.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\HandsetManufacturersController@changeActiveStatus"
        )
        ->name("handset-manufacturers.change-active-status");
    $router
        ->post("save", "DBObjects\HandsetManufacturersController@save")
        ->name("handset-manufacturers.save");
    $router
        ->get(
            "get-single",
            "DBObjects\HandsetManufacturersController@getSingle"
        )
        ->name("handset-manufacturers.get-single");
    $router
        ->post("delete", "DBObjects\HandsetManufacturersController@delete")
        ->name("handset-manufacturers.delete");
    $router
        ->post(
            "check-duplicate-name",
            "DBObjects\HandsetManufacturersController@checkDuplicateName"
        )
        ->name("handset-manufacturers.check-duplicate-name");
});

Route::group(["prefix" => "parts"], function ($router) {
    $router->get("", "PagesController@parts")->name("parts");
    $router
        ->get("data", "DBObjects\PartsController@getData")
        ->name("parts.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\PartsController@changeActiveStatus"
        )
        ->name("parts.change-active-status");
    $router->post("save", "DBObjects\PartsController@save")->name("parts.save");
    $router
        ->get("get-single", "DBObjects\PartsController@getSingle")
        ->name("parts.get-single");
    $router
        ->post("delete", "DBObjects\PartsController@delete")
        ->name("parts.delete");
    $router
        ->post(
            "check-duplicate-name",
            "DBObjects\PartsController@checkDuplicateName"
        )
        ->name("parts.check-duplicate-name");
});

Route::group(["prefix" => "handsets"], function ($router) {
    $router->get("", "PagesController@handsets")->name("handsets");
    $router
        ->get("data", "DBObjects\HandsetsController@getData")
        ->name("handsets.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\HandsetsController@changeActiveStatus"
        )
        ->name("handsets.change-active-status");
    $router
        ->post("save", "DBObjects\HandsetsController@save")
        ->name("handsets.save");
    $router
        ->get("get-single", "DBObjects\HandsetsController@getSingle")
        ->name("handsets.get-single");
    $router
        ->post("delete", "DBObjects\HandsetsController@delete")
        ->name("handsets.delete");
    $router
        ->post(
            "check-duplicate-name",
            "DBObjects\HandsetsController@checkDuplicateName"
        )
        ->name("handsets.check-duplicate-name");
});

Route::group(["prefix" => "users"], function ($router) {
    $router->get("", "PagesController@users")->name("users");
    $router
        ->get("data", "DBObjects\UsersController@getData")
        ->name("users.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\UsersController@changeActiveStatus"
        )
        ->name("users.change-active-status");
    $router
        ->post(
            "change-admin-status",
            "DBObjects\UsersController@changeAdminStatus"
        )
        ->name("users.change-admin-status");
    $router->post("save", "DBObjects\UsersController@save")->name("users.save");
    $router
        ->get("get-single", "DBObjects\UsersController@getSingle")
        ->name("users.get-single");
    $router
        ->post("delete", "DBObjects\UsersController@delete")
        ->name("users.delete");
    $router
        ->post(
            "check-duplicate-name",
            "DBObjects\UsersController@checkDuplicateName"
        )
        ->name("users.check-duplicate-name");
});

Route::group(["prefix" => "customers"], function ($router) {
    $router->get("", "PagesController@customers")->name("customers");
    $router
        ->get("data", "DBObjects\CustomersController@getData")
        ->name("customers.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\CustomersController@changeActiveStatus"
        )
        ->name("customers.change-active-status");
    $router
        ->post("save", "DBObjects\CustomersController@save")
        ->name("customers.save");
    $router
        ->get("get-single", "DBObjects\CustomersController@getSingle")
        ->name("customers.get-single");
    $router
        ->post("delete", "DBObjects\CustomersController@delete")
        ->name("customers.delete");
});

Route::group(["prefix" => "parts_suppliers"], function ($router) {
    $router
        ->get("", "PagesController@parts_suppliers")
        ->name("parts_suppliers");
    $router
        ->get("data", "DBObjects\PartsSuppliersController@getData")
        ->name("parts_suppliers.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\PartsSuppliersController@changeActiveStatus"
        )
        ->name("parts_suppliers.change-active-status");
    $router
        ->post("save", "DBObjects\PartsSuppliersController@save")
        ->name("parts_suppliers.save");
    $router
        ->get("get-single", "DBObjects\PartsSuppliersController@getSingle")
        ->name("parts_suppliers.get-single");
    $router
        ->post("delete", "DBObjects\PartsSuppliersController@delete")
        ->name("parts_suppliers.delete");
});

Route::group(["prefix" => "suppliers"], function ($router) {
    $router->get("", "PagesController@suppliers")->name("suppliers");
    $router
        ->get("data", "DBObjects\MobileSuppliersController@getData")
        ->name("suppliers.data");
    $router
        ->post(
            "change-active-status",
            "DBObjects\MobileSuppliersController@changeActiveStatus"
        )
        ->name("suppliers.change-active-status");
    $router
        ->post("save", "DBObjects\MobileSuppliersController@save")
        ->name("suppliers.save");
    $router
        ->get("get-single", "DBObjects\MobileSuppliersController@getSingle")
        ->name("suppliers.get-single");
    $router
        ->post("delete", "DBObjects\MobileSuppliersController@delete")
        ->name("suppliers.delete");
});

Route::group(["prefix" => "phonestock"], function ($router) {
    $router->get("", "PagesController@phoneStock")->name("phonestock");
    $router
        ->get("data", "DBObjects\PhoneStockController@getData")
        ->name("phonestock.data");
    $router
        ->get(
            "available",
            "DBObjects\PhoneStockController@getAvailablePhoneData"
        )
        ->name("phonestock.available");
    $router
        ->post("get-single", "DBObjects\PhoneStockController@getSingle")
        ->name("phonestock.get-single");
    $router
        ->post("validate-imei", "DBObjects\PhoneStockController@validateIMEI")
        ->name("phonestock.validate-imei");
});

Route::group(["prefix" => "repairs"], function ($router) {
    $router->get("", "PagesController@repairs")->name("repairs");
    $router
        ->get("data", "DBObjects\RepairsController@getData")
        ->name("repairs.data");
    $router
        ->post("save", "DBObjects\RepairsController@save")
        ->name("repair.save");
    $router
        ->get("get-single", "DBObjects\RepairsController@getSingle")
        ->name("repair.get-single");
    $router
        ->post("delete", "DBObjects\RepairsController@delete")
        ->name("repair.delete");
});

Route::group(["prefix" => "purchase"], function ($router) {
    $router->get("", "PagesController@purchases")->name("purchases");
    $router
        ->get("data", "DBObjects\PurchaseController@getData")
        ->name("purchases.data");
    $router
        ->post("save", "DBObjects\PurchaseController@save")
        ->name("purchase.save");
    $router
        ->get("get-single", "DBObjects\PurchaseController@getSingle")
        ->name("purchase.get-single");
    $router
        ->post("delete", "DBObjects\PurchaseController@delete")
        ->name("purchase.delete");
});

Route::group(["prefix" => "sale"], function ($router) {
    $router->get("", "PagesController@sales")->name("sales");
    $router
        ->get("data", "DBObjects\SalesController@getData")
        ->name("sales.data");
    $router->post("save", "DBObjects\SalesController@save")->name("sale.save");
    $router
        ->get("get-single", "DBObjects\SalesController@getSingle")
        ->name("sale.get-single");
    $router
        ->post("delete", "DBObjects\SalesController@delete")
        ->name("sale.delete");
    $router
        ->post("return-item", "DBObjects\SalesController@returnItem")
        ->name("sale.return-item");
    $router
        ->get("get-pdf-invoice", "DBObjects\SalesController@getPDFInvoice")
        ->name("sale.get-pdf-invoice");
});

Route::group(["prefix" => "tradein"], function ($router) {
    $router->post("delete", "TradeInController@delete")->name("tradein.delete");
});

Route::group(["prefix" => "dashboard"], function ($router) {
    $router
        ->get("get-stats", "DashboardController@getStats")
        ->name("dashboard.get-stats");
});

Route::get("/app-settings", "PagesController@appSettings")->name(
    "app-settings"
);

//Auth::routes();
