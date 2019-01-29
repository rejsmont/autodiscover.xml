# Autodiscover.xml

Autodiscovery.xml is a PHP-based service that provides autodiscovery
capabilities to mail servers that lack native autodiscovery support.

##Installation

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
>   * pdo-mysql (MySQL and MariaDB)
>   * pdo-pgsql (PostgreSQL)
>   * pdo-sqlite (SQLite)
>   * pdo-oci (Oracle)
>   * pdo-sqlsrv (Microsoft SQL Server)
>   * pdo-dblib (Sybase)
>
> For Ubuntu or Debian you can install the required packages
> (with MySQL backend) using the following command:
> ```
> sudo apt install php-xml php-ldap php-mysql php-json php-fpm apache2 composer
> ```



## Configuration

###Example nginx reverse proxy configuration:
```
    location ~* ^\/(\.well-known\/autoconfig\/)?(mail\/config-v1\.1\.xml) {
        proxy_pass http://web-srv-autodiscover:80/mail/config-v1.1.xml$is_args$args;
    }

    location ~* ^\/(autodiscover\/autodiscover)(\.xml|\.json) {
        proxy_pass http://web-srv-autodiscover:80/autodiscover/autodiscover$2;
    }

    location ~* ^\/(email\.mobileconfig) {
        proxy_pass http://web-srv-autodiscover:80/email.mobileconfig$is_args$args;
    }
```
