<?php

namespace Agency\ProductSample\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class SampleInventoryDataGrid extends DataGrid
{
    protected $primaryColumn = 'inventory_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('sample_inventory')
            ->leftJoin('product_flat', function ($join) {
                $join->on('sample_inventory.product_id', '=', 'product_flat.product_id')
                     ->where('product_flat.locale', app()->getLocale());
            })
            ->select(
                'sample_inventory.id as inventory_id',
                'sample_inventory.product_id',
                'product_flat.name as product_name',
                'sample_inventory.sample_stock',
                'sample_inventory.allocated',
                DB::raw('(sample_inventory.sample_stock - sample_inventory.allocated) as available')
            );

        $this->addFilter('product_name', 'product_flat.name');

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'product_name',
            'label'      => trans('product-sample::app.admin.sample-inventory.product'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'sample_stock',
            'label'      => trans('product-sample::app.admin.sample-inventory.stock'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'allocated',
            'label'      => trans('product-sample::app.admin.sample-inventory.allocated'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'available',
            'label'      => trans('product-sample::app.admin.sample-inventory.available'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-edit',
            'title'  => trans('product-sample::app.admin.sample-inventory.update'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.sample-inventory.index') . '?edit=' . $row->product_id;
            },
        ]);
    }
}
