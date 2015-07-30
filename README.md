
В composer.json добавляем в блок require
```json
 "vis/banners": "1.0.*"
```

Выполняем
```json
composer update
```

Добавляем в app.php
```php
  'Vis\Banners\BannersServiceProvider',
```

и в aliases
```php
'Banner'         => 'Vis\Banners\Banner',
```
Выполняем миграцию таблиц
```json
   php artisan migrate --package=vis/banners
```

Публикуем js файлы
```json
   php artisan asset:publish vis/banners
```

В файле app/config/packages/vis/builder/admin.php в массив menu добавляем
```php
 	array(
        'title' => 'Баннера',
        'icon'  => 'crop',
        'submenu' => array(
            array(
                'title'   => 'Баннера',
                'link'    => '/banners/banners_all',
                'check' => function() {
                    return true;
                }
            ),
            array(
                'title' => 'Баннерные площадки',
                'link'  => '/banners/area',
                'check' => function() {
                    return true;
                }
            ),

        ),
    ),
```