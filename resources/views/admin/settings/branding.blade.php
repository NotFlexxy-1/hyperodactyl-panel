@extends('layouts.admin')
@include('partials/admin.settings.nav', ['activeTab' => 'branding'])

@section('title')
    Branding & Theme
@endsection

@section('content-header')
    <h1>Branding & Theme<small>Customize the look, feel and layout of your panel.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.settings') }}">Settings</a></li>
        <li class="active">Branding & Theme</li>
    </ol>
@endsection

@section('content')
    @yield('settings::nav')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-identity" data-toggle="tab">Identity</a></li>
                    <li><a href="#tab-meta" data-toggle="tab">Meta / Social</a></li>
                    <li><a href="#tab-colors" data-toggle="tab">Colors</a></li>
                    <li><a href="#tab-layout" data-toggle="tab">Layout</a></li>
                    <li><a href="#tab-permissions" data-toggle="tab">Permissions</a></li>
                </ul>
                <div class="tab-content">
                    {{-- Identity --}}
                    <div class="tab-pane active" id="tab-identity">
                        <form action="{{ route('admin.settings.branding.update') }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PATCH">
                            <div class="box-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Site Name</label>
                                        <input type="text" class="form-control" name="site_name" value="{{ old('site_name', $branding['site_name']) }}" required>
                                        <p class="text-muted small">Displayed in the title bar, headers and emails.</p>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Short Name</label>
                                        <input type="text" class="form-control" name="site_short_name" value="{{ old('site_short_name', $branding['site_short_name']) }}">
                                        <p class="text-muted small">Used for compact UI elements (e.g. collapsed sidebar).</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Logo</label>
                                        <div>
                                            @if($branding['logo_url'])
                                                <img src="{{ $branding['logo_url'] }}" alt="Logo" style="max-height:48px;display:block;margin-bottom:10px;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Favicon</label>
                                        <div>
                                            @if($branding['favicon_url'])
                                                <img src="{{ $branding['favicon_url'] }}" alt="Favicon" style="max-height:32px;display:block;margin-bottom:10px;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- Hidden fields to preserve other tab values so a single-tab submit doesn't wipe settings --}}
                                @foreach(['social_description', 'meta_keywords', 'og_image_url', 'color_primary', 'color_accent', 'color_background', 'color_surface', 'color_text', 'color_danger', 'color_success', 'border_radius', 'font'] as $key)
                                    <input type="hidden" name="{{ $key }}" value="{{ old($key, $branding[$key]) }}">
                                @endforeach
                                <input type="hidden" name="sidebar_layout" value="{{ old('sidebar_layout', json_encode($branding['sidebar_layout'])) }}">
                                <input type="hidden" name="dashboard_widgets" value="{{ old('dashboard_widgets', json_encode($branding['dashboard_widgets'])) }}">
                                <input type="hidden" name="allow_user_theme_override" value="{{ old('allow_user_theme_override', $branding['allow_user_theme_override'] ? 1 : 0) }}">
                                <input type="hidden" name="user_customizable_keys" value="{{ old('user_customizable_keys', json_encode($branding['user_customizable_keys'])) }}">
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-sm btn-primary pull-right">Save Identity</button>
                            </div>
                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Upload Logo</h4>
                                <form action="{{ route('admin.settings.branding.logo') }}" method="POST" enctype="multipart/form-data">
                                    {!! csrf_field() !!}
                                    <div class="form-group">
                                        <input type="file" name="file" accept=".png,.jpg,.jpeg,.svg,.webp" required>
                                        <p class="text-muted small">PNG, JPG, SVG or WEBP. Max 2MB.</p>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">Upload Logo</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h4>Upload Favicon</h4>
                                <form action="{{ route('admin.settings.branding.favicon') }}" method="POST" enctype="multipart/form-data">
                                    {!! csrf_field() !!}
                                    <div class="form-group">
                                        <input type="file" name="file" accept=".png,.ico,.svg,.webp" required>
                                        <p class="text-muted small">PNG, ICO, SVG or WEBP. Max 1MB.</p>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">Upload Favicon</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Meta / Social --}}
                    <div class="tab-pane" id="tab-meta">
                        <form action="{{ route('admin.settings.branding.update') }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="site_name" value="{{ $branding['site_name'] }}">
                            <input type="hidden" name="site_short_name" value="{{ $branding['site_short_name'] }}">
                            <input type="hidden" name="logo_url" value="{{ $branding['logo_url'] }}">
                            <input type="hidden" name="favicon_url" value="{{ $branding['favicon_url'] }}">
                            @foreach(['color_primary', 'color_accent', 'color_background', 'color_surface', 'color_text', 'color_danger', 'color_success', 'border_radius', 'font'] as $key)
                                <input type="hidden" name="{{ $key }}" value="{{ $branding[$key] }}">
                            @endforeach
                            <input type="hidden" name="sidebar_layout" value="{{ json_encode($branding['sidebar_layout']) }}">
                            <input type="hidden" name="dashboard_widgets" value="{{ json_encode($branding['dashboard_widgets']) }}">
                            <input type="hidden" name="allow_user_theme_override" value="{{ $branding['allow_user_theme_override'] ? 1 : 0 }}">
                            <input type="hidden" name="user_customizable_keys" value="{{ json_encode($branding['user_customizable_keys']) }}">
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label">Social Description</label>
                                    <textarea class="form-control" name="social_description" rows="3">{{ old('social_description', $branding['social_description']) }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Meta Keywords</label>
                                    <input type="text" class="form-control" name="meta_keywords" value="{{ old('meta_keywords', $branding['meta_keywords']) }}">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Open Graph Image URL</label>
                                    <input type="text" class="form-control" name="og_image_url" value="{{ old('og_image_url', $branding['og_image_url']) }}">
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-sm btn-primary pull-right">Save Meta / Social</button>
                            </div>
                        </form>
                    </div>

                    {{-- Colors --}}
                    <div class="tab-pane" id="tab-colors">
                        <form action="{{ route('admin.settings.branding.update') }}" method="POST" id="colorsForm">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="site_name" value="{{ $branding['site_name'] }}">
                            <input type="hidden" name="site_short_name" value="{{ $branding['site_short_name'] }}">
                            <input type="hidden" name="logo_url" value="{{ $branding['logo_url'] }}">
                            <input type="hidden" name="favicon_url" value="{{ $branding['favicon_url'] }}">
                            <input type="hidden" name="social_description" value="{{ $branding['social_description'] }}">
                            <input type="hidden" name="meta_keywords" value="{{ $branding['meta_keywords'] }}">
                            <input type="hidden" name="og_image_url" value="{{ $branding['og_image_url'] }}">
                            <input type="hidden" name="sidebar_layout" value="{{ json_encode($branding['sidebar_layout']) }}">
                            <input type="hidden" name="dashboard_widgets" value="{{ json_encode($branding['dashboard_widgets']) }}">
                            <input type="hidden" name="allow_user_theme_override" value="{{ $branding['allow_user_theme_override'] ? 1 : 0 }}">
                            <input type="hidden" name="user_customizable_keys" value="{{ json_encode($branding['user_customizable_keys']) }}">
                            <div class="box-body">
                                <div class="row">
                                    @foreach([
                                        'color_primary' => 'Primary',
                                        'color_accent' => 'Accent',
                                        'color_background' => 'Background',
                                        'color_surface' => 'Surface',
                                        'color_text' => 'Text',
                                        'color_danger' => 'Danger',
                                        'color_success' => 'Success',
                                    ] as $key => $label)
                                        <div class="form-group col-md-3">
                                            <label class="control-label">{{ $label }}</label>
                                            <div style="display:flex;align-items:center;">
                                                <input type="color" class="theme-color-input" data-var="--hyper-{{ str_replace('color_', '', $key) }}" value="{{ old($key, $branding[$key]) }}" style="width:44px;height:34px;padding:0;border:none;margin-right:8px;">
                                                <input type="text" class="form-control theme-color-text" name="{{ $key }}" value="{{ old($key, $branding[$key]) }}">
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="form-group col-md-3">
                                        <label class="control-label">Border Radius</label>
                                        <input type="text" class="form-control" name="border_radius" id="border_radius" value="{{ old('border_radius', $branding['border_radius']) }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="control-label">Font Family</label>
                                        <input type="text" class="form-control" name="font" value="{{ old('font', $branding['font']) }}">
                                    </div>
                                </div>
                                <hr>
                                <h4>Live Preview</h4>
                                <div id="theme-preview" style="border:1px solid #444;padding:20px;background:var(--hyper-background);color:var(--hyper-text);border-radius:var(--hyper-radius);">
                                    <button type="button" style="background:var(--hyper-primary);color:#fff;border:none;padding:8px 16px;border-radius:var(--hyper-radius);margin-right:10px;">Primary Button</button>
                                    <button type="button" style="background:var(--hyper-accent);color:#fff;border:none;padding:8px 16px;border-radius:var(--hyper-radius);margin-right:10px;">Accent Button</button>
                                    <span style="background:var(--hyper-surface);padding:8px 16px;border-radius:var(--hyper-radius);display:inline-block;">Surface Card</span>
                                    <div style="margin-top:10px;">
                                        <span style="color:var(--hyper-danger);margin-right:10px;">Danger text</span>
                                        <span style="color:var(--hyper-success);">Success text</span>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-sm btn-primary pull-right">Save Colors</button>
                            </div>
                        </form>
                    </div>

                    {{-- Layout --}}
                    <div class="tab-pane" id="tab-layout">
                        <form action="{{ route('admin.settings.branding.update') }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="site_name" value="{{ $branding['site_name'] }}">
                            <input type="hidden" name="site_short_name" value="{{ $branding['site_short_name'] }}">
                            <input type="hidden" name="logo_url" value="{{ $branding['logo_url'] }}">
                            <input type="hidden" name="favicon_url" value="{{ $branding['favicon_url'] }}">
                            <input type="hidden" name="social_description" value="{{ $branding['social_description'] }}">
                            <input type="hidden" name="meta_keywords" value="{{ $branding['meta_keywords'] }}">
                            <input type="hidden" name="og_image_url" value="{{ $branding['og_image_url'] }}">
                            @foreach(['color_primary', 'color_accent', 'color_background', 'color_surface', 'color_text', 'color_danger', 'color_success', 'border_radius', 'font'] as $key)
                                <input type="hidden" name="{{ $key }}" value="{{ $branding[$key] }}">
                            @endforeach
                            <input type="hidden" name="allow_user_theme_override" value="{{ $branding['allow_user_theme_override'] ? 1 : 0 }}">
                            <input type="hidden" name="user_customizable_keys" value="{{ json_encode($branding['user_customizable_keys']) }}">

                            <div class="box-body">
                                <h4>Sidebar Navigation Order</h4>
                                <p class="text-muted small">Toggle visibility and set the display order (lower numbers show first).</p>
                                <table class="table">
                                    <thead><tr><th>Enabled</th><th>Label</th><th>Key</th><th>Order</th></tr></thead>
                                    <tbody>
                                        @foreach($branding['sidebar_layout'] as $index => $item)
                                            <tr>
                                                <td><input type="checkbox" data-layout="sidebar" data-index="{{ $index }}" data-field="enabled" @checked($item['enabled'])></td>
                                                <td><input type="text" class="form-control input-sm" data-layout="sidebar" data-index="{{ $index }}" data-field="label" value="{{ $item['label'] }}"></td>
                                                <td>{{ $item['key'] }}</td>
                                                <td><input type="number" class="form-control input-sm" style="width:80px" data-layout="sidebar" data-index="{{ $index }}" data-field="order" value="{{ $item['order'] }}"></td>
                                                <input type="hidden" data-layout="sidebar" data-index="{{ $index }}" data-field="icon" value="{{ $item['icon'] }}">
                                                <input type="hidden" data-layout="sidebar" data-index="{{ $index }}" data-field="key" value="{{ $item['key'] }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <h4>Dashboard Widgets</h4>
                                <table class="table">
                                    <thead><tr><th>Enabled</th><th>Key</th><th>Order</th></tr></thead>
                                    <tbody>
                                        @foreach($branding['dashboard_widgets'] as $index => $widget)
                                            <tr>
                                                <td><input type="checkbox" data-layout="widgets" data-index="{{ $index }}" data-field="enabled" @checked($widget['enabled'])></td>
                                                <td>{{ $widget['key'] }}</td>
                                                <td><input type="number" class="form-control input-sm" style="width:80px" data-layout="widgets" data-index="{{ $index }}" data-field="order" value="{{ $widget['order'] }}"></td>
                                                <input type="hidden" data-layout="widgets" data-index="{{ $index }}" data-field="key" value="{{ $widget['key'] }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" name="sidebar_layout" id="sidebar_layout_input" value="{{ json_encode($branding['sidebar_layout']) }}">
                                <input type="hidden" name="dashboard_widgets" id="dashboard_widgets_input" value="{{ json_encode($branding['dashboard_widgets']) }}">
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-sm btn-primary pull-right" id="layoutSubmit">Save Layout</button>
                            </div>
                        </form>
                    </div>

                    {{-- Permissions --}}
                    <div class="tab-pane" id="tab-permissions">
                        <form action="{{ route('admin.settings.branding.update') }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="site_name" value="{{ $branding['site_name'] }}">
                            <input type="hidden" name="site_short_name" value="{{ $branding['site_short_name'] }}">
                            <input type="hidden" name="logo_url" value="{{ $branding['logo_url'] }}">
                            <input type="hidden" name="favicon_url" value="{{ $branding['favicon_url'] }}">
                            <input type="hidden" name="social_description" value="{{ $branding['social_description'] }}">
                            <input type="hidden" name="meta_keywords" value="{{ $branding['meta_keywords'] }}">
                            <input type="hidden" name="og_image_url" value="{{ $branding['og_image_url'] }}">
                            @foreach(['color_primary', 'color_accent', 'color_background', 'color_surface', 'color_text', 'color_danger', 'color_success', 'border_radius', 'font'] as $key)
                                <input type="hidden" name="{{ $key }}" value="{{ $branding[$key] }}">
                            @endforeach
                            <input type="hidden" name="sidebar_layout" value="{{ json_encode($branding['sidebar_layout']) }}">
                            <input type="hidden" name="dashboard_widgets" value="{{ json_encode($branding['dashboard_widgets']) }}">

                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label">Allow users to customize their own theme</label>
                                    <div>
                                        <select class="form-control" name="allow_user_theme_override" style="max-width:200px;">
                                            <option value="0" @if(!$branding['allow_user_theme_override']) selected @endif>Disabled</option>
                                            <option value="1" @if($branding['allow_user_theme_override']) selected @endif>Enabled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">User Customizable Keys</label>
                                    <div>
                                        @foreach(['color_primary', 'color_accent', 'color_background', 'color_surface', 'color_text', 'color_danger', 'color_success'] as $key)
                                            <label class="checkbox-inline">
                                                <input type="checkbox" class="user-key-checkbox" value="{{ $key }}" @checked(in_array($key, $branding['user_customizable_keys']))>
                                                {{ $key }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-muted small">Only these settings can be overridden by individual users in their account appearance page.</p>
                                    <input type="hidden" name="user_customizable_keys" id="user_customizable_keys_input" value="{{ json_encode($branding['user_customizable_keys']) }}">
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-sm btn-primary pull-right">Save Permissions</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        (function () {
            // Live color preview: update CSS vars as colors are edited.
            document.querySelectorAll('.theme-color-input').forEach(function (input) {
                input.addEventListener('input', function () {
                    document.documentElement.style.setProperty(input.dataset.var, input.value);
                    var textSibling = input.parentElement.querySelector('.theme-color-text');
                    if (textSibling) textSibling.value = input.value;
                });
            });
            document.querySelectorAll('.theme-color-text').forEach(function (input) {
                input.addEventListener('input', function () {
                    var colorInput = input.parentElement.querySelector('.theme-color-input');
                    if (colorInput && /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(input.value)) {
                        colorInput.value = input.value;
                        document.documentElement.style.setProperty(colorInput.dataset.var, input.value);
                    }
                });
            });
            var radiusInput = document.getElementById('border_radius');
            if (radiusInput) {
                radiusInput.addEventListener('input', function () {
                    document.documentElement.style.setProperty('--hyper-radius', radiusInput.value);
                });
            }

            // Layout tab: serialize sidebar/widget rows into hidden JSON inputs before submit.
            var layoutForm = document.getElementById('sidebar_layout_input') ? document.getElementById('sidebar_layout_input').closest('form') : null;
            if (layoutForm) {
                layoutForm.addEventListener('submit', function () {
                    function collect(prefix) {
                        var rows = {};
                        document.querySelectorAll('[data-layout="' + prefix + '"]').forEach(function (el) {
                            var idx = el.dataset.index;
                            rows[idx] = rows[idx] || {};
                            var field = el.dataset.field;
                            var value;
                            if (el.type === 'checkbox') {
                                value = el.checked;
                            } else if (field === 'order') {
                                value = parseInt(el.value, 10) || 0;
                            } else {
                                value = el.value;
                            }
                            rows[idx][field] = value;
                        });
                        return Object.keys(rows).sort(function (a, b) { return a - b; }).map(function (k) { return rows[k]; });
                    }
                    document.getElementById('sidebar_layout_input').value = JSON.stringify(collect('sidebar'));
                    document.getElementById('dashboard_widgets_input').value = JSON.stringify(collect('widgets'));
                });
            }

            // Permissions tab: serialize checked user-customizable keys before submit.
            var keysInput = document.getElementById('user_customizable_keys_input');
            if (keysInput) {
                var permForm = keysInput.closest('form');
                permForm.addEventListener('submit', function () {
                    var checked = Array.prototype.slice.call(document.querySelectorAll('.user-key-checkbox:checked')).map(function (el) { return el.value; });
                    keysInput.value = JSON.stringify(checked);
                });
            }
        })();
    </script>
@endsection
