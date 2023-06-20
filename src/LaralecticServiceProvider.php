<?php

namespace Omar\Laralestic;

use Omar\Laralestic\Commands\ReindexCommand;
use Elasticsearch\ClientBuilder as ElasticBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Illuminate\Support\Str;
use Omar\Laralestic\Commands\ListIndicesCommand;
use Omar\Laralestic\Commands\CreateIndexCommand;
use Omar\Laralestic\Commands\DropIndexCommand;
use Omar\Laralestic\Commands\UpdateIndexCommand;

/**
 * Class LaralesticServiceProvider
 * @package Omar\Laralestic
 */
class LaralesticServiceProvider extends ServiceProvider
{

    /**
     * LaralesticServiceProvider constructor.
     * @param Application $app
     */
    function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->mergeConfigFrom(
            dirname(__FILE__) . '/config/es.php', 'es'
        );

        $this->publishes([
            dirname(__FILE__) . '/config/' => config_path(),
        ], "es.config");

        // Auto configuration with lumen framework.

        if (Str::contains($this->app->version(), 'Lumen')) {
            $this->app->configure("es");
        }

        // Resolve Laravel Scout engine.

        if (class_exists("Laravel\\Scout\\EngineManager")) {

            try {

                $this->app->make(EngineManager::class)->extend('es', function () {

                    $config = config('es.connections.' . config('scout.es.connection'));

                    return new ScoutEngine(
                        ElasticBuilder::create()
                            ->setHosts($config["servers"])
                            ->setSSLVerification($config["verify_ssl"])
                            ->build(),
                        $config["index"]
                    );

                });

            } catch (BindingResolutionException $e) {

                // Class is not resolved.
                // Laravel Scout service provider was not loaded yet.

            }

        }


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // Package commands available for laravel or lumen higher than 5.1

        if(version_compare($this->app->version(), '5.1', ">=")) {

            if ($this->app->runningInConsole()) {

                // Registering commands

                $this->commands([
                    ListIndicesCommand::class,
                    CreateIndexCommand::class,
                    UpdateIndexCommand::class,
                    DropIndexCommand::class,
                    ReindexCommand::class
                ]);

            }
        }

        $this->app->singleton('es', function () {
            return new Connection();
        });
    }
}
