@extends('layouts.admin')

@section('title') New LXC Node @endsection

@section('content-header')
    <h1>New LXC Node<small>Register a new LXD/Proxmox host.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.lxc.nodes') }}">LXC Nodes</a></li>
        <li class="active">New</li>
    </ol>
@endsection

@section('content')
<form action="{{ route('admin.lxc.nodes.new') }}" method="POST">
    @include('admin.lxc.nodes._form')
    <div class="box-footer">
        <button type="submit" class="btn btn-primary">Create Node</button>
    </div>
</form>
@endsection
