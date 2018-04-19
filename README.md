# WP PWA DEMO

```bash
$ composer install
$ docker-compose up -d
$ docker-compose
```

```bash
cd /var/www/html
vendor/bin/wp config create --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --allow-root

vendor/bin/wp core install \
      --url=http://localhost:9000 \
      --title="WP PWA DEMO" \
      --admin_user="admin" \
      --admin_password="admin" \
      --admin_email="admin@example.com" --allow-root
      
curl https://raw.githubusercontent.com/jawordpressorg/theme-test-data-ja/master/wordpress-theme-test-date-ja.xml > /tmp/wordpress-theme-test-date-ja.xml
vendor/bin/wp import /tmp/wordpress-theme-test-date-ja.xml --authors=create --allow-root
vendor/bin/wp rewrite structure "/%postname%/" --allow-root
```