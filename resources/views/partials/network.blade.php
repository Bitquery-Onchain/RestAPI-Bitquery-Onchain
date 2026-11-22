<select name="network" class="form-select" aria-label="Jaringan">
    @foreach (config('bitquery.networks') as $key => $label)
        <option value="{{ $key }}" @selected($key === $network)>{{ $label }}</option>
    @endforeach
</select>
