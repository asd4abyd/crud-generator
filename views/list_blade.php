@extends('crud_main')

@section('content')

    <h1>{{ $titlePage }}</h1>

    <div class="text-right">
        <a href="{{ Route('<?php echo $modelName ?>.create') }}" class="btn btn-primary">Create new</a>
    </div>


    <table class="table table-striped">
        <colgroup>
<?php foreach ($columns as $column): ?>
            <col style="" />
<?php endforeach; ?>
        </colgroup>
        <thead>
        <tr>
<?php foreach ($columns as $column): ?>
            <th scope="col"><?php echo $column['title'] ?></th>
<?php endforeach; ?>

<?php if($hasID): ?>
            <th scope="col">Action</th>
<?php endif; ?>
        </tr>
        </thead>
        <tbody>
        @foreach($tableList as $row)
        <tr>
<?php foreach ($columns as $column): ?>
            <td>{{ $row['<?php echo $column['field'] ?>'] }}</td>
<?php endforeach; ?>
<?php if($hasID): ?>
            <td>
                <form action="{{ Route('<?php echo $modelName ?>.destroy', $row['<?php echo $idKey ?>']) }}" method="post">

                    <a href="{{ Route('<?php echo $modelName ?>.edit', $row['<?php echo $idKey ?>']) }}" class="btn btn-sm btn-warning">Edit</a>

                    <button type="button" class="btn btn-sm btn-danger delete">Delete</button>
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="delete" />
                </form>
            </td>
<?php endif; ?>
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="text-right">{!! $tableList->render() !!}</div>


@endsection