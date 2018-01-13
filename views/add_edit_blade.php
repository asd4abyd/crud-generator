@extends('crud_main')

@section('content')
    <h1>{{ $title }}</h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{{ $panelTitle }}</div>
        </div>
        <div class="panel-body">
            <form class="" role="form" action="{{ $link }}" method="post">
                {{ csrf_field() }}
            @if($update)
                <input name="_method" type="hidden" value="PUT" />
            @endif

                <?php foreach ($columns as $column):
                if($column['increment']){
                    continue;
                }
                ?>

                <div class="form-group form-group-default <?php echo $column['null']?'' : 'required' ?> {{ count($errors->get('<?php echo $column['field'] ?>'))>0? 'has-error': '' }}" >
                    <label for="<?php echo $column['field'] ?>"><?php echo $column['title'].($column['null']?'' : ' *') ?></label>

                    <?php include 'components/'.(\Dweik\CrudGenerator\Extra\ExplainTable::getTypeAlias($column)).'.php' ?>

                    @if (count($errors->get('<?php echo $column['field'] ?>'))>0)
                        <br/>
                        <div class="b-t text-danger">
                            <ul>
                                @foreach ($errors->get('<?php echo $column['field'] ?>') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <?php endforeach; ?>

                <div class="form-group form-group-default disabled text-right">
                    <a href="{{ route('<?php echo $modelName ?>.index') }}" class="btn btn-danger">Cancel</a>
                    <button class="btn btn-complete">Save</button>
                </div>
            </form>
        </div>
    </div>

@endsection