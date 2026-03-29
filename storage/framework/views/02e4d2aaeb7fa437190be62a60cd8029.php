

<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('dashboard_content'); ?>
<section class="auth-card" aria-label="Settings overview" style="padding:32px; display:flex; flex-direction:column; gap:24px;">
    <header style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
        <div>
            <h1 style="margin:0;"><?php echo e(__('Account')); ?></h1>
            <p style="margin:6px 0 0; color:var(--c-on-surface-2);"><?php echo e(__('Manage profile info, security, notifications, and currency in one place.')); ?></p>
        </div>
        <a class="btn btn-outline" href="<?php echo e(route('simulations.index')); ?>">← <?php echo e(__('Back')); ?></a>
    </header>

    <?php if(session('status') === 'profile-updated'): ?>
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            <strong><?php echo e(__('Saved.')); ?></strong> <?php echo e(__('Your account details are up to date.')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('status') === 'currency-updated'): ?>
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            <strong><?php echo e(__('Currency saved.')); ?></strong> <?php echo e(__('Your display currency is synced to your account.')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('status') === 'password-updated'): ?>
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            <strong><?php echo e(__('Password updated.')); ?></strong> <?php echo e(__('Your password has been changed successfully.')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('status') === 'two-factor-enabled'): ?>
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            <strong><?php echo e(__('Two-factor authentication enabled.')); ?></strong> <?php echo e(__('Your account is now more secure.')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('status') === 'two-factor-disabled'): ?>
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            <strong><?php echo e(__('Two-factor authentication disabled.')); ?></strong>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid #e53935; border-radius:10px; background: color-mix(in srgb, #e53935 10%, var(--c-surface));">
            <ul style="margin:0; padding-left:18px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li style="color: var(--c-on-surface);"><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div style="display:flex; flex-direction:column; gap:24px;">
        <!-- Profile -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <h2 style="margin:0;"><?php echo e(__('Profile')); ?></h2>
                <div class="info-bubble" data-tooltip="<?php echo e(__('Your name is synced across reports, invites, and alerts. Email cannot be changed.')); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('settings.profile')); ?>" style="display:flex; flex-direction:column; gap:16px;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('patch'); ?>

                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;"><?php echo e(__('Full name')); ?></span>
                    <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" class="footer-email-input" required />
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span style="color:#e53935; font-size:12px;"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <div style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;"><?php echo e(__('Email address')); ?></span>
                    <div style="position:relative; display:flex; align-items:center;">
                        <input
                            type="email"
                            value="<?php echo e($user->email); ?>"
                            class="footer-email-input"
                            readonly
                            style="cursor:not-allowed; opacity:0.65; padding-right:40px;"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; right:12px; color:var(--c-on-surface-2); pointer-events:none;">
                            <rect width="11" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <span style="font-size:12px; color:var(--c-on-surface-2);"><?php echo e(__('Email address cannot be changed.')); ?></span>
                </div>

                <button type="submit" class="btn btn-primary" style="align-self:flex-start;"><?php echo e(__('Save profile')); ?></button>
            </form>
        </article>

        <!-- Security -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <h2 style="margin:0;"><?php echo e(__('Security')); ?></h2>
                <div class="info-bubble" data-tooltip="<?php echo e(__('Manage your password and two-factor authentication to keep your account secure.')); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>
            </div>
            <div style="display:grid; gap:16px;">
                <!-- Change Password -->
                <div style="border-radius:12px; padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-secondary) 5%); display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                        <div>
                            <h4 style="margin:0 0 4px;"><?php echo e(__('Password')); ?></h4>
                            <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Minimum 12 characters with uppercase, lowercase, and numbers or symbols.')); ?></p>
                        </div>
                        <button
                            type="button"
                            id="toggle-password-form"
                            class="btn btn-outline"
                            onclick="
                                const form = document.getElementById('change-password-form');
                                const open = form.style.display === 'flex';
                                form.style.display = open ? 'none' : 'flex';
                                this.textContent = open ? '<?php echo e(__('Change password')); ?>' : '<?php echo e(__('Cancel')); ?>';
                            "
                        ><?php echo e(__('Change password')); ?></button>
                    </div>

                    <form
                        id="change-password-form"
                        method="POST"
                        action="<?php echo e(route('settings.password')); ?>"
                        style="display:none; flex-direction:column; gap:12px; padding-top:12px; border-top:1px solid var(--c-border);"
                    >
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('patch'); ?>

                        <label style="display:flex; flex-direction:column; gap:6px;">
                            <span style="font-weight:600; font-size:14px;"><?php echo e(__('Current password')); ?></span>
                            <input type="password" name="current_password" class="footer-email-input" required autocomplete="current-password" />
                            <?php $__errorArgs = ['current_password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span style="color:#e53935; font-size:12px;"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>

                        <label style="display:flex; flex-direction:column; gap:6px;">
                            <span style="font-weight:600; font-size:14px;"><?php echo e(__('New password')); ?></span>
                            <input type="password" name="password" class="footer-email-input" required autocomplete="new-password" />
                            <?php $__errorArgs = ['password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span style="color:#e53935; font-size:12px;"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>

                        <label style="display:flex; flex-direction:column; gap:6px;">
                            <span style="font-weight:600; font-size:14px;"><?php echo e(__('Confirm new password')); ?></span>
                            <input type="password" name="password_confirmation" class="footer-email-input" required autocomplete="new-password" />
                        </label>

                        <button type="submit" class="btn btn-primary" style="align-self:flex-start;"><?php echo e(__('Update password')); ?></button>
                    </form>
                </div>

                <!-- 2FA -->
                <div style="border-radius:12px; padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-secondary) 5%); display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div>
                        <h4 style="margin:0 0 4px;"><?php echo e(__('Two-Factor Authentication (2FA)')); ?></h4>
                        <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">
                            <?php if($user->hasTwoFactorEnabled()): ?>
                                <?php echo e(__('Two-factor authentication is enabled.')); ?>

                            <?php else: ?>
                                <?php echo e(__('Add an extra layer of security to your account.')); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="<?php echo e(route('settings.two-factor')); ?>" class="btn btn-outline">
                        <?php if($user->hasTwoFactorEnabled()): ?>
                            <?php echo e(__('Manage 2FA')); ?>

                        <?php else: ?>
                            <?php echo e(__('Enable 2FA')); ?>

                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </article>

        <!-- Notifications -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <h2 style="margin:0;"><?php echo e(__('Notifications')); ?></h2>
                <div class="info-bubble" data-tooltip="<?php echo e(__('These toggles only affect this browser for now and are stored locally.')); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>
            </div>
            <div style="display:grid; gap:12px;">
                <?php $__currentLoopData = [
                    ['label' => __('Monthly performance insights'), 'key' => 'notify-performance'],
                    ['label' => __('Security alerts & logins'), 'key' => 'notify-security'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $toggle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label style="display:flex; justify-content:space-between; gap:12px; align-items:center; border:1px solid var(--c-border); border-radius:10px; padding:12px 16px;">
                        <span><?php echo e($toggle['label']); ?></span>
                        <input type="checkbox" data-setting-key="<?php echo e($toggle['key']); ?>" />
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </article>

        <!-- Currency -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <h2 style="margin:0;"><?php echo e(__('Currency & Region')); ?></h2>
                <div class="info-bubble" data-tooltip="<?php echo e(__('Your choice is saved on your account (not just this browser). Rates load from a public exchange API.')); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>
            </div>
            <div style="display:grid; gap:12px;">
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;"><?php echo e(__('Amount in euros')); ?></span>
                    <input type="number" id="currency-amount" class="footer-email-input" min="0" step="100" value="1000" />
                </label>
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;"><?php echo e(__('Target currency')); ?></span>
                    <select id="currency-select" class="footer-email-input">
                        <option value="USD"><?php echo e(__('US Dollar (USD)')); ?></option>
                        <option value="GBP"><?php echo e(__('British Pound (GBP)')); ?></option>
                        <option value="JPY"><?php echo e(__('Japanese Yen (JPY)')); ?></option>
                        <option value="EUR"><?php echo e(__('Euro (EUR)')); ?></option>
                    </select>
                </label>
                <div style="border:1px solid var(--c-border); border-radius:10px; padding:16px;">
                    <p style="margin:0; color:var(--c-on-surface-2); font-size:14px;"><?php echo e(__('Converted amount')); ?></p>
                    <p id="converted-amount" style="margin:4px 0 0; font-size:24px; font-weight:700;">—</p>
                </div>
                <div style="border:1px dashed var(--c-border); border-radius:10px; padding:16px;">
                    <p style="margin:0 0 6px; font-weight:600;"><?php echo e(__('Quick previews')); ?></p>
                    <ul style="margin:0; padding-left:18px; display:grid; gap:4px; font-size:14px;">
                        <li data-preview-base="5000"><?php echo e(__('Emergency fund')); ?>: <span class="currency-preview">—</span></li>
                        <li data-preview-base="25000"><?php echo e(__('Long-term goal')); ?>: <span class="currency-preview">—</span></li>
                        <li data-preview-base="100000"><?php echo e(__('Retirement fund')); ?>: <span class="currency-preview">—</span></li>
                    </ul>
                </div>
            </div>
        </article>

        <!-- Danger zone -->
        <article style="border:1px solid #e53935; border-radius:16px; padding:24px; background:color-mix(in srgb, #e53935 10%, var(--c-surface)); display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <h2 style="margin:0; color:#e53935;"><?php echo e(__('Danger zone')); ?></h2>
                <div class="info-bubble" data-tooltip="<?php echo e(__('Deleting your account clears every simulation and cannot be undone.')); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#e53935; cursor:help;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('settings.destroy')); ?>" style="display:flex; flex-direction:column; gap:16px;" onsubmit="return confirm('<?php echo e(__('Delete your account and all simulations? This cannot be undone.')); ?>');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('delete'); ?>
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;"><?php echo e(__('Confirm password')); ?></span>
                    <input type="password" name="password" class="footer-email-input" required />
                    <?php $__errorArgs = ['password', 'userDeletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span style="color:#e53935; font-size:12px;"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>
                <button type="submit" class="btn btn-outline" style="color:#e53935; border-color:#e53935;"><?php echo e(__('Delete account')); ?></button>
            </form>
        </article>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-open password form if there were validation errors in that bag
    <?php if($errors->hasBag('updatePassword') && $errors->getBag('updatePassword')->any()): ?>
        const pwForm = document.getElementById('change-password-form');
        const pwToggle = document.getElementById('toggle-password-form');
        if (pwForm) pwForm.style.display = 'flex';
        if (pwToggle) pwToggle.textContent = '<?php echo e(__('Cancel')); ?>';
    <?php endif; ?>

    const toggles = document.querySelectorAll('[data-setting-key]');
    const localKey = 'nosleguma-settings';
    const saved = JSON.parse(localStorage.getItem(localKey) || '{}');

    toggles.forEach(toggle => {
        const key = toggle.dataset.settingKey;
        toggle.checked = Boolean(saved[key]);
        toggle.addEventListener('change', () => {
            saved[key] = toggle.checked;
            localStorage.setItem(localKey, JSON.stringify(saved));
        });
    });

    const amountInput = document.getElementById('currency-amount');
    const select = document.getElementById('currency-select');
    const resultEl = document.getElementById('converted-amount');
    const previewEls = document.querySelectorAll('[data-preview-base]');
    const settingsCurrencyUrl = <?php echo json_encode(route('settings.currency'), 15, 512) ?>;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const accountCurrency = <?php echo json_encode($user->currency_preference ?? 'EUR', 15, 512) ?>;

    const defaultRates = { EUR: 1, USD: 1.08, GBP: 0.86, JPY: 162.5 };
    let rates = { ...defaultRates };
    const currencySymbols = { EUR: '€', USD: '$', GBP: '£', JPY: '¥' };

    async function fetchExchangeRates() {
        try {
            const response = await fetch('https://api.frankfurter.app/latest?from=EUR');
            if (!response.ok) throw new Error('API request failed');
            const data = await response.json();
            if (data && data.rates) {
                rates = { EUR: 1, USD: data.rates.USD || defaultRates.USD, GBP: data.rates.GBP || defaultRates.GBP, JPY: data.rates.JPY || defaultRates.JPY };
                try { localStorage.setItem('nosleguma-currency-rates', JSON.stringify({ rates, timestamp: Date.now() })); } catch (e) {}
                renderCurrency();
                return true;
            }
        } catch (error) {
            try {
                const cached = localStorage.getItem('nosleguma-currency-rates');
                if (cached) {
                    const parsed = JSON.parse(cached);
                    if (parsed.timestamp && (Date.now() - parsed.timestamp) < 86400000) {
                        rates = parsed.rates || defaultRates;
                        renderCurrency();
                        return true;
                    }
                }
            } catch (e) {}
            rates = { ...defaultRates };
            renderCurrency();
        }
        return false;
    }

    function format(amount, currency) {
        return `${currencySymbols[currency]}${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function renderCurrency() {
        const amount = parseFloat(amountInput.value) || 0;
        const currency = select.value;
        resultEl.textContent = format(amount * (rates[currency] ?? 1), currency);
        previewEls.forEach(el => {
            const base = parseFloat(el.dataset.previewBase) || 0;
            const label = el.querySelector('.currency-preview');
            if (label) label.textContent = format(base * (rates[currency] ?? 1), currency);
        });
        try { localStorage.setItem('nosleguma-currency-preference', currency); } catch (e) {}
    }

    async function persistCurrencyPreference(currency) {
        if (!settingsCurrencyUrl || !csrf) return;
        const res = await fetch(settingsCurrencyUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ currency_preference: currency }),
        });
        if (!res.ok) throw new Error('currency save failed');
        const data = await res.json().catch(() => ({}));
        if (data.currency_preference) {
            window.__NOS_SERVER_CURRENCY__ = data.currency_preference;
        }
        if (window.NosCurrencyFormatter) {
            await window.NosCurrencyFormatter.render();
        }
    }

    fetchExchangeRates();

    if (accountCurrency && rates[accountCurrency]) {
        select.value = accountCurrency;
    }

    amountInput?.addEventListener('input', renderCurrency);
    select?.addEventListener('change', async () => {
        await fetchExchangeRates();
        try {
            await persistCurrencyPreference(select.value);
        } catch (err) {
            console.warn(err);
        }
        renderCurrency();
    });

    renderCurrency();
});
</script>
<style>
.info-bubble { position:relative; display:inline-flex; align-items:center; transition:opacity 0.2s; }
.info-bubble:hover { opacity:0.8; }
.info-bubble:hover::after {
    content: attr(data-tooltip);
    position: absolute; bottom: calc(100% + 12px); left: 50%; transform: translateX(-50%);
    padding: 14px 18px; background: var(--c-surface) !important; border: 2px solid var(--c-border);
    border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.4); width: max-content;
    max-width: 300px; min-width: 220px; font-size: 14px; line-height: 1.7;
    color: var(--c-on-surface) !important; font-weight: 500; z-index: 10000;
    pointer-events: none; white-space: normal; text-align: left; opacity: 1 !important;
}
.info-bubble:hover::before {
    content: ''; position: absolute; bottom: calc(100% + 4px); left: 50%; transform: translateX(-50%);
    border: 7px solid transparent; border-top-color: var(--c-border); z-index: 10001; pointer-events: none;
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/settings/index.blade.php ENDPATH**/ ?>