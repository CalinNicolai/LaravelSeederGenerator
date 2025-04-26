@extends('seedergen::layout')

@section('content')
    <div class="container mt-5">

        @include('seedergen::components.settings.tabs', ['config' => $config])

        <div class="tab-content" id="settingsTabContent">
            @foreach ($config as $table => $settings)

                @include('seedergen::components.settings.tab-content', [
                'config' => $config,
                'settings' => $settings,
                'first' => $loop->first,
                ])

            @endforeach
        </div>
    </div>
@endsection
