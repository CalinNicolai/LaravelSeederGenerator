<div class="form-group form-section">
    <label for="{{ $table }}_{{ $field }}" class="form-label">
        {{ ucfirst($field) }}
        <span class="fw-light text-secondary">
            [ {{ is_array($fieldSettings) ? $fieldSettings['type'] : $fieldSettings }} ]
        </span>
    </label>

    @php
        $type = is_array($fieldSettings) ? $fieldSettings['type'] : $fieldSettings;
        $attributes = config("field_attributes.$type");
    @endphp

    <input type="hidden" name="{{ $table }}[fields][{{ $field }}][type]"
           value="{{ $fieldSettings['type'] }}">

    @if($type === 'json')
        <div class="border p-3 mt-3">
            <label class="form-label">Структура JSON-поля:</label>

            @php
                $structure = $fieldSettings['structure'] ?? [];
            @endphp

            <div id="json-fields-{{ $table }}-{{ $field }}">
                @foreach($structure as $subFieldKey => $subFieldSettings)
                    <div class="border p-2 mb-2 json-subfield">
                        <div class="mb-2">
                            <label>Название подполя:</label>
                            <input type="text"
                                   name="{{ $table }}[fields][{{ $field }}][structure][{{ $subFieldKey }}][name]"
                                   class="form-control"
                                   value="{{ $subFieldKey }}">
                        </div>

                        <div class="mb-2">
                            <label>Тип подполя:</label>
                            <select name="{{ $table }}[fields][{{ $field }}][structure][{{ $subFieldKey }}][type]"
                                    class="form-select">
                                <option value="string" @selected(($subFieldSettings['type'] ?? '') == 'string')>String
                                </option>
                                <option value="integer" @selected(($subFieldSettings['type'] ?? '') == 'integer')>
                                    Integer
                                </option>
                                <option value="boolean" @selected(($subFieldSettings['type'] ?? '') == 'boolean')>
                                    Boolean
                                </option>
                                <option value="text" @selected(($subFieldSettings['type'] ?? '') == 'text')>Text
                                </option>
                                <option value="name" @selected(($subFieldSettings['type'] ?? '') == 'name')>Name
                                </option>
                                <option value="title" @selected(($subFieldSettings['type'] ?? '') == 'title')>Title
                                </option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Локаль (если нужна):</label>
                            <input type="text"
                                   name="{{ $table }}[fields][{{ $field }}][structure][{{ $subFieldKey }}][locale]"
                                   class="form-control"
                                   value="{{ $subFieldSettings['locale'] ?? '' }}">
                        </div>

                        <button type="button" class="btn btn-danger btn-sm"
                                onclick="this.closest('.json-subfield').remove()">Удалить подполе
                        </button>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-primary btn-sm mt-2"
                    onclick="addJsonSubfield('{{ $table }}', '{{ $field }}')">Добавить подполе
            </button>
        </div>

        <script>
            function addJsonSubfield(table, field) {
                const container = document.getElementById(`json-fields-${table}-${field}`);
                const index = container.children.length;
                const div = document.createElement('div');
                div.classList.add('border', 'p-2', 'mb-2', 'json-subfield');
                div.innerHTML = `
                    <div class="mb-2">
                        <label>Название подполя:</label>
                        <input type="text" name="${table}[fields][${field}][structure][new_${index}][name]" class="form-control" value="">
                    </div>
                    <div class="mb-2">
                        <label>Тип подполя:</label>
                        <select name="${table}[fields][${field}][structure][new_${index}][type]" class="form-select">
                            <option value="string">String</option>
                            <option value="integer">Integer</option>
                            <option value="boolean">Boolean</option>
                            <option value="text">Text</option>
                            <option value="name">Name</option>
                            <option value="title">Title</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Локаль (если нужна):</label>
                        <input type="text" name="${table}[fields][${field}][structure][new_${index}][locale]" class="form-control" value="">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.json-subfield').remove()">Удалить подполе</button>
                `;
                container.appendChild(div);
            }
        </script>
    @elseif($attributes)
        @foreach($attributes as $attributeKey => $attribute)
            @php
                $value = $fieldSettings[$attributeKey] ?? null;
            @endphp

            <div class="form-check">
                <label for="{{ $table }}_{{ $field }}_{{ $attributeKey }}_enabled"
                       class="form-check-label">{{ ucfirst($attributeKey) }}</label>
                @if(!in_array($attribute['type'], ['dropdown', 'json']))
                    <input type="{{ $attribute['type'] }}" class="form-control mt-2"
                           name="{{ $table }}[fields][{{ $field }}][{{ $attributeKey }}]"
                           id="{{ $table }}_{{ $field }}_{{ $attributeKey }}"
                           value="{{ $value ?? '' }}"
                           placeholder="{{ $attribute['placeholder'] ?? 'Some text' }}">
                @elseif($attribute['type'] === 'dropdown')
                    <select name="{{ $table }}[fields][{{ $field }}][{{ $attributeKey }}]"
                            id="{{ $table }}_{{ $field }}_{{ $attributeKey }}"
                            class="form-select mt-2">
                        @foreach($attribute['options'] as $optionKey => $option)
                            <option value="{{ $optionKey }}" @selected($value == $optionKey)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach
    @endif
</div>
