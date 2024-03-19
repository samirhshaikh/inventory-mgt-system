<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\RepairsDatatable;
use App\Datatables\SalesDatatable;
use App\Exceptions\InvalidDataException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveRepairRequest;
use App\Services\PurchaseService;
use App\Services\RepairsService;
use App\Services\SalesService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;

class RepairsController extends BaseController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new RepairsDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.repairs.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.repairs.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $repairs_service = new RepairsService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $repairs_service->getAll(
            $order_by,
            $order_direction,
            $request->get("search_type", "simple") ?? "simple",
            $request->get("search_text", "") ?? "",
            $request->get("search_data", "{}") ?? "{}"
        );

        return $this->prepareRecordsOutput(
            $table,
            $records,
            $total_records,
            (int) $request->get("page_no", 1),
            $request->get("search_text", ""),
            (int) $request->get("get_all_records", 0)
        );
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $repair_service = new RepairsService();

        try {
            $response = [];
            $response["record"] = $repair_service->getSingleRepair($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param SaveRepairRequest $request
     * @return JsonResponse
     */
    public function save(SaveRepairRequest $request): JsonResponse
    {
        $repair_service = new RepairsService();

        try {
            $response = $repair_service->save($request);

            return $this->sendOK($response, self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (InvalidDataException $e) {
            return $this->sendError(
                self::INVALID_DATA,
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $repair_service = new RepairsService();

        try {
            $repair_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (ReferenceException $e) {
            return $this->sendError(
                self::RECORD_REFERENCE_FOUND,
                [],
                JsonResponse::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse|void
     */
    public function getPDFInvoice(IdRequest $request)
    {
        $repairs_service = new RepairsService();
        try {
            $invoice = $repairs_service->getSingleRepair($request);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $pdf = new Pdf();

        $customer_name = Arr::get($invoice, "customer.CustomerName", "");

        $customer_address = [];
        $customer_address[] = Arr::get($invoice, "customer.Address", "");
        $customer_address[] = Arr::get($invoice, "customer.City", "");

        $customer_address = array_filter($customer_address);
        $customer_address = join("<br>", $customer_address);

        $store_details = [];
        $store_details[] = session(
            "app_settings.store_settings.name",
            "Store Name"
        );
        $store_name = $store_details[0];
        if (session("app_settings.store_settings.address", "")) {
            $store_details[] = nl2br(
                session("app_settings.store_settings.address")
            );
        }
        if (session("app_settings.store_settings.phone", "")) {
            $store_details[] =
                "Tel. No: " . session("app_settings.store_settings.phone");
        }
        if (session("app_settings.store_settings.email", "")) {
            $store_details[] =
                "Email: " . session("app_settings.store_settings.email");
        }
        $store_details = join("<br>", $store_details);

        $account_details = "";
        if (session("app_settings.store_settings.account_details", "")) {
            $account_details = session(
                "app_settings.store_settings.account_details"
            );
        }

        $parts_total = 0;
        $items = [];
        foreach ($invoice["children"] ?? [] as $child) {
            $parts_total += $child["Cost"];

            //            $item =
            //                "
            //<tr>
            //    <td>{$child["part"]["Name"]}</td>
            //    <td style='text-align: right;'>&#163; " .
            //                number_format($child["Cost"], 2) .
            //                "</td>
            //</tr>
            //";
            //
            //            $items[] = $item;
        }

        $grand_total = $invoice["Amount"]; //+ $parts_total

        $items = join("", $items);

        $parts_total = number_format($parts_total, 2);
        $grand_total = number_format($grand_total, 2);
        $amount = number_format($invoice["Amount"], 2);

        $device_details = [];
        foreach (
            [
                "IMEI",
                "phone_details.manufacturer.Name",
                "phone_details.model.Name",
                "phone_details.color.Name",
            ]
            as $key
        ) {
            if (Arr::get($invoice, $key, "")) {
                $device_details[] = Arr::get($invoice, $key);
            }
        }
        $device_details = join(" - ", $device_details);

        $notes = nl2br($invoice["Notes"] ?? "Repair");

        $body = <<<EOT
<html xmlns="http://www.w3.org/1999/html">
<body>
    <style>
    body {font-family: Arial, Helvetica, sans-serif; margin: 30px; font-size: 15px;}
    td {vertical-align: top;}
    .invoice_items_table {border: 1px solid black; border-collapse: collapse;}
    .invoice_items_table thead tr td {background: #b0b0b0; font-weight: bold; font-size: 17px; text-transform: uppercase; padding: 5px 3px; border-bottom: 1px solid black;}
    .invoice_items_table tbody tr td {background: #ffffff; font-size: 16px; padding: 5px 3px; border-bottom: 1px solid #b0b0b0;}
    .invoice_items_table tbody tr:last-child td {border-bottom: 1px solid #000000;}
    .invoice_items_table tfoot tr td {background: #8cc9e0; font-size: 16px; padding: 5px 3px;}
    .invoice_items_table .tradein_items td {background: #f9eaec;}
    .terms_table {border: 1px solid #000000; border-collapse: collapse; width: 100%;}
    .terms_table tr td {padding: 5px;}
    </style>
    <br>
    <table style="width: 100%;">
        <tr>
            <td>
                <h1 style="font-size: 21px; font-weight: bold; margin-bottom: 2px;">{$store_name}</h1>
<!--                <h1 style="font-size: 21px; font-weight: bold; margin-top: 0px;">T/A PHONENATION</h1>-->
            </td>
            <td style="text-align: right;">
                <h1 style="font-size: 27px; font-weight: bold; color: #3B5E90;">REPAIR INVOICE</h1>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="height: 20px;"></td>
        </tr>

        <tr>
            <td style="line-height: 19px;">
                {$store_details}
            </td>
            <td style="text-align: right;">
                <div style="float: right;">
                    <table style="width: 290px;">
                        <tr>
                            <td style="width: 120px; font-weight: bold;">Invoice No:</td>
                            <td>{$invoice["InvoiceNo"]}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Invoice Date:</td>
                            <td>{$invoice["InvoiceDate"]}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Recieved Date:</td>
                            <td>{$invoice["ReceivedDate"]}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="height: 20px;"></td>
        </tr>

        <tr>
            <td colspan="2">
                <table style="width: 500px; font-weight: bold; font-size: 17px;">
                    <tr>
                        <td style="width: 140px;">Customer:</td>
                        <td>{$customer_name}</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="height: 10px;"></td>
                    </tr>

                    <tr>
                        <td>Address:</td>
                        <td>{$customer_address}</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="height: 10px;"></td>
                    </tr>

                    <tr>
                        <td>Payment Type:</td>
                        <td>{$invoice["PaymentMethod"]}</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="height: 10px;"></td>
                    </tr>

                    <tr>
                        <td>Device Details:</td>
                        <td>{$device_details}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="height: 10px;"></td>
        </tr>
    </table>

    <table style="width: 100%;" class="invoice_items_table">
        <thead>
            <tr>
                <td style="">Description</td>
                <td style="width: 120px; text-align: right;">Price</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="">{$notes}</td>
                <td></td>
            </tr>

            <!--tr>
                <td style="font-weight: bold; text-align: right;">Parts Total:</td>
                <td style="text-align: right;">&#163; {$parts_total}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: right;">Service Cost:</td>
                <td style="text-align: right;">&#163; {$amount}</td>
            </tr-->
        </tbody>
        <tfoot>
            <tr>
                <td style="font-weight: bold; text-align: right;">Total:</td>
                <td style="text-align: right;">&#163; {$grand_total}</td>
            </tr>
        </tfoot>
    </table>

    <br>
    <br>

    <table class="terms_table">
        <tr>
            <td>
                <p style="margin: 0px; text-decoration: underline; font-weight: bold;">Terms & Conditions:</p>
                <br>
                We declare that this invoice shows actual price of the goods and that all particulars are true and correct.
                <br>
            </td>
        </tr>
        <tr>
            <td style="height: 20px;"></td>
        </tr>
        <tr>
            <td>
                <ul>
                    <li>Should you have any enquiries concerning this invoice, please contact 0116 2737273</li>
                    <li>
                        FOR BACS PLEASE USE ACCOUNT DETAILS AS BELOW:
                        <br>
                        {$account_details}
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="height: 20px;"></td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold; font-size: 19px;">THANK YOU FOR YOUR BUSINESS!</td>
        </tr>
        <tr>
            <td style="height: 20px;"></td>
        </tr>
        <tr>
            <td style="text-align: center;">This is a computer generated Invoice and does not require signature.</td>
        </tr>
    </table>
</body>
</html>
EOT;

        $pdf->addPage($body);

        $filename = "Invoice_" . $request->get("id") . ".pdf";

        // Save the PDF
        if (!$pdf->saveAs(storage_path("app/public/" . $filename))) {
            return $this->sendError($pdf->getError());
        }

        if (!$pdf->send()) {
            return $this->sendError("File not found.");
        }

        Storage::disk("public")->delete($filename);
    }
}
