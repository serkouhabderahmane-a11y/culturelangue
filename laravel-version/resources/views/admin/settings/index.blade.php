@extends('layouts.admin')
@section('title', 'Paramètres')
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form">
@csrf @method('PUT')
@foreach($settings as $group => $groupSettings)
<div class="admin-section"><h2>{{ ucfirst($group) }}</h2>
@foreach($groupSettings as $setting)
<div class="form-group"><label for="setting_{{ $setting->key }}">{{ $setting->key }}</label>
@if($setting->type === 'text')
<input type="text" name="settings[{{ $loop->parent->index }}][value]" id="setting_{{ $setting->key }}" value="{{ old('settings.' . $loop->parent->index . '.value', $setting->value) }}" class="form-control">
@endif
<input type="hidden" name="settings[{{ $loop->parent->index }}][key]" value="{{ $setting->key }}">
</div>
@endforeach</div>
@endforeach
<button type="submit" class="btn btn-primary">Enregistrer les paramètres</button>
</form>
@endsection
