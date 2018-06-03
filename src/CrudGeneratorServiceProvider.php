<?php
/**
 * Laravel Crud Generator
 *
 * @author    Abdelqader Osama Al Dweik <asd.abyd@gmail.com>
 * @copyright 2018 Abdelqader Osama Al Dweik / Abdelqader's Resume (http://abyd.net)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/asd4abyd/crud-generator
 * @see       http://abyd.net
 *
 */

namespace Dweik\CrudGenerator;

use Dweik\CrudGenerator\Command\CrudGeneratorCommand;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;


class CrudGeneratorServiceProvider extends ServiceProvider
{

    protected $defer = true;


    public function boot()
    {
        $viewPath = __DIR__.'/../views';
        $this->loadViewsFrom($viewPath, 'Dweik');

        $configPath = __DIR__ . '/../config/crud-generator.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('crud-generator.php');
        } else {
            $publishPath = base_path('config/crud-generator.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/crud-generator.php';
        $this->mergeConfigFrom($configPath, 'crud-generator');

        $localViewFactory = $this->createLocalViewFactory();

        $this->app->singleton(
            'command.crud.generate',
            function ($app) use ($localViewFactory) {
                return new CrudGeneratorCommand($app['config'], $app['files'], $localViewFactory);
            }
        );


        $this->commands('command.crud.generate');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.crud.generate');
    }

    /**
     * @return Factory
     */
    private function createLocalViewFactory()
    {
        $resolver = new EngineResolver();
        $resolver->register('php', function () {
            return new PhpEngine();
        });
        $finder = new FileViewFinder($this->app['files'], [__DIR__ . '/../views']);
        $factory = new Factory($resolver, $finder, $this->app['events']);
        $factory->addExtension('php', 'php');

        return $factory;
    }
}