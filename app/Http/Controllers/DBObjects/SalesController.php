<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\InvalidDataException;
use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\PDFInvoiceRequest;
use App\Http\Requests\ReturnItemRequest;
use App\Http\Requests\SaveSaleRequest;
use App\Services\SalesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;

class SalesController extends BaseController
{
    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $sales_service = new SalesService();
        try {
            $response = [];
            $response['record'] = $sales_service->getSingleSales($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param SaveSaleRequest $request
     * @return JsonResponse
     */
    public function save(SaveSaleRequest $request): JsonResponse
    {
        $sales_service = new SalesService();

        try {
            $invoice_id = $sales_service->save($request);

            return $this->sendOK(['records_count' => 1, 'id' => $invoice_id], self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [],JsonResponse::HTTP_NOT_FOUND);
        } catch (InvalidDataException $e) {
            return $this->sendError(self::INVALID_DATA, [], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $sales_service = new SalesService();

        try {
            $sales_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param ReturnItemRequest $request
     * @return JsonResponse
     */
    public function returnItem(ReturnItemRequest $request): JsonResponse
    {
        $sales_service = new SalesService();

        try {
            $sales_service->returnItem($request);

            return $this->sendOK([], self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @return int
     */
    public function getNextInvoiceNo(): int
    {
        $sales_service = new SalesService();

        return $sales_service->getNextInvoiceNo();
    }

    /**
     * @param IdRequest $request
     */
    public function getPDFInvoice(IdRequest $request)
    {
        $sales_service = new SalesService();
        try {
            $invoice = $sales_service->getSingleSales($request);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }

        $pdf = new Pdf;

        $customer_name = Arr::get($invoice, 'customer.CustomerName', '');

        $customer_address = [];
        $customer_address[] = Arr::get($invoice, 'customer.Address', '');
        $customer_address[] = Arr::get($invoice, 'customer.City', '');

        $customer_address = array_filter($customer_address);
        $customer_address = join('<br>', $customer_address);

        $store_details = [];
        $store_details[] = session('app_settings.store_settings.name', 'Store Name');
        if (session('app_settings.store_settings.address', '')) {
            $store_details[] = nl2br(session('app_settings.store_settings.address'));
        }
        if (session('app_settings.store_settings.phone', '')) {
            $store_details[] = 'Tel. No: ' . session('app_settings.store_settings.phone');
        }
        if (session('app_settings.store_settings.email', '')) {
            $store_details[] = 'Email: ' . session('app_settings.store_settings.email');
        }
        $store_details = join('<br>', $store_details);

        $total = 0;
        $items = [];
        foreach ($invoice['children'] ?? [] as $child) {
            if ($child['Returned']) {
                continue;
            }

            $price = number_format($child['Cost'], 2);

            $child_total = number_format($price * $child['Qty'], 2);

            if (!$child['Returned']) {
                $total += $child_total;
            }

            $returned = $child['Returned'] ? '<br><font color="red">Returned</font>' : '';

            $item = <<<EOT
<tr>
    <td>{$child['Qty']}</td>
    <td>{$child['phone_details']['StockType']}</td>
    <td>
        {$child['phone_details']['manufacturer']['Name']} {$child['phone_details']['model']['Name']} {$child['phone_details']['Size']}
        <br>
        {$child['phone_details']['color']['Name']}
        <br>
        {$child['phone_details']['Network']}
        <br>
        IMEI: {$child['phone_details']['IMEI']}
        {$returned}
    </td>
    <td style="text-align: right;">&#163; {$price}</td>
    <td style="text-align: right;">&#163; {$child_total}</td>
</tr>
EOT;

            $items[] = $item;
        }

        $items = join('', $items);

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
    </style>
    <br>
    <table style="width: 100%;">
        <tr>
            <td>
                <h1 style="font-size: 21px; font-weight: bold; margin-bottom: 2px;">Smartfix Solutions LTD</h1>
                <h1 style="font-size: 21px; font-weight: bold; margin-top: 0px;">T/A PHONENATION</h1>
            </td>
            <td style="text-align: right;">
                <h1 style="font-size: 27px; font-weight: bold; color: #3B5E90;">INVOICE</h1>
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
                            <td style="width: 110px; font-weight: bold;">Invoice No.</td>
                            <td>{$invoice['InvoiceNo']}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Date</td>
                            <td>{$invoice['InvoiceDate']}</td>
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
                        <td style="width: 140px;">Sold To:</td>
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
                        <td>{$invoice['PaymentMethod']}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="height: 20px;"></td>
        </tr>
    </table>

    <table style="width: 100%;" class="invoice_items_table">
        <thead>
            <tr>
                <td style="width: 80px;">Qty</td>
                <td style="width: 120px;">Type</td>
                <td style="">Description</td>
                <td style="width: 120px; text-align: right;">Unit Price</td>
                <td style="width: 120px; text-align: right;">Total</td>
            </tr>
        </thead>
        <tbody>
            {$items}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td style="font-weight: bold; text-align: right;">Total:</td>
                <td style="text-align: right;">&#163; {$total}</td>
            </tr>
        </tfoot>
    </table>

    <br>
    <br>

    <table style="border: 1px solid #000000; border-collapse: collapse; width: 100%;" cellpadding="10">
        <tr>
            <td>
                <p style="margin: 0px; text-decoration: underline; font-weight: bold;">Terms & Conditions:</p>
                <br>
                We declare that this invoice shows actual price of the goods and that all particulars are true and correct.
                <br>
                <ol>
                    <li>28 days warranty on used phones.</li>
                    <li>NO RETURNS, ONCE ITEM IS SOLD.</li>
                    <li>BROKEN OR CRACKED OR SCRATCHED SCREENS ARE ALREADY OUT OF WARRANTY. ANY SERVICES CONDUCTED TO YOUR DEVICE MAY VOID ANY MANUFACTURE WARRANTY PROVIDED BY MANUFACTURER</li>
                    <li>NO WARRANTY / REFUNDS ON WATER DAMAGES, BROKEN, ROOTED, AND ON DEVICES REPAIRED PREVIOUSLY BY SOMEONE ELSE OTHER THAN "PHONENATION"</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="height: 30px;"></td>
        </tr>
        <tr>
            <td>
                <ul>
                    <li>Should you have any enquiries concerning this invoice, please contact 0116 2737273</li>
                    <li>
                        FOR BACS PLEASE USE ACCOUNT DETAILS AS BELOW:
                        <br>
                        SMARTFIX SOLUTIONS LTD, ACC. No.: 713565541, Sort code: 60-15-48
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

        $filename = 'Invoice_' . $request->get('Id') . '.pdf';

        // Save the PDF
        if (!$pdf->saveAs(storage_path('app/public/' . $filename))) {
            return $this->sendError($pdf->getError());
        }

        if (!$pdf->send()) {
            return $this->sendError('File not found.');
        }

        Storage::disk('public')->delete($filename);
    }
}
