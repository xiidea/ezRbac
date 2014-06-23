Use Composer
==============

If you don't have [Composer][1] yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -s http://getcomposer.org/installer | php

If you want some [customization](./docs/customization.md), first make it in the composer.json file. Then, use the `require` command to download ezrbac to appropriate location.

    php composer.phar require xiidea/ezrbac:dev-stable

[1]:  http://getcomposer.org/