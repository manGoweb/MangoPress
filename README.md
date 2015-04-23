# MangoPress

Fine tuned WordPress structure with the horse power of the Nette Framework, all utilizing the [Composer](https://getcomposer.org) and [mango-cli](https://github.com/manGoweb/mango-cli).


## Starting a new project

```sh
mango init --source manGoweb/MangoPress your-project-name && composer install
```

Requirements: PHP stack,  [Composer](https://getcomposer.org), [mango-cli](https://github.com/manGoweb/mango-cli)

Additional steps:
- Create a new database for WordPress installation
- Create your `config/config.local.neon` based on `config.local.sample.neon`.
- Make directories `log/`,  `temp/`, `public/wp-content/*` writeable for web process

## Project structure

* `app` - Nette MVC application
* `config` - All configuration in one place
* `public` - Public directory to be set as document_root dir
  * `assets` - compiled theme assets, do not edit them here
  * `wp-content` - WP content directory
  * `wp-core` - WP distribution installed via composer
* `theme` - main WP theme with all templates and original assets
* `vendor` - composer packages

## Theme development

You are going to spent the most of your time in the `theme` directory. Follow these code architecture instructions to avoid a loss of your sanity:

* Use `index.php` and other WP template files as controllers (php code only). Controller should define and fill a context for an actual template. 
* Use templates `views/*.latte` as views. All the HTML chunks belong here. Work with given context only and do not execute unnecessary php code. 
* Assets source directories are `styles`, `scripts` and `images` and the [mango-cli](https://github.com/manGoweb/mango-cli) compiles them to the `public/assets` distribution directory.

## Manage WP plugins

```sh
composer install wpackagist-plugin/PLUGINNAME
```

Thanks to [wpackagist](http://wpackagist.org) repository, you can install all plugins and themes from [official WordPress directory](http://plugins.svn.wordpress.org) via composer.

Installed plugins are used as [mu-plugins](http://codex.wordpress.org/Must_Use_Plugins), which cannot be disabled or removed from administration.
Beware: not all plugins can work that way, especially ones that need some sort of activation initialization steps.



## Copyright

Copyright 2014 by [manGoweb s.r.o.](http://www.mangoweb.cz) Code released under [the MIT license](LICENSE).

