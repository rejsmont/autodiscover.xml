# Installation

The project is based on the [Symfony framework](https://symfony.com),
which makes it easy to install, configure and maintain.

> **Note:** Before you begin, make sure that you have `php` installed
> and correctly configured on your webserver. You will also
> need `composer` to install project dependencies.
>
> **Autodiscovery.xml requires the following PHP extensions:**
> * Ctype
> * iconv
> * JSON
> * PCRE
> * Session
> * SimpleXML
> * Tokenizer
> * LDAP
> * PDO extension for one of the following databases:
>   * pdo-mysql (MySQL or MariaDB) - *default*
>   * pdo-pgsql (PostgreSQL)
>   * pdo-sqlite (SQLite)
>   * pdo-oci (Oracle)
>   * pdo-sqlsrv (Microsoft SQL Server)
>   * pdo-dblib (Sybase)
>
> For Ubuntu or Debian you can install the required packages
> (with MySQL backend) using the following command:
> ```shell
> # sudo apt install php-xml php-ldap php-mysql php-json php-fpm apache2 composer
> ```


To download this package, simply clone the
[GitHub reporitory](https://github.com/rejsmont/autodiscover.xml)
into your webserver home:

```shell
# cd /var/www
# git clone https://github.com/rejsmont/autodiscover.xml.git
```

Change the directory to cloned repository and pull dependencies
using composer:
```shell
# cd autodiscover.xml
# composer install
```

That's it! Now, move ahead to [configuration](configure.md).
