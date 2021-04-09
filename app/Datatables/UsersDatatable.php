<?php

namespace App\Datatables;

use App\Datatables\BaseDatatable;

class UsersDatatable extends BaseDatatable {
    public $columns = [
        [
            'name' => 'User Name',
            'key' => 'UserName',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 1
        ],
        [
            'name' => 'Admin',
            'key' => 'IsAdmin',
            'enabled' => true,
            'th' => 'w-24 text-left sticky',
            'td' => 'text-left',
            'type' => 'Toggle',
            'route' => 'users.change-admin-status',
            'sorting' => false,
            'searching' => false,
            'order' => 2
        ],
        [
            'name' => 'Active',
            'key' => 'IsActive',
            'enabled' => true,
            'th' => 'w-24 text-left sticky',
            'td' => 'text-left',
            'type' => 'Toggle',
            'route' => 'users.change-active-status',
            'sorting' => false,
            'searching' => false,
            'order' => 4
        ],
        [
            'name' => 'Date',
            'key' => 'UpdatedDate',
            'enabled' => true,
            'th' => 'w-56 text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 5
        ],
        [
            'name' => 'Actions',
            'key' => 'route',
            'enabled' => true,
            'th' => 'w-44 sticky',
            'type' => 'UsersActions',
            'sorting' => false,
            'searching' => false,
            'order' => 6
        ]
    ];

    public function options() {
        $this->options = [
            'id' => 'users',
            'url' => route('datatable.users.data'),
            'pagination' => false,
            'enable_search' => true,
            'primary_key' => 'UserName',
            'record_name' => 'User',
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
