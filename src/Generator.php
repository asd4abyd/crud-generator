<?php
/**
 * Created by PhpStorm.
 * User: Abdelqader Osama
 * Date: 13/01/18
 * Time: 12:48
 */

namespace Dweik\CrudGenerator;

use Dweik\CrudGenerator\Extra\ExplainTable;

class Generator {

    /** @var ExplainTable */
    private $explainTable;

    /** @var \Illuminate\Filesystem\Filesystem */
    private $files;

    /** @var \Illuminate\View\Factory */
    private $view;

    private $viewFiles = [];

    /** @var bool */
    private $overwrite;

    private $controllerPath = '';
    private $modelPath      = '';
    private $viewPath       = '';
    private $providerPath   = '';


    public function __construct(&$explainTable, $view, $files, $viewFiles, $overwrite=false) {
        $this->explainTable = $explainTable;
        $this->view = $view;
        $this->files = $files;
        $this->viewFiles = $viewFiles;
        $this->overwrite = $overwrite;

        $this->controllerPath = app_path('Http/Controllers');
        $this->modelPath = app_path();
        $viewPath = config('view.paths');
        $this->viewPath = isset($viewPath[0])? $viewPath[0]: realpath(base_path('resources/views'));
        $this->providerPath = app_path('Providers');
    }


    public function generateController($table, $filename, $namespace='') {

        $this->createPath($controllerPath = $this->controllerPath.$this->namespaceToPath($namespace));

        $this->saveFile($controllerPath.DIRECTORY_SEPARATOR.$filename.'Controller.php', $this->getController($namespace, $table));
    }

    public function getController($namespace, $table) {

        if($namespace!='') {
            $namespace = '\\' . trim($namespace);
        }

        $app = app();
        return $this->view->make($this->viewFiles['controller'])
            ->with('namespace', $namespace)
            ->with('tableName', $this->explainTable->getTableName($table))
            ->with('modelName', $this->explainTable->getModelName($table))
            ->with('tableTitleName', str_replace('_', ' ', $this->explainTable->getTableName($table)))
            ->with('hasID', $this->explainTable->hasIdKey($table))
            ->with('columns', $this->explainTable->getColumns($table))
            ->with('version', $app->version())
            ->render();
    }


    public function generateModel($table, $filename, $namespace='') {

        $this->createPath($modelPath = $this->modelPath.$this->namespaceToPath($namespace));

        $this->saveFile($modelPath.DIRECTORY_SEPARATOR.$filename.'.php', $this->getModel($namespace, $table));
    }

    public function getModel($namespace, $table) {

        if($namespace!='') {
            $namespace = '\\' . trim($namespace);
        }

        $app = app();
        return $this->view->make($this->viewFiles['model'])
            ->with('namespace', $namespace)
            ->with('table', $table)
            ->with('writeTableName', $table != $this->explainTable->getTableName($table))
            ->with('tableName', $this->explainTable->getTableName($table))
            ->with('modelName', $this->explainTable->getModelName($table))
            ->with('columns', $this->explainTable->getColumns($table))
            ->with('version', $app->version())
            ->render();
    }


    public function generateAddEdit($table, $folderName, $namespace='') {

        $this->createPath($viewPath = $this->viewPath.DIRECTORY_SEPARATOR.$this->namespaceToPath($namespace).DIRECTORY_SEPARATOR.$folderName);

        $this->saveFile($viewPath.DIRECTORY_SEPARATOR.'add_edit.blade.php', $this->getAddEdit($table));
    }

    public function getAddEdit($table) {

        return $this->view->make($this->viewFiles['add_edit'])
            ->with('modelName', $this->explainTable->getModelName($table))
            ->with('columns', $this->explainTable->getColumns($table))
            ->render();
    }


    public function generateList($table, $folderName, $namespace='') {

        $this->createPath($viewPath = $this->viewPath.DIRECTORY_SEPARATOR.$this->namespaceToPath($namespace).DIRECTORY_SEPARATOR.$folderName);

        $this->saveFile($viewPath.DIRECTORY_SEPARATOR.'list.blade.php', $this->getList($table));
    }

    public function getList($table) {

        return $this->view->make($this->viewFiles['list'])
            ->with('modelName', $this->explainTable->getModelName($table))
            ->with('hasID', $this->explainTable->hasIdKey($table))
            ->with('columns', $this->explainTable->getColumns($table))
            ->with('idKey', $this->explainTable->getIdKey($table))
            ->render();
    }


    public function generateRouteProvider($models, $filename, $namespace='') {
        $this->saveFile($this->providerPath.DIRECTORY_SEPARATOR.$filename.'ServiceProvider.php', $this->getRouteProvider($namespace, $models));
    }

    public function getRouteProvider($namespace, $models) {

        return $this->view->make($this->viewFiles['route'])
            ->with('models', $models)
            ->with('namespace', $namespace)
            ->render();
    }


    public function generateAssets() {
        $this->saveFile($this->viewPath.DIRECTORY_SEPARATOR.'crud_main.blade.php', $this->getAssets('main'));
        $this->saveFile($this->viewPath.DIRECTORY_SEPARATOR.'javascript.blade.php', $this->getAssets('javascript'));
    }

    public function getAssets($type) {
        return $this->view->make($this->viewFiles[$type])
            ->render();
    }


    private function saveFile($path, $content){
        if(!file_exists($path) || $this->overwrite ){
            $this->files->put($path, $content);
        }

    }

    private function createPath($path){
        if(!file_exists($path)){
            mkdir($path, 0777, true);
        }
    }

    protected function namespaceToPath($namespace){
        if($namespace==''){
            return '';
        }

        $namespace = trim($namespace, '\\');

        return DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }
}