<?php

/**
 * Laravel IDE Helper Generator
 *
 * @author    Barry vd. Heuvel <barryvdh@gmail.com>
 * @copyright 2014 Barry vd. Heuvel / Fruitcake Studio (http://www.fruitcakestudio.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/barryvdh/laravel-ide-helper
 */

namespace Dweik\CrudGenerator\Command;

use Dweik\CrudGenerator\Extra\ExplainTable;
use Dweik\CrudGenerator\Generator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;


class CrudGeneratorCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD code of all database tables';

    /** @var \Illuminate\Config\Repository */
    private $config;

    /** @var Filesystem  */
    private $files;

    /** @var \Illuminate\View\Factory */
    private $view;

    private $app;

    /**
     * CrudGeneratorCommand constructor.
     * @param $config
     * @param Filesystem $files
     * @param $view
     */
    public function __construct($config, Filesystem $files, $view) {

        $this->config = $config;
        $this->files = $files;
        $this->view = $view;
	    $this->app = app();

	    parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $namespace = trim($this->option('namespace'));

        if($namespace!='') {
            $namespace = explode('\\', trim($namespace,'\\'));

            foreach($namespace as &$name){
                $name = ucfirst(strtolower($name));
            }

            $namespace = implode('\\', $namespace);
        }

	    $version = $this->app->version();

	    if(!in_array(trim(substr($version, 0,3)), ['5.1']) and $this->option('make-auth')) {
		    $this->call('make:auth');
		    $this->call('migrate');
	    }
	    elseif($this->option('migrate')){
		    $this->call('migrate');
	    }

	    $this->config->set('crud-generator.hide_timestamps', $this->option('hide-timestamps'));


	    $explainTable = new ExplainTable();

        $generator = new Generator(
            $explainTable,
            $this->view,
            $this->files,
            $files = $this->config->get('crud-generator.files'),
            $this->option('over-write')
            );

        $models = [];
        foreach($explainTable->getTables() as $table) {

            if($table===false) {
                continue;
            }

            if($this->option('ignore-none-autoincrement') && !$explainTable->hasIdKey($table)) {
                continue;
            }

	        $generator->generateModel($table, $explainTable->getModelName($table), $namespace);
	        $this->comment($explainTable->getModelName($table).' Model has generated');
            $generator->generateController($table, $explainTable->getModelName($table), $namespace);
            $this->comment($explainTable->getModelName($table).' Controller has generated');
            $generator->generateAddEdit($table, $explainTable->getModelName($table), $namespace);
            $this->comment($explainTable->getModelName($table).' add_edit view has generated');
            $generator->generateList($table, $explainTable->getModelName($table), $namespace);
            $this->comment($explainTable->getModelName($table).' list view has generated');

            $models[] = $explainTable->getModelName($table);
        }

        $generator->generateAssets();
        $this->comment('CRUD Assets has generated');


        $generator->generateRouteProvider($models, 'CrudRoute', $namespace);
        $this->comment('Route has generated');



        $this->line('');
        $this->line('add the service provider to the `providers` array in `config/app.php`');
        $this->info('App\Providers\CrudRouteServiceProvider::class' );
        $this->line(PHP_EOL.PHP_EOL."Finish.");
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $namespace = $this->config->get('crud-generator.namespace');

        $version = $this->app->version();

	    $return =array();

	    if(!in_array(trim(substr($version, 0,3)), ['5.1'])) {
		    $return[] = array( 'make-auth', "a", InputOption::VALUE_NONE, 'Execute make:auth and migrate commands' );
	    }

		$return[] = array('migrate', "m", InputOption::VALUE_NONE, 'Execute migrate command');
		$return[] = array('hide-timestamps', "t", InputOption::VALUE_NONE, 'Hide [created_at, updated_at and deleted_at] from add list');
		$return[] = array('over-write', "o", InputOption::VALUE_NONE, 'Allow to overwrite exist files');
		$return[] = array('namespace', "s", InputOption::VALUE_OPTIONAL, 'Chose CRUD namespace', $namespace);
		$return[] = array('ignore-none-autoincrement', "i", InputOption::VALUE_NONE, 'Ignore tables which have no auto-increment field');



        return $return;
    }
}