{{ csrf_field() }}
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Basic Information</h3></div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $node->name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $node->description ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="control-label">Driver</label>
                    <select name="driver" class="form-control">
                        <option value="lxd" @selected(old('driver', $node->driver ?? 'lxd') === 'lxd')>LXD</option>
                        <option value="proxmox" @selected(old('driver', $node->driver ?? '') === 'proxmox')>Proxmox</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Proxmox Node Name (if Proxmox)</label>
                    <input type="text" name="proxmox_node" class="form-control" value="{{ old('proxmox_node', $node->proxmox_node ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="checkbox-inline"><input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $node->maintenance_mode ?? false))> Maintenance Mode</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Connection</h3></div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label">FQDN / IP</label>
                    <input type="text" name="fqdn" class="form-control" value="{{ old('fqdn', $node->fqdn ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Scheme</label>
                    <select name="scheme" class="form-control">
                        <option value="https" @selected(old('scheme', $node->scheme ?? 'https') === 'https')>https</option>
                        <option value="http" @selected(old('scheme', $node->scheme ?? '') === 'http')>http</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Port</label>
                    <input type="number" name="port" class="form-control" value="{{ old('port', $node->port ?? 8443) }}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">API Token {{ isset($node) ? '(leave blank to keep current)' : '' }}</label>
                    <input type="password" name="api_token" class="form-control" {{ isset($node) ? '' : 'required' }}>
                </div>
                <div class="form-group">
                    <label class="control-label">API Secret (Proxmox token secret, optional for LXD)</label>
                    <input type="password" name="api_secret" class="form-control">
                </div>
                <div class="form-group">
                    <label class="checkbox-inline"><input type="checkbox" name="tls_verify" value="1" @checked(old('tls_verify', $node->tls_verify ?? true))> Verify TLS Certificate</label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Storage / Network</h3></div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label">Storage Pool</label>
                    <input type="text" name="storage_pool" class="form-control" value="{{ old('storage_pool', $node->storage_pool ?? 'default') }}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Network Bridge</label>
                    <input type="text" name="network_bridge" class="form-control" value="{{ old('network_bridge', $node->network_bridge ?? 'lxdbr0') }}" required>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Capacity (real limits enforced on provisioning)</h3></div>
            <div class="box-body">
                <div class="form-group">
                    <label class="control-label">Memory (MiB)</label>
                    <input type="number" name="memory" class="form-control" value="{{ old('memory', $node->memory ?? 0) }}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Memory Overallocate (%)</label>
                    <input type="number" name="memory_overallocate" class="form-control" value="{{ old('memory_overallocate', $node->memory_overallocate ?? 0) }}">
                </div>
                <div class="form-group">
                    <label class="control-label">Disk (MiB)</label>
                    <input type="number" name="disk" class="form-control" value="{{ old('disk', $node->disk ?? 0) }}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Disk Overallocate (%)</label>
                    <input type="number" name="disk_overallocate" class="form-control" value="{{ old('disk_overallocate', $node->disk_overallocate ?? 0) }}">
                </div>
                <div class="form-group">
                    <label class="control-label">CPU (cores)</label>
                    <input type="number" name="cpu" class="form-control" value="{{ old('cpu', $node->cpu ?? 0) }}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">CPU Overallocate (%)</label>
                    <input type="number" name="cpu_overallocate" class="form-control" value="{{ old('cpu_overallocate', $node->cpu_overallocate ?? 0) }}">
                </div>
            </div>
        </div>
    </div>
</div>
