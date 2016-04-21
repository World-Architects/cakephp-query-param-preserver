# Query Param Preserver Component

## Setup
Checkout the plugin using composer.
`composer require psa/cakephp-query-param-preserver:dev-master`

[Load the plugin](http://book.cakephp.org/3.0/en/plugins.html#loading-a-plugin) and define the actions for which you want to preserve the query params. The plugin won't do anything by default, you have to define the actions explicitly.

```php
$this->loadComponent('Psa/QueryParamPreserver.QueryParamPreserver', [
    'actions' => [
        'index'
    ]
]);
```

## License & Copyright

Copyright 2016 PSA Publishers Ltd.

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License. Redistributions of the source code included in this repository must retain the copyright notice found in each file.
