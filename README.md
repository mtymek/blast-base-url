Blast\BaseUrl
=============

[![Build Status](https://travis-ci.org/mtymek/blast-base-url.svg?branch=master)](https://travis-ci.org/mtymek/blast-base-url)

PSR-7 middleware and helpers for working with base URL.
  
Introduction
------------

This package detects base URL of web application. It is useful when you need your app
to be served from subdirectory (like `http://localhost/my-project/public`). This can
be useful sometimes, especially in development environment.

Detection logic is based on [`zend-http`](https://github.com/zendframework/zend-http) 
package.

Usage
-----

For simplicity, following instructions are targeting applications based on 
[Zend Expressive Skeleton](https://github.com/zendframework/zend-expressive-skeleton),
assuming that `Zend\ServiceManager` was selected as DI container.
  
`Blast\BaseUrl` is based on PSR-7, so it will work well with other frameworks/dispatchers
like Slim3 or Relay, just that wiring process will look different.

### Base URL Middleware

Register factory for middleware:

```php
return [
    'dependencies' => [
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            Blast\BaseUrl\BaseUrlMiddleware::class => Blast\BaseUrl\BaseUrlMiddlewareFactory::class,
        ],
    ],
];
```

Add `BaseUrlMiddleware` to your pipeline before routing:

```php
return [
    'middleware_pipeline' => [
        'always' => [
            [
                'middleware' => [
                    BaseUrlMiddleware::class,
                ],
                'priority' => 10000,
            ],
        ],
    ],
];
```

`BaseUrlMiddleware` will alter path from request URI, stripping base url. It means that
even if you access your project from `http:/localhost/~user/project/public/index.php/foo/bar`,
next middleware in the pipe will see the path as `/foo/bar`.

Additionally, two attributes will be added to ServerRequest, holding base URL and base path:

```php
echo $request->getAttribute(BaseUrlMiddleware::BASE_URL);   
// outputs: /some/subdirectory/index.php

echo $request->getAttribute(BaseUrlMiddleware::BASE_PATH);
// outputs: /some/subdirectory/
```

### Generating URLs

`BaseUrlMiddleware` is able to automatically configure `UrlHelper`, so that all URLs generated 
by this helper will have appropriate prefix. This will be done automatically if `UrlHelper`
is available in service container.

### Accessing assets - base path

Another feature provided by this package is base path helper. It can be used to generate URLS
for your asset files that work correctly under subdirectory. Enabling it requires following
additions to your configuration:

```php
return [
    'dependencies' => [
        'invokables' => [
            Blast\BaseUrl\BasePathHelper::class => Blast\BaseUrl\BasePathHelper::class,            
        ],        
    ],
    'view_helpers => [
        'aliases' => [
            'basePath' => Blast\BaseUrl\BasePathHelper::class,
        ],
        'factories' => [
            Blast\BaseUrl\BasePathHelper::class => Blast\BaseUrl\BasePathViewHelperFactory::class,
        ],
    ],
];
```

If `BasePathHelper` is available, `BaseUrlMiddleware` will automatically configure it during
execution. You will be able to use following syntax inside `zend-view` templates:

```html
<link rel="stylesheet" href="<?= $this->basePath('/css/style.css') ?>" />
```

Depending on your application directory, it will produce something similar to:

```html
<link rel="stylesheet" href="/public_html/my-project/public/css/style.css" />
```
