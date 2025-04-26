<ul class="nav nav-pills mb-3" id="settingsTab" role="tablist">
    @foreach ($config as $table => $settings)
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $settings['enabled'] ? '' : 'not-enabled' }} {{ $loop->first ? 'active' : '' }}"
               id="{{ $table }}-tab" data-bs-toggle="pill"
               href="#{{ $table }}" role="tab" aria-controls="{{ $table }}"
               aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                {{ ucfirst($table) }}
            </a>
        </li>
    @endforeach
</ul>
