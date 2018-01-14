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
<?php
$viewKey = str_replace('\\', ',', trim($namespace, '\\')).'.'.$modelName;
?>

namespace App\Http\Controllers<?php echo $namespace ?>;

<?php if($namespace!=''): ?>
use App\Http\Controllers\Controller;
<?php endif; ?>
use \Validator;
use Illuminate\Http\Request;

use App<?php echo $namespace.'\\'.$modelName.';' ?>

class <?php echo $modelName ?>Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('<?php echo $viewKey ?>.list')
            ->with('titlePage', '<?php echo $tableTitleName ?>')
            ->with('tableList', <?php echo $modelName ?>::paginate(config('crud-generator.perPage', 10)));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $record = new <?php echo $modelName ?>();
        $record = array_combine($record->getFillable(), array_fill(0, count($record->getFillable()), ''));

        return view('<?php echo $viewKey ?>.add_edit')
            ->with('titlePage', '<?php echo $tableTitleName ?>')
            ->with('title', 'Create New')
            ->with('panelTitle', '<?php echo $tableTitleName ?>')
            ->with('record', $record)
            ->with('update', false)
            ->with('link', route('<?php echo $modelName ?>.store'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->makeValidate($request);

        if ($validator->fails()) {

            return redirect()
                ->route('<?php echo $modelName ?>.create')
                ->withErrors($validator)
                ->withInput();
        }

        <?php echo $modelName ?>::create($request->all());
        return redirect()->route('<?php echo $modelName ?>.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
<?php if($hasID): ?>
        return view('<?php echo $viewKey ?>.add_edit')
            ->with('titlePage', '<?php echo $tableTitleName ?>')
            ->with('title', 'Edit')
            ->with('panelTitle', '<?php echo $tableTitleName ?>')
            ->with('record', <?php echo $modelName ?>::findOrFail($id))
            ->with('update', true)
            ->with('link', route('<?php echo $modelName ?>.update', $id));

<?php endif; ?>
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
<?php if($hasID): ?>
        $validator = $this->makeValidate($request);

        if ($validator->fails()) {

            return redirect()
                ->route('<?php echo $modelName ?>.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        <?php echo $modelName ?>::findOrFail($id)->update($request->all());

        return redirect()->route('<?php echo $modelName ?>.index');
<?php else: ?>
        // Do nothing
<?php endif; ?>
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
<?php if($hasID): ?>
        <?php echo $modelName ?>::destroy($id);
        return redirect()->route('<?php echo $modelName ?>.index');
<?php else: ?>
        // Do nothing
<?php endif; ?>
    }


    private function makeValidate(Request $request)
    {
        return Validator::make($request->all(), [
<?php foreach ($columns as $column):

    if($column['null']||$column['increment']){
        continue;
    }

    $validation ='';

    if($column['type'] == \Dweik\CrudGenerator\Extra\ExplainTable::TYPE_INT){
        $validation='|numeric';
    }
    elseif($column['type'] == \Dweik\CrudGenerator\Extra\ExplainTable::TYPE_DECIMAL){
        $validation='|regex:/[\d\.]+/';
    }
    elseif($column['length'] > 0){
        $validation='|max:255';
    }
    ?>
            '<?php echo $column['field'] ?>' => 'required<?php echo $validation ?>',
<?php endforeach; ?>
        ]);
    }
}
