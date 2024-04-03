<?php

namespace blizko\LibrenmsAPIPlugin\Providers;

use App\Plugins\Hooks\MenuEntryHook;
use App\Plugins\PluginManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use blizko\LibrenmsAPIPlugin\Commands\SayHelloCommand;
use blizko\LibrenmsAPIPlugin\Hooks\MenuHook;

class ApiPluginProvider extends ServiceProvider
{
    public function boot(PluginManager $manager)
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'api-plugin');
        
        $name = 'api-plugin';
        $manager->publishHook($name,MenuEntryHook::class, MenuHook::class);
    }
}
