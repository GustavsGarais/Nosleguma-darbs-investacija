<section aria-label="Welcome" class="auth-card sim-dash-welcome">
    <div class="sim-dash-list__head">
        <div>
            <h1>{{ __('Welcome :name!', ['name' => auth()->user()->name]) }}</h1>
            <p class="sim-dash-welcome__sub">{{ __("You're signed in. Your data is loaded from the database.") }}</p>
        </div>
        <button id="start-tutorial" class="btn btn-secondary" type="button">📚 {{ __('Start Tutorial') }}</button>
    </div>
</section>
