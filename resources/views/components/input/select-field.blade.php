<label class="form-label w-100" for="{{ $id }}">{{ $label }}</label>
<select id="{{ $id }}" name="{{ $id }}" class="form-control w-100" {{ $prop }}>
    <option value=""></option>
    <!-- Options will be loaded via AJAX or Customized -->
    {{ $slot }}

    @if($options)
        @foreach($options as $option)
            <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
        @endforeach
    @endif
</select>