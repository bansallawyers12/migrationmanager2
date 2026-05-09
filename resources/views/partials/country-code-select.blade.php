@php
    use App\Helpers\PhoneHelper;
    use App\Models\Country;

    $selectClass = $selectClass ?? 'country-code-input';
    $showPlaceholder = $showPlaceholder ?? true;
    $placeholderLabel = $placeholderLabel ?? 'Select';

    $selectedRaw = $selected ?? null;
    $selectedNorm = ($selectedRaw !== null && trim((string) $selectedRaw) !== '')
        ? PhoneHelper::normalizeCountryCode($selectedRaw)
        : null;

    $preferred = Country::getPreferredCountries();
    $preferredIds = $preferred->pluck('id')->map(fn ($id) => (int) $id)->all();
    $allCountries = Country::getAllWithPhoneCodes();

    $listedCodes = [];
@endphp
<select class="{{ $selectClass }} {{ $wrapperClass ?? '' }}" name="{{ $name }}">
    @if($showPlaceholder)
        <option value="">{{ $placeholderLabel }}</option>
    @endif
    @if($preferred->count())
        <optgroup label="Popular">
            @foreach($preferred as $country)
                @php
                    $dial = '+' . $country->phonecode;
                    $listedCodes[$dial] = true;
                @endphp
                <option value="{{ $dial }}" {{ $selectedNorm !== null && $selectedNorm === $dial ? 'selected' : '' }}>
                    {{ $dial }} ({{ $country->name }})
                </option>
            @endforeach
        </optgroup>
    @endif
    <optgroup label="All countries">
        @foreach($allCountries as $country)
            @if(! in_array((int) $country->id, $preferredIds, true))
                @php
                    $dial = '+' . $country->phonecode;
                    $listedCodes[$dial] = true;
                @endphp
                <option value="{{ $dial }}" {{ $selectedNorm !== null && $selectedNorm === $dial ? 'selected' : '' }}>
                    {{ $dial }} ({{ $country->name }})
                </option>
            @endif
        @endforeach
    </optgroup>
    @if($selectedNorm && ! isset($listedCodes[$selectedNorm]))
        <option value="{{ $selectedNorm }}" selected>{{ $selectedNorm }} (current)</option>
    @endif
</select>
