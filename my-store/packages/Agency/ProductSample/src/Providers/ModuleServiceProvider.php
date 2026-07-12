<?php

namespace Agency\ProductSample\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Agency\ProductSample\Models\SampleRequest::class,
        \Agency\ProductSample\Models\SampleInventory::class,
    ];
}
