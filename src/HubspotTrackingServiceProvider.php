<?php

namespace HubspotTrackingCode;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use HubspotTrackingCode\HubspotTrackingCode;

class HubspotTrackingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/hubspot.php' => config_path('hubspot.php'),
        ], 'livewire-hubspot-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/livewire-hubspot'),
        ], 'livewire-hubspot-resources');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-hubspot');

        Livewire::component('hubspot-tracking-code', HubspotTrackingCode::class);
    }
}
