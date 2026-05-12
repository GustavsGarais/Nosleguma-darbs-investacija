<section aria-label="{{ __('Welcome') }}" class="sim-dash-welcome sim-dash-welcome--frameless">
    <div class="sim-dash-welcome__row sim-dash-list__head">
        <div>
            <h1 class="home-hero-title hero-title">{{ __('Welcome :name!', ['name' => auth()->user()->name]) }}</h1>
            <p class="home-hero-subtitle hero-subtitle sim-dash-welcome__sub">{{ __("You're signed in. Your data is loaded from the database.") }}</p>
        </div>
        <button id="start-tutorial" class="btn btn-secondary" type="button">📚 {{ __('Start Tutorial') }}</button>
    </div>
</section>
