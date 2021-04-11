<?php

namespace App\Datatables;

use App\Datatables\BaseDatatable;

abstract class ObjectType1Datatable extends BaseDatatable {
    protected abstract function getRouteId();
    protected abstract function getId();
    protected abstract function getRecordName();
    protected abstract function getIcon();

    public $columns = [
        [
            'name' => 'Name',
            'key' => 'Name',
            'enabled' => true,
            'th' => 'text-left sticky',
            'td' => 'text-left',
            'sorting' => true,
            'searching' => true,
            'order' => 1
        ],
        [
            'name' => 'Active',
            'key' => 'IsActive',
            'enabled' => true,
            'th' => 'w-1/12 text-left sticky hidden md:table-cell',
            'td' => 'text-left hidden md:block',
            'type' => 'Toggle',
            'route' => '%s.change-active-status',
            'sorting' => false,
            'searching' => false,
            'order' => 2
        ],
        [
            'name' => 'Date',
            'key' => 'UpdatedDate',
            'enabled' => true,
            'th' => 'text-left sticky hidden md:table-cell',
            'td' => 'text-left hidden md:block',
            'sorting' => true,
            'searching' => true,
            'order' => 5
        ],
        [
            'name' => 'Actions',
            'key' => 'route',
            'enabled' => true,
            'th' => 'sticky',
            'type' => 'ObjectType1Actions',
            'sorting' => false,
            'searching' => false,
            'order' => 6
        ]
    ];

    public function columns() {
        for ($i=0; $i<count($this->columns); $i++) {
            if (array_key_exists('route', $this->columns[$i])) {
                $this->columns[$i]['route'] = sprintf($this->columns[$i]['route'], $this->getRouteId());
            }
        }

        return $this->columns;
    }

    public function options() {
        $this->options = [
            'id' => $this->getId(),
            'url' => route('datatable.' . $this->getRouteId() . '.data'),
            'pagination' => false,
            'enable_search' => true,
            'primary_key' => 'Id',
            'record_name' => $this->getRecordName(),
            'routes' => [
                'delete' => route($this->getRouteId() . '.delete'),
                'get-single' => route($this->getRouteId() . '.get-single'),
                'save' => route($this->getRouteId() . '.save'),
                'check-duplicate-name' => route($this->getRouteId() . '.check-duplicate-name')
            ],
            'icon' => $this->getIcon(),
            'sorting' => [
                'enabled' => true,
                'default' => 'UpdatedDate',
                'direction' => 'desc'
            ],
            'cache_data' => true
        ];

        return $this->options;
    }

    public function rowTransformer(array $row, string $rowKey)
    {
        $row = parent::rowTransformer($row, $rowKey);

        return $row;
    }
}
