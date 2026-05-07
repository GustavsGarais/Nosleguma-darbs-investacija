<section aria-label="{{ __('Welcome') }}" class="auth-card sim-dash-welcome">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="margin:0 0 8px;">{{ __('Welcome :name!', ['name' => auth()->user()->name]) }}</h1>
            <p class="sim-dash-welcome__sub">{{ __("You're signed in. Your data is loaded from the database.") }}</p>
        </div>
        <button id="start-tutorial" class="btn btn-secondary" type="button">📚 {{ __('Start Tutorial') }}</button>
    </div>
</section>
