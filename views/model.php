<?php echo "<?php" ?>

/**
* A auto generate Controller for Laravel 5, to serve <<?php echo $tableName ?>> table
* Generated for Laravel <?= $version ?> on <?= date("Y-m-d") ?>.
*
* @author   Abdelqader Osama Al Dweik <asd.abyd@gmail.com>
* @see      https://github.com/asd4abyd/crud-generator
* @see      http://abyd.net
*
*/

namespace App<?php echo $namespace ?>;

use Illuminate\Database\Eloquent\Model;


/**
* Class <?php echo $modelName ?>
* @package App<?php echo $namespace ?>
*
* @mixin \Eloquent
*/
class <?php echo $modelName ?> extends Model
{

    <?php
if(!(in_array('created_at', array_column($columns->toArray(), 'field')) && in_array('updated_at', array_column($columns->toArray(), 'field')))){
    echo 'public $timestamps = false;';
}
?>

<?php if($writeTableName): ?>
    protected $table = '<?php echo $table ?>';

<?php endif; ?>

    protected $fillable =
        ['<?php

$fields = [];

foreach ($columns as $column){
    if($column['increment']){
        continue;
    }
    $fields[] = $column['field'];
}

echo implode("', '", $fields);

?>'];

}
