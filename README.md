Minions Component Server using ReactPHP
===================

[![Build Status](https://travis-ci.org/katsana/minions-server.svg?branch=master)](https://travis-ci.org/katsana/minions-server)
[![Latest Stable Version](https://poser.pugx.org/katsana/minions-server/v/stable)](https://packagist.org/packages/katsana/minions-server)
[![Total Downloads](https://poser.pugx.org/katsana/minions-server/downloads)](https://packagist.org/packages/katsana/minions-server)
[![Latest Unstable Version](https://poser.pugx.org/katsana/minions-server/v/unstable)](https://packagist.org/packages/katsana/minions-server)
[![License](https://poser.pugx.org/katsana/minions-server/license)](https://packagist.org/packages/katsana/minions-server)

* [Installation](#installation)
* [Usages](#usages)
    - [Configurations](#configurations)
    - [Keeping the socket server running with supervisord](#keeping-the-socket-server-running-with-supervisord)

## Installation

Minions can be installed via composer:

```
composer require "katsana/minions-server=^1.0"
```

Please ensure that you already install **Minions** and go through the [installation and setup documentation](https://github.com/katsana/minions).

## Usages

**Minion Server** will run the RPC Server using ReactPHP running from Artisan command. 

You can start the RPC server by issuing the artisan command:

    php artisan minions:serve

### Configurations

You can export the default `minions-server.php` to your project directory via the following command:

```
php artisan vendor:publish --provider="Minions\Server\MinionsServiceProvider" --tag="config"
```

#### Using a different port

The default port of the RPC server is `8085`. You may set a different port by updating the environment file (or `config/minions.php`).

```ini
MINION_SERVER_PORT=8081
```

#### Restricting the listening host

By default, the RPC server will listen on `127.0.0.1` and will **only** allow incoming connections from local networks. If you want to expose this, you set a different host updating the environment file (or `config/minions.php`).

```ini
MINION_SERVER_HOST='0.0.0.0'
```

### Keeping the socket server running with supervisord

The `minions:serve` daemon needs to always be running in order to accept connections. This is a prime use case for `supervisor`, a task runner on Linux.

First, make sure `supervisor` is installed.

    # On Debian / Ubuntu
    apt install supervisor

    # On Red Hat / CentOS
    yum install supervisor
    systemctl enable supervisor

Once installed, add a new process that supervisor needs to keep running. You place your configurations in the `/etc/supervisor/conf.d` (Debian/Ubuntu) or `/etc/supervisord.d` (Red Hat/CentOS) directory.

Within that directory, create a new file called `minions.conf`.

```
[program:minion]
command=/usr/bin/php /home/project/artisan minions:serve
numprocs=1
autostart=true
autorestart=true
```

Once created, instruct `supervisor` to reload it's configuration files (without impacting the already running `supervisor` jobs).

    supervisorctl update
    supervisorctl start minion

Your RPC server should now be running (you can verify this with `supervisorctl status`). If it were to crash, `supervisor` will automatically restart it.

Please note that, by default, `supervisor` will force a maximum number of open files onto all the processes that it manages. This is configured by the `minfds` parameter in `supervisord.conf`.

If you want to increase the maximum number of open files, you may do so in `/etc/supervisor/supervisord.conf` (Debian/Ubuntu) or `/etc/supervisord.conf` (Red Hat/CentOS):

    [supervisord]
    minfds=10240; (min. avail startup file descriptors;default 1024)

After changing this setting, you'll need to restart the `supervisor` process (which in turn will restart all your processes that it manages).
