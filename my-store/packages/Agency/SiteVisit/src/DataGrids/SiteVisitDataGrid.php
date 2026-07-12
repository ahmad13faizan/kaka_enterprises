<?php

namespace Agency\SiteVisit\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class SiteVisitDataGrid extends DataGrid
{
    protected $primaryColumn = 'site_visit_id';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('site_visits')
            ->leftJoin('customers', 'site_visits.customer_id', '=', 'customers.id')
            ->leftJoin('products', 'site_visits.product_id', '=', 'products.id')
            ->leftJoin('product_flat', function ($join) {
                $join->on('products.id', '=', 'product_flat.product_id')
                     ->where('product_flat.locale', app()->getLocale());
            })
            ->leftJoin('admins as reps', 'site_visits.assigned_rep_id', '=', 'reps.id')
            ->select(
                'site_visits.id as site_visit_id',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as requester_name"),
                'product_flat.name as product_name',
                'site_visits.address',
                'site_visits.preferred_date',
                'site_visits.status',
                'reps.name as assigned_rep_name',
                'site_visits.created_at'
            );

        $this->addFilter('site_visit_id', 'site_visits.id');
        $this->addFilter('requester_name', DB::raw("CONCAT(customers.first_name, ' ', customers.last_name)"));
        $this->addFilter('product_name', 'product_flat.name');
        $this->addFilter('status', 'site_visits.status');
        $this->addFilter('assigned_rep_name', 'reps.name');
        $this->addFilter('preferred_date', 'site_visits.preferred_date');
        $this->addFilter('created_at', 'site_visits.created_at');

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'site_visit_id',
            'label'      => trans('site-visit::app.admin.site-visits.id'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'requester_name',
            'label'      => trans('site-visit::app.admin.site-visits.requester'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'product_name',
            'label'      => trans('site-visit::app.admin.site-visits.product'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'address',
            'label'      => trans('site-visit::app.admin.site-visits.address'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => false,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'preferred_date',
            'label'      => trans('site-visit::app.admin.site-visits.preferred-date'),
            'type'       => 'date',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'            => 'status',
            'label'            => trans('site-visit::app.admin.site-visits.status'),
            'type'             => 'string',
            'searchable'       => false,
            'filterable'       => true,
            'filterable_type'  => 'dropdown',
            'filterable_options' => [
                ['label' => 'Requested', 'value' => 'requested'],
                ['label' => 'Assigned', 'value' => 'assigned'],
                ['label' => 'Scheduled', 'value' => 'scheduled'],
                ['label' => 'Completed', 'value' => 'completed'],
                ['label' => 'Cancelled', 'value' => 'cancelled'],
            ],
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'assigned_rep_name',
            'label'      => trans('site-visit::app.admin.site-visits.assigned-rep'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('site-visit::app.admin.site-visits.created-at'),
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
            'title'  => trans('site-visit::app.admin.site-visits.view-details'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.site-visits.show', $row->site_visit_id);
            },
        ]);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'title'  => 'Cancel',
            'method' => 'POST',
            'url'    => route('admin.site-visits.mass-cancel'),
        ]);
    }
}
