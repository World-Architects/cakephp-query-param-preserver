# Query Param Preserver Component

A plugin to preserve the query params between requests and even visits by storing them in the users session.

Be aware that this is has downsides, for example if two tabs are open and an user modifies the params in one it will affect the next page reload in the other tab as well.
## Setup

Checkout the plugin using composer.

```sh
composer require psa/cakephp-query-param-preserver:dev-master
```

[Load the plugin](http://book.cakephp.org/3.0/en/plugins.html#loading-a-plugin) and define the actions for which you want to preserve the query params. The plugin won't do anything by default, you have to define the actions explicitly.

```php
$this->loadComponent('Psa/QueryParamPreserver.QueryParamPreserver', [
    // The action the component should be active on
    'actions' => [
        'index'
    ],
    // You want to ignore the page param on pages with pagination
    'ignoreParams' => [
        'page'
    ]
]);
```

## Resetting query params

Add query parameter `preserve=0` to URLs to reset previously stored query params. Use it in your Reset Filter buttons.

## License & Copyright

Copyright PSA Publishers Ltd.

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License. Redistributions of the source code included in this repository must retain the copyright notice found in each file.
