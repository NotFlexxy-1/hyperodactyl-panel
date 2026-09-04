@extends('layouts.admin')

@section('title') LXC Nodes @endsection

@section('content-header')
    <h1>LXC Nodes<small>Remote LXD/Proxmox hosts available for container provisioning.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">LXC Nodes</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Node List</h3>
                <div class="box-tools">
                    <a href="{{ route('admin.lxc.nodes.new') }}"><button type="button" class="btn btn-sm btn-primary">Create New</button></a>
                    <a href="{{ route('admin.lxc.containers') }}"><button type="button" class="btn btn-sm btn-default">View Containers</button></a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Driver</th>
                            <th>FQDN</th>
                            <th>Memory</th>
                            <th>Disk</th>
                            <th class="text-center">Containers</th>
                            <th class="text-center">Maintenance</th>
                        </tr>
                        @foreach ($nodes as $node)
                            <tr>
                                <td><a href="{{ route('admin.lxc.nodes.view', $node->id) }}">{{ $node->name }}</a></td>
                                <td>{{ strtoupper($node->driver) }}</td>
                                <td>{{ $node->getConnectionAddress() }}</td>
                                <td>{{ $node->memory }} MiB</td>
                                <td>{{ $node->disk }} MiB</td>
                                <td class="text-center">{{ $node->containers_count }}</td>
                                <td class="text-center">{!! $node->maintenance_mode ? '<span class="label label-warning">Yes</span>' : '<span class="label label-default">No</span>' !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($nodes->hasPages())
                <div class="box-footer with-border"><div class="col-md-12 text-center">{!! $nodes->render() !!}</div></div>
            @endif
        </div>
    </div>
</div>
@endsection
