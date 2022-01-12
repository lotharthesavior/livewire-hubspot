
# Livewire Hubspot

This package installs [Hubspot Tracking Code](https://developers.hubspot.com/docs/api/events/tracking-code) with [Visitor Identification](https://developers.hubspot.com/docs/api/conversation/visitor-identification) support.

## Installation

1. Install composer package:

```shell
composer require lotharthesavior/livewire-hubspot
```

2. Install configuration file:

```shell
php artisan vendor:publish --tag=livewire-hubspot-config
```

3. Add it to your template:

For templates behind login all

```php
@livewire('hubspot-tracking-code', [
    [
        'team' => auth()->user()->currentTeam->name,
        'firstname' => auth()->user()->name,
        'phone' => auth()->user()->currentTeam->phone,
    ],
])
```

For templates outside login all

```php
@livewire('hubspot-tracking-code')
```

## Todo

- Add tests

