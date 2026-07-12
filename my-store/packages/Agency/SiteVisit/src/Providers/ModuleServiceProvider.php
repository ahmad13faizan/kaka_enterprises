<?php

namespace Agency\SiteVisit\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Agency\SiteVisit\Models\SiteVisit::class,
        \Agency\SiteVisit\Models\SiteVisitStatusLog::class,
    ];
}
