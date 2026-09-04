@extends('layouts.admin')

@section('title') New LXC Container @endsection

@section('content-header')
    <h1>New LXC Container<small>Provision a new container on a node.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.lxc.containers') }}">LXC Containers</a></li>
        <li class="active">New</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('admin.lxc.containers.new') }}" method="POST">
    {{ csrf_field() }}
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Owner</label>
                        <select name="owner_id" class="form-control" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Node</label>
                        <select name="lxc_node_id" class="form-control" required>
                            @foreach ($nodes as $node)
                                <option value="{{ $node->id }}">{{ $node->name }} ({{ strtoupper($node->driver) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Image</label>
                        <input type="text" name="image" class="form-control" placeholder="ubuntu/22.04" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Memory (MiB)</label>
                        <input type="number" name="memory" class="form-control" value="512" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Swap (MiB)</label>
                        <input type="number" name="swap" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Disk (MiB)</label>
                        <input type="number" name="disk" class="form-control" value="2048" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">CPU Limit (cores, 0 = unlimited)</label>
                        <input type="number" name="cpu_limit" class="form-control" value="1">
                    </div>
                    <div class="form-group">
                        <label class="control-label">IO Weight</label>
                        <input type="number" name="io_weight" class="form-control" value="500">
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Provision Container</button>
        </div>
    </div>
</form>
@endsection
