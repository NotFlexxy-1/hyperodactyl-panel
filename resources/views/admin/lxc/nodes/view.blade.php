@extends('layouts.admin')

@section('title') LXC Node: {{ $node->name }} @endsection

@section('content-header')
    <h1>{{ $node->name }}<small>{{ $node->getConnectionAddress() }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.lxc.nodes') }}">LXC Nodes</a></li>
        <li class="active">{{ $node->name }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-{{ $error ? 'danger' : 'success' }}">
            <div class="box-header with-border"><h3 class="box-title">Real-Time Node Health</h3></div>
            <div class="box-body">
                @if ($error)
                    <div class="alert alert-danger">Unable to reach node: {{ $error }}</div>
                @else
                    <div class="row text-center">
                        <div class="col-md-3"><strong>CPU</strong><br>{{ $usage['cpu'] ?? 'n/a' }}</div>
                        <div class="col-md-3"><strong>Memory</strong><br>{{ $usage['memory'] ?? 'n/a' }}</div>
                        <div class="col-md-3"><strong>Disk</strong><br>{{ $usage['disk'] ?? 'n/a' }}</div>
                        <div class="col-md-3"><strong>Containers</strong><br>{{ $node->containers()->count() }}</div>
                    </div>
                    <pre>{{ json_encode($usage, JSON_PRETTY_PRINT) }}</pre>
                @endif
            </div>
        </div>
    </div>
</div>
<form action="{{ route('admin.lxc.nodes.view', $node->id) }}" method="POST">
    {{ method_field('PATCH') }}
    @php($node = $node)
    @include('admin.lxc.nodes._form')
    <div class="box-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="submit" formaction="{{ route('admin.lxc.nodes.view', $node->id) }}" formmethod="POST" onclick="event.preventDefault(); if(confirm('Delete this node?')) { let f=document.createElement('form'); f.method='POST'; f.action=this.form.action; f.innerHTML='@csrf @method(\'DELETE\')'; document.body.appendChild(f); f.submit(); }" class="btn btn-danger pull-right">Delete Node</button>
    </div>
</form>
@endsection
