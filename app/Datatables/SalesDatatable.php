<?php

namespace App\Datatables;

class SalesDatatable extends BaseDatatable
{
    public $columns = [
        [
            'name' => '',
            'key' => '',
            'enabled' => true,
            'th' => 'w-2 text-left sticky',
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
            'name' => 'Customer',
            'key' => 'customer.CustomerName',
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
            'type' => 'SalesCost',
            'sorting' => true,
            'searching' => true,
            'order' => 5
        ],
        [
            'name' => 'Payment',
            'key' => 'PaymentMethod',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 6
        ],
        [
            'name' => 'Date',
            'key' => 'UpdatedDate',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 7
        ],
        [
            'name' => 'Actions',
            'key' => 'route',
            'enabled' => true,
            'th' => 'w-20 sticky',
            'type' => 'SalesActions',
            'sorting' => false,
            'searching' => false,
            'order' => 8
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
            'name' => 'IMEI',
            'key' => 'IMEI',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'sorting' => false,
            'searching' => true,
            'order' => 2
        ],
        [
            'name' => 'Cost',
            'key' => 'phone.Cost',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'type' => 'Float',
            'sorting' => false,
            'searching' => true,
            'order' => 3
        ],
        [
            'name' => 'Phone',
            'key' => 'phone',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'column_span' => 3,
            'type' => 'Phone',
            'sorting' => false,
            'searching' => true,
            'order' => 4
        ],
        [
            'name' => 'Size',
            'key' => 'phone.Size',
            'enabled' => true,
            'th' => 'text-left',
            'td' => 'text-left',
            'sorting' => false,
            'searching' => true,
            'order' => 5
        ],
        [
            'name' => '',
            'key' => 'route',
            'enabled' => true,
            'th' => '',
            'type' => 'SalesPhoneActions',
            'sorting' => false,
            'searching' => false,
            'order' => 6
        ]
    ];

    public function options()
    {
        $this->options = [
            'id' => 'sales',
            'url' => route('datatable.sales.data'),
            'pagination' => false,
            'enable_search' => true,
            'primary_key' => 'Id',
            'record_name' => 'Sale',
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
