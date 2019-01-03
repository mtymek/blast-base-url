Blast\BaseUrl
=============

[![Build Status](https://travis-ci.org/mtymek/blast-base-url.svg?branch=master)](https://travis-ci.org/mtymek/blast-base-url)
  
This package detects base URL of web application. It is useful when you need your app
to be served from subdirectory (like `http://localhost/my-project/public`). This can
be useful sometimes, especially in development environment.

View helpers for working with assets are also provided in the package.

Detection logic is based on [`zend-http`](https://github.com/zendframework/zend-http) 
package.

Installation
------------

Installation is supported using Composer:
```
$ composer require mtymek/blast-base-url
```

If `Zend Component Installer` is present, it will automatically update application configuration.

Usage
-----

For simplicity, following instructions are targeting applications based on 
[Zend Expressive Skeleton](https://github.com/zendframework/zend-expressive-skeleton),
assuming that `Zend\ServiceManager` was selected as DI container.
  
`Blast\BaseUrl` is based on PSR-7, so it will work well with other frameworks/dispatchers
like Slim3 or Relay, just that wiring process will look different.

### Base URL Middleware

Add `BaseUrlMiddleware` to your pipeline, just before routing middleware (`config/pipeline.php` file):

```php
// ...
$app->pipe(\Blast\BaseUrl\BaseUrlMiddleware::class);

// ...
$app->pipe(RouteMiddleware::class);
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
for your asset files that work correctly under subdirectory. 

If `BasePathHelper` is available, `BaseUrlMiddleware` will automatically configure it during
execution. 

#### Zend View

You will be able to use following syntax inside `zend-view` templates:

```html
<link rel="stylesheet" href="<?= $this->basePath('/css/style.css') ?>" />
```

Depending on your application directory, it will produce something similar to:

```html
<link rel="stylesheet" href="/public_html/my-project/public/css/style.css" />
```

#### Twig

You will be able to use following syntax inside `twig` templates:

```html
<link rel="stylesheet" href="{{ basePath('/css/style.css') }}" />
```

Depending on your application directory, it will produce something similar to:

```html
<link rel="stylesheet" href="/public_html/my-project/public/css/style.css" />
```
