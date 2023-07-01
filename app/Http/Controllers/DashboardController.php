<?php

namespace App\Http\Controllers;

use App\Services\PhoneStockService;
use App\Services\PurchaseService;
use App\Services\SalesService;

class DashboardController extends BaseController
{
    public function getStats()
    {
        $phonestock_service = new PhoneStockService();
        $phonestock_data = $phonestock_service->getPhonesStatsForStatus();

        $today = date("Y-m-d");

        $current_week = new \DateTime();
        $day_of_week = $current_week->format("w") - 1;
        $current_week->modify("$day_of_week day");

        $first_day_of_this_month = new \DateTime("first day of this month");
        $last_day_of_this_month = new \DateTime("last day of this month");

        $first_day_of_this_year = new \DateTime("first day of January");
        $last_day_of_this_year = new \DateTime("last day of December");

        $sales_service = new SalesService();
        $purchase_service = new PurchaseService();

        return $this->sendOK([
            "inventory" => [
                "stock" => array_column(
                    $phonestock_data,
                    "stock_total",
                    "Status"
                ),
                "cost" => array_column(
                    $phonestock_data,
                    "cost_total",
                    "Status"
                ),
            ],
            "today" => [
                "purchase" => $purchase_service->getPurchaseForPeriod(
                    $today,
                    $today
                ),
                "sales" => $sales_service->getSalesForPeriod($today, $today),
            ],
            "current_week" => [
                "purchase" => $purchase_service->getPurchaseForPeriod(
                    $current_week->format("Y-m-d"),
                    $current_week->modify("+6 days")->format("Y-m-d")
                ),
                "sales" => $sales_service->getSalesForPeriod(
                    $current_week->format("Y-m-d"),
                    $current_week->modify("+6 days")->format("Y-m-d")
                ),
            ],
            "current_month" => [
                "purchase" => $purchase_service->getPurchaseForPeriod(
                    $first_day_of_this_month->format("Y-m-d"),
                    $last_day_of_this_month->format("Y-m-d")
                ),
                "sales" => $sales_service->getSalesForPeriod(
                    $first_day_of_this_month->format("Y-m-d"),
                    $last_day_of_this_month->format("Y-m-d")
                ),
            ],
            "current_year" => [
                "purchase" => $purchase_service->getPurchaseForPeriod(
                    $first_day_of_this_year->format("Y-m-d"),
                    $last_day_of_this_year->format("Y-m-d")
                ),
                "sales" => $sales_service->getSalesForPeriod(
                    $first_day_of_this_year->format("Y-m-d"),
                    $last_day_of_this_year->format("Y-m-d")
                ),
            ],
        ]);
    }
}
