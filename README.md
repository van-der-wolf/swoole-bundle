# Swoole Bundle

[![Maintainability](https://api.codeclimate.com/v1/badges/1d73a214622bba769171/maintainability)](https://codeclimate.com/github/pixelfederation/swoole-bundle/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/1d73a214622bba769171/test_coverage)](https://codeclimate.com/github/pixelfederation/swoole-bundle/test_coverage)
[![Open Source Love](https://badges.frapsoft.com/os/v1/open-source.svg?v=103)](https://github.com/ellerbrock/open-source-badges/)
[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)

Symfony integration with [Open Swoole](https://openswoole.com/) to speed up your applications.

---

## Build Matrix

| CI Job  | Branch [`master`](https://github.com/pixelfederation/swoole-bundle/tree/master)                                                                                   | Branch [`develop`](https://github.com/pixelfederation/swoole-bundle/tree/develop)                                                                                   |
| ------- |-------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Circle  | [![CircleCI](https://circleci.com/gh/pixelfederation/swoole-bundle/tree/master.svg?style=svg)](https://circleci.com/gh/pixelfederation/swoole-bundle/tree/master) | [![CircleCI](https://circleci.com/gh/pixelfederation/swoole-bundle/tree/develop.svg?style=svg)](https://circleci.com/gh/pixelfederation/swoole-bundle/tree/develop) |
| CodeCov | [![codecov](https://codecov.io/gh/pixelfederation/swoole-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/pixelfederation/swoole-bundle)              | [![codecov](https://codecov.io/gh/pixelfederation/swoole-bundle/branch/develop/graph/badge.svg)](https://codecov.io/gh/pixelfederation/swoole-bundle)               |
| Travis  | [![Build Status](https://travis-ci.org/pixelfederation/swoole-bundle.svg?branch=master)](https://travis-ci.org/pixelfederation/swoole-bundle)                     | [![Build Status](https://travis-ci.org/pixelfederation/swoole-bundle.svg?branch=develop)](https://travis-ci.org/pixelfederation/swoole-bundle)                      |

## Table of Contents

- [Swoole Bundle](#swoole-bundle)
  - [Build Matrix](#build-matrix)
  - [Table of Contents](#table-of-contents)
  - [Quick start guide](#quick-start-guide)
  - [Features](#features)
  - [Requirements](#requirements)
    - [Current version](#current-version)
    - [Future versions](#future-versions)
    - [Open Swoole](#open-swoole)
      - [Version check](#version-check)
      - [Installation](#installation)

## Quick start guide

1. Make sure you have installed proper Open Swoole PHP Extension and pass other [requirements](#requirements).

2. (optional) Create a new symfony project

    ```bash
    composer create-project symfony/skeleton project

    cd ./project
    ```

3. Install bundle in your Symfony application

    ```bash
    composer require k911/swoole-bundle
    ```

4. Edit `config/bundles.php`

    ```php
    return [
        // ...other bundles
        K911\Swoole\Bridge\Symfony\Bundle\SwooleBundle::class => ['all' => true],
    ];
    ```

5. Run Swoole HTTP Server

    ```bash
    bin/console swoole:server:run
    ```

6. Enter http://localhost:9501

7. You can now configure bundle according to your needs

## Features

-   Built-in API Server

    Swoole Bundle API Server allows managing Swoole HTTP Server in real-time.

    -   Reload worker processes
    -   Shutdown server
    -   Access metrics and settings

-   Improved static files serving

    Swoole HTTP Server provides a default static files handler, but it lacks supporting many `Content-Types`. To overcome this issue, there is a configurable Advanced Static Files Server. Static files serving remains enabled by default in the development environment. Static files directory defaults to `%kernel.project_dir%/public`. To configure your custom mime types check [configuration reference](docs/configuration-reference.md) (key `swoole.http_server.static.mime_types`).

-   Symfony Messenger integration

    _Available since version: `0.6`_

    Swoole Server Task Transport has been integrated into this bundle to allow easy execution of asynchronous actions. Documentation of this feature is available [here](docs/swoole-task-symfony-messenger-transport.md).

-   Hot Module Reload (HMR) for development **ALPHA**

    Since Swoole HTTP Server runs in Event Loop and does not flush memory between requests, to keep DX equal with normal servers, this bundle uses code replacement technique, using `inotify` PHP Extension to allow continuous development. It is enabled by default (when the extension is found) and requires no additional configuration. You can turn it off in bundle configuration.

    _Remarks: This feature currently works only on a Linux host machine. It probably won't work with Docker, and it is possible that it works only with configuration: `swoole.http_server.running_mode: process` (default)._

## Requirements

### Current version

-   PHP version `>= 7.4`
-   Open Swoole PHP Extension `>= 4.10.0`
-   Symfony `>= 5.4.0`

### Future versions

-   PHP version `>= 8.0`
-   Swoole PHP Extension `>= 4.10.0`
-   Symfony `>= 5.4`

Additional requirements to enable specific features:

-   [Inotify PHP Extension](https://pecl.php.net/package/inotify) `^2.0.0` to use Hot Module Reload (HMR)
    -   When using PHP 8, inotify version `^3.0.0` is required

### Open Swoole

Bundle requires [Open Swoole PHP Extension](https://github.com/openswoole/swoole-src) version `4.10.0` or higher. Active bug fixes are provided only for the latest version.

#### Version check

To check your installed version you can run the following command:

```sh
php -r "echo swoole_version() . \PHP_EOL;"

# 4.10.0
```

#### Installation

Official GitHub repository [openswoole/swoole-src](https://github.com/openswoole/swoole-src#installation) contains comprehensive installation guide. The recommended approach is to install it [from source](https://github.com/openswoole/swoole-src#2-compile-from-source).