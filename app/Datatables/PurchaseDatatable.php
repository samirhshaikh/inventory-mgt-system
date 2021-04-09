<?php

namespace App\Datatables;

class PurchaseDatatable extends BaseDatatable
{
    public $columns = [
        [
            'name' => '',
            'key' => '',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'type' => 'ExpandCollapse',
            'sorting' => false,
            'searching' => false,
            'order' => 1
        ],
        [
            'name' => 'Invoice No',
            'key' => 'InvoiceNo',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 2
        ],
        [
            'name' => 'Invoice Date',
            'key' => 'InvoiceDate',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 3
        ],
        [
            'name' => 'Supplier',
            'key' => 'supplier.SupplierName',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 4
        ],
        [
            'name' => 'Total',
            'key' => 'childs',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'type' => 'PurchaseCost',
            'sorting' => true,
            'searching' => true,
            'order' => 5
        ],
        [
            'name' => 'Date',
            'key' => 'UpdatedDate',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 6
        ],
        [
            'name' => 'Actions',
            'key' => 'route',
            'enabled' => true,
            'th' => 'sticky',
            'type' => 'PurchaseActions',
            'sorting' => false,
            'searching' => false,
            'order' => 7
        ]
    ];

    public $child_columns = [
        [
            'name' => '',
            'key' => '',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'sorting' => false,
            'searching' => false,
            'order' => 1
        ],
        [
            'name' => 'Type',
            'key' => 'StockType',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'sorting' => false,
            'searching' => true,
            'order' => 2
        ],
        [
            'name' => 'IMEI',
            'key' => 'IMEI',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'sorting' => false,
            'searching' => true,
            'order' => 3
        ],
        [
            'name' => 'Phone',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'type' => 'Phone',
            'sorting' => false,
            'searching' => true,
            'order' => 4
        ],
        [
            'name' => 'Cost',
            'key' => 'Cost',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'type' => 'Float',
            'sorting' => false,
            'searching' => true,
            'order' => 5
        ],
        [
            'name' => 'Size',
            'key' => 'Size',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'sorting' => false,
            'searching' => true,
            'order' => 6
        ],
        [
            'name' => '',
            'key' => 'route',
            'enabled' => true,
            'th' => '',
            'type' => 'PurchasePhoneActions',
            'sorting' => false,
            'searching' => false,
            'order' => 7
        ]
    ];

    public function options()
    {
        $this->options = [
            'id' => 'purchase',
            'url' => route('datatable.purchase.data'),
            'pagination' => false,
            'enable_search' => true,
            'primary_key' => 'Id',
            'record_name' => 'Purchase',
            'child_record_name' => 'Phone',
            'sorting' => [
                'enabled' => true,
                'default' => 'UpdatedDate',
                'direction' => 'desc'
            ]
        ];

        return $this->options;
    }

    public function rowTransformer(array $row, string $rowKey)
    {
        $row = parent::rowTransformer($row, $rowKey);

        return $row;
    }
}
