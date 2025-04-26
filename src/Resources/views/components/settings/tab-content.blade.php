<div class="tab-pane fade {{ $first ? 'show active' : '' }}" id="{{ $table }}" role="tabpanel"
     aria-labelledby="{{ $table }}-tab">
    <div class="form-section">

        <form action="{{ route('seeder-generator.store', $table) }}" method="POST">
            @csrf
            <h3>
                <input type="hidden" name="{{ $table }}[enabled]" value="0">
                <input type="checkbox" class="form-check-input"
                       name="{{ $table }}[enabled]"
                       id="{{ $table }}_enabled"
                       value="1" @checked($settings['enabled'])>
                {{ ucfirst($table) }} - Настройки полей
            </h3>
            @foreach ($settings['fields'] as $field => $fieldSettings)

                @include('seedergen::components.settings.field',[
                    'table' => $table,
                    'field' => $field,
                    'fieldSettings' => $fieldSettings,
                ])

            @endforeach
            <button type="submit" class="btn btn-primary mt-3">Сохранить настройки</button>
        </form>
    </div>
</div>
