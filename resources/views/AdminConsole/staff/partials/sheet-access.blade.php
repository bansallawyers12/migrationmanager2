@php
    $sheetSelection = old('sheet_access', $selectedSheetKeys);
    if (! is_array($sheetSelection)) {
        $sheetSelection = [];
    }
@endphp
<div class="form-group">
    <label>CRM sheets</label>
    <p class="text-muted small mb-2">Choose which sheets this user can open. Leave all selected for full access (same as before this setting existed).</p>
    <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
        @foreach ($sheetDefinitions as $sheetKey => $sheetLabel)
            <div class="custom-control custom-checkbox mb-2">
                <input type="checkbox" class="custom-control-input" id="sheet_access_{{ $sheetKey }}" name="sheet_access[]" value="{{ $sheetKey }}"
                    {{ in_array($sheetKey, $sheetSelection, true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="sheet_access_{{ $sheetKey }}">{{ $sheetLabel }}</label>
            </div>
        @endforeach
    </div>
</div>
