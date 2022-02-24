<?php

namespace WebId\Boo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WebId\Boo\Commands\BooCommand;
use WebId\Boo\Providers\EventServiceProvider;

class BooServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('boo')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_boo_table')
            ->hasCommand(BooCommand::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        return parent::boot();
    }

    public function register()
    {
        parent::register();

        $this->app->register(EventServiceProvider::class);
    }
}
