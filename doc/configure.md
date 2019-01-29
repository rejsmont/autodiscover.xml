# Configuration

## Application configuration

Configuration is done in [.env.local](examples/.env.local) file.
First, copy template file to appropriate location:
 
```shell
cp doc/examples/.env.local.template .env.local
```

The file is populated with default values and decently documented.
Each string that contains a space must be surrounded by quotes `''`.
Modify this file to reflect your configuration. 
Please pay special attention to `_SERVERS` fields - they have to be provided
in json array format, for example:
```shell
IMAP_SERVERS='[imap://imap.example.org, imaps://imap.example.org]'
SMTP_SERVERS='[smtp://smtp.example.org::587/tls]'
```
The server URLs follow the 
`"protocol://[u:auth@]host[:port][/tls]"` format. Providing `protocol`
and `host` is mandatory. Valid values for `protocol` include:

 * imap
 * imaps
 * smtp
 * ssmtp
 * smtps
 * submission
 * pop3
 * pop3s

Optional fields include `auth` and `port`. Additionally, `tls` flag can
be specified to indicate that the server supports `STARTTLS` command.
Default values for port are selected automatically, based on the protocol.
The `auth` option defaults to `password-cleartext`. Valid values for `auth`
include:

 * password-cleartext
 * password-encrypted
 * NTLM
 * GSSAPI

### Configuration default values
```shell
PROVIDER_NAME=
PROVIDER_DOMAIN=
PROVIDER_SHORT_NAME=

DATABASE_DRIVER=pdo_mysql
DATABASE_URL=
DATABASE_DOMAIN_QUERY=
DATABASE_USER_QUERY=
DATABASE_USER_NAME_FIELD=
DATABASE_USER_DN_FIELD=

LDAP_URL=ldap://localhost
LDAP_DOMAIN_BASE=
LDAP_DOMAIN_FILTER=(&(associatedDomain=%s)(objectClass=organizationalUnit))
LDAP_DOMAIN_ATTRIBUTE=associatedDomain
LDAP_USER_BASE=
LDAP_USER_FILTER=(&(mail=%s)(objectClass=person))
LDAP_USER_NAME_ATTRIBUTE=mail
LDAP_USER_DN_ATTRIBUTE=cn

IMAP_SERVERS='["imap://imap.${PROVIDER_DOMAIN}/tls"]'
SMTP_SERVERS='["smtp://smtp.${PROVIDER_DOMAIN}:587/tls"]'
POP3_SERVERS='[]'
ACTIVESYNC_URL=
```

After each change in .env.local you **must** run the following command:
```shell
# bin/console cache:clear --env=prod
```
otherwise your changes won't take an effect.

---
## Web server configuration

The application provides the following case-sensitive routes:

```
GET  /mail/config-v1.1.xml?emailaddress=
POST /autodiscover/autodiscover.xml
GET  /email.mobileconfig?email=
```

To provide maximum compatibility and security it's desirable to run this
application behind a http reverse proxy.

### Example apache configuration:
This configuration requires `mod_proxy_fcgi`, `mod_setenvif`,
`mod_rewrite` and `php7.2-fpm`:

```shell
# sudo a2enmod proxy_fcgi setenvif rewrite
# sudo a2enconf php7.2-fpm
```

#### /etc/apache2/sites-available/090-autodiscover.conf
```
<VirtualHost localhost:8000>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/autodiscover.xml/public

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/html/public>
        AllowOverride None
        Order Allow,Deny
        Allow from All

        DirectoryIndex index.php
        FallbackResource /index.php

        <IfModule mod_negotiation.c>
            Options -MultiViews
        </IfModule>

        <IfModule mod_rewrite.c>
            RewriteEngine On

            RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
            RewriteRule ^(.*) - [E=BASE:%1]

            RewriteCond %{HTTP:Authorization} .
            RewriteRule ^ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

            RewriteCond %{ENV:REDIRECT_STATUS} ^$
            RewriteRule ^index\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]

            RewriteCond %{REQUEST_FILENAME} -f
            RewriteRule ^ - [L]

            RewriteRule ^ %{ENV:BASE}/index.php [L]
        </IfModule>

        <IfModule !mod_rewrite.c>
            <IfModule mod_alias.c>
                RedirectMatch 307 ^/$ /index.php/
            </IfModule>
        </IfModule>
    </Directory>
    <Directory /var/www/html/public/bundles>
        FallbackResource disabled
    </Directory>
</VirtualHost>
```

### Example nginx reverse proxy configuration:

To use the reverse proxy you must install nginx:
```shell
# sudo apt install nginx
```

#### /etc/nginx/sites-available/autodiscover
```
server {
    server_name example.org, autoconfig.example.org autodiscover.example.org;

    proxy_redirect           off;
    proxy_set_header         X-Real-IP $remote_addr;
    proxy_set_header         X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header         Host $http_host;
    proxy_set_header         X-Forwarded-Proto $scheme;

    location ~* ^\/(\.well-known\/autoconfig\/)?(mail\/config-v1\.1\.xml) {
        proxy_pass http://localhost:8000/mail/config-v1.1.xml$is_args$args;
    }

    location ~* ^\/(autodiscover\/autodiscover)(\.xml|\.json) {
        proxy_pass http://localhost:8000/autodiscover/autodiscover$2;
    }

    location ~* ^\/(email\.mobileconfig) {
        proxy_pass http://localhost:8000/email.mobileconfig$is_args$args;
    }

    listen [::]:443 ssl;
    listen 443 ssl;
    ssl_certificate /etc/letsencrypt/live/example.org/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.org/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
}
```
