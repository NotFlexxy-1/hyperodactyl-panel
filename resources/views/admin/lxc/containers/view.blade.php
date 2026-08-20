@extends('layouts.admin')

@section('title') Container: {{ $container->name }} @endsection

@section('content-header')
    <h1>{{ $container->name }}<small>{{ $container->uuid }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.lxc.containers') }}">LXC Containers</a></li>
        <li class="active">{{ $container->name }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Status &amp; Power</h3></div>
            <div class="box-body">
                <p>Status: <span class="label label-{{ $container->status === 'running' ? 'success' : 'default' }}">{{ $container->status }}</span></p>
                <p>Node: {{ $container->node->name }} ({{ strtoupper($container->node->driver) }})</p>
                <p>Owner: {{ $container->owner->email ?? 'n/a' }}</p>
                @if ($error)
                    <div class="alert alert-danger">Unable to fetch live resources: {{ $error }}</div>
                @else
                    <pre>{{ json_encode($usage, JSON_PRETTY_PRINT) }}</pre>
                @endif
                <form action="{{ route('admin.lxc.containers.power', $container->id) }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <select name="action" class="form-control">
                        <option value="start">Start</option>
                        <option value="stop">Stop</option>
                        <option value="restart">Restart</option>
                        <option value="freeze">Freeze</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
        <div class="box box-warning">
            <div class="box-header with-border"><h3 class="box-title">Reassign Owner</h3></div>
            <div class="box-body">
                <form action="{{ route('admin.lxc.containers.reassign', $container->id) }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <select name="owner_id" class="form-control">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected($user->id === $container->owner_id)>{{ $user->email }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-warning">Reassign</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Limits</h3></div>
            <div class="box-body">
                <form action="{{ route('admin.lxc.containers.view', $container->id) }}" method="POST">
                    {{ csrf_field() }} {{ method_field('PATCH') }}
                    <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $container->name }}"></div>
                    <div class="form-group"><label>Memory (MiB)</label><input type="number" name="memory" class="form-control" value="{{ $container->memory }}"></div>
                    <div class="form-group"><label>Swap (MiB)</label><input type="number" name="swap" class="form-control" value="{{ $container->swap }}"></div>
                    <div class="form-group"><label>Disk (MiB)</label><input type="number" name="disk" class="form-control" value="{{ $container->disk }}"></div>
                    <div class="form-group"><label>CPU Limit</label><input type="number" name="cpu_limit" class="form-control" value="{{ $container->cpu_limit }}"></div>
                    <div class="form-group"><label>IO Weight</label><input type="number" name="io_weight" class="form-control" value="{{ $container->io_weight }}"></div>
                    <button type="submit" class="btn btn-primary">Update Limits</button>
                </form>
            </div>
        </div>
        <div class="box box-danger">
            <div class="box-header with-border"><h3 class="box-title">Danger Zone</h3></div>
            <div class="box-body">
                <form action="{{ route('admin.lxc.containers.view', $container->id) }}" method="POST" onsubmit="return confirm('Delete this container?');">
                    {{ csrf_field() }} {{ method_field('DELETE') }}
                    <label class="checkbox-inline"><input type="checkbox" name="force" value="1"> Force delete (ignore remote node errors)</label><br><br>
                    <button type="submit" class="btn btn-danger">Delete Container</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
