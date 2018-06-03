<?php echo "<?php" ?>

namespace App\Providers;

use Route;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class CrudRouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'App\Http\Controllers';

<?php if(in_array(trim(substr($version, 0,3)), ['5.1', '5.2'])) { ?>
    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }
<?php } else { ?>
    public function boot()
    {
        //

        parent::boot();
    }
<?php } ?>

    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
<?php foreach($models as $modelName): ?>
            Route::resource('/<?php echo $modelName ?>', '<?php
                if($namespace!=''){
                    echo trim($namespace,'\\').'\\';
                }
                echo $modelName; ?>Controller');
<?php endforeach; ?>
        });
    }
}
