@extends('layouts.admin')

@section('title') LXC Containers @endsection

@section('content-header')
    <h1>LXC Containers<small>All containers provisioned across LXC/Proxmox nodes.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">LXC Containers</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Container List</h3>
                <div class="box-tools">
                    <form action="{{ route('admin.lxc.containers') }}" method="GET" class="input-group input-group-sm" style="display:inline-flex">
                        <input type="text" name="filter[name]" class="form-control" value="{{ request()->input('filter.name') }}" placeholder="Search">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                    <a href="{{ route('admin.lxc.containers.new') }}"><button type="button" class="btn btn-sm btn-primary">Create New</button></a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Owner</th>
                            <th>Node</th>
                            <th>Status</th>
                            <th>Memory</th>
                            <th>Disk</th>
                        </tr>
                        @foreach ($containers as $container)
                            <tr>
                                <td><a href="{{ route('admin.lxc.containers.view', $container->id) }}">{{ $container->name }}</a></td>
                                <td>{{ $container->owner->email ?? 'n/a' }}</td>
                                <td>{{ $container->node->name ?? 'n/a' }}</td>
                                <td><span class="label label-{{ $container->status === 'running' ? 'success' : ($container->status === 'install_failed' ? 'danger' : 'default') }}">{{ $container->status }}</span></td>
                                <td>{{ $container->memory }} MiB</td>
                                <td>{{ $container->disk }} MiB</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($containers->hasPages())
                <div class="box-footer with-border"><div class="col-md-12 text-center">{!! $containers->appends(request()->query())->render() !!}</div></div>
            @endif
        </div>
    </div>
</div>
@endsection
