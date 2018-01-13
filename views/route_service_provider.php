<?php echo "<?php" ?>

namespace App\Providers;

//use App\Http\Controllers\AccIndependentReviewerController;
use Route;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class CrudRouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'App\Http\Controllers';

    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }

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
