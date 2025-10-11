@if (count($documentTemplateFilev2))
@foreach ($documentTemplateFilev2 as $index => $template)
    <tr class="filter-item filter-item-{{ $template->type }}">
        <td class="text-center">
            <div class="checkbox">
                <input type="checkbox" name="files" value="{{ $template->id }}"
                    id="chk_{{ $template->id }}">
                <label for="chk_{{ $template->id }}">
                    {{ $index + 1 }}</label>
            </div>
        </td>
        <td>{{ $template->name }}</td>
    </tr>
@endforeach
@endif