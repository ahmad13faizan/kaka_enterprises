<?php

namespace Agency\ProductSample\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class SampleRequestDataGrid extends DataGrid
{
    protected $primaryColumn = 'sample_request_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('sample_requests')
            ->leftJoin('customers', 'sample_requests.customer_id', '=', 'customers.id')
            ->leftJoin('product_flat', function ($join) {
                $join->on('sample_requests.product_id', '=', 'product_flat.product_id')
                     ->where('product_flat.locale', app()->getLocale());
            })
            ->select(
                'sample_requests.id as sample_request_id',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as requester_name"),
                'product_flat.name as product_name',
                'sample_requests.quantity',
                'sample_requests.shipping_address',
                'sample_requests.status',
                'sample_requests.created_at'
            );

        $this->addFilter('sample_request_id', 'sample_requests.id');
        $this->addFilter('status', 'sample_requests.status');
        $this->addFilter('created_at', 'sample_requests.created_at');

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'sample_request_id',
            'label'      => trans('product-sample::app.admin.sample-requests.id'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'requester_name',
            'label'      => trans('product-sample::app.admin.sample-requests.requester'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'product_name',
            'label'      => trans('product-sample::app.admin.sample-requests.product'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'quantity',
            'label'      => trans('product-sample::app.admin.sample-requests.quantity'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('product-sample::app.admin.sample-requests.status'),
            'type'               => 'string',
            'searchable'         => false,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => 'Pending', 'value' => 'pending'],
                ['label' => 'Approved', 'value' => 'approved'],
                ['label' => 'Rejected', 'value' => 'rejected'],
                ['label' => 'Shipped', 'value' => 'shipped'],
                ['label' => 'Delivered', 'value' => 'delivered'],
            ],
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('product-sample::app.admin.sample-requests.created-at'),
            'type'            => 'date',
            'searchable'      => false,
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-view',
            'title'  => trans('product-sample::app.admin.sample-requests.view'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.sample-requests.show', $row->sample_request_id);
            },
        ]);
    }
}
