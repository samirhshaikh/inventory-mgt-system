<?php

namespace App\Http\Controllers;

use App\Datatables\CustomerSalesDatatable;
use App\Datatables\HandsetColorsDatatable;
use App\Datatables\HandsetManufacturersDatatable;
use App\Datatables\HandsetModelsDatatable;
use App\Datatables\HandsetsDatatable;
use App\Datatables\SalesDatatable;
use App\Datatables\PurchasesDatatable;
use App\Datatables\UsersDatatable;
use App\Datatables\SuppliersDatatable;
use App\Datatables\PhoneStockDatatable;
use Inertia\Inertia;

class PagesController extends Controller
{
    public function index()
    {
        if (!session("user", false)) {
            return redirect("login");
        } else {
            return redirect("dashboard");
        }
    }

    public function login()
    {
        return Inertia::render("Login", []);
    }

    public function dashboard()
    {
        return Inertia::render("Dashboard", []);
    }

    public function handsetColors()
    {
        $table = new HandsetColorsDatatable();

        return Inertia::render("ObjectTypeName", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }

    public function handsetModels()
    {
        $table = new HandsetModelsDatatable();

        return Inertia::render("ObjectTypeName", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }

    public function handsetManufacturers()
    {
        $table = new HandsetManufacturersDatatable();

        return Inertia::render("ObjectTypeName", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }

    public function phoneStock()
    {
        $table = new PhoneStockDatatable();

        return Inertia::render("PhoneStock", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }

    public function purchases()
    {
        $table = new PurchasesDatatable();

        return Inertia::render("Purchases", [
            "columns" => $table->columns,
            "child_columns" => $table->child_columns,
            "options" => $table->options,
        ]);
    }

    public function sales()
    {
        $table = new SalesDatatable();

        return Inertia::render("Sales", [
            "columns" => $table->columns,
            "child_columns" => $table->child_columns,
            "options" => $table->options,
        ]);
    }

    public function customerSales()
    {
        $table = new CustomerSalesDatatable();

        return Inertia::render("CustomerSales", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }

    public function suppliers()
    {
        $table = new SuppliersDatatable();

        return Inertia::render("Suppliers", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }

    public function handsets()
    {
        $table = new HandsetsDatatable();

        return Inertia::render("Handsets", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }

    public function users()
    {
        $table = new UsersDatatable();

        return Inertia::render("Users", [
            "columns" => $table->columns,
            "options" => $table->options,
        ]);
    }
}
