<section class="rounded-2xl border border-base-300 bg-base-100 p-6"
    x-data="{
        qr: '', codes: [], failed: false,
        async load(url, kind) {
            this.failed = false;
            const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin', cache: 'no-store' });
            if (!response.ok) { this.failed = true; return; }
            const data = await response.json();
            if (kind === 'qr') this.qr = data.svg; else this.codes = data;
        }
    }">
    <h2 class="text-lg font-semibold">{{ __('lazy-admin::users.two_factor') }}</h2>
    <p class="mt-2 text-sm text-base-content/60">{{ __('lazy-admin::users.two_factor_help') }}</p>
    <p class="mt-3 font-medium">{{ $user->two_factor_secret && $user->two_factor_confirmed_at ? __('lazy-admin::users.enabled') : __('lazy-admin::users.disabled') }}</p>
    @if ($errors->any())
        <x-lazy-alert error class="mt-4" :message="$errors->first()" />
    @endif
    <div class="mt-4 space-y-4">
        <x-lazy-btn ghost :href="route('password.confirm')" :label="__('lazy-admin::users.confirm_identity')" />
        @if (! $user->two_factor_secret)
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <x-lazy-btn primary type="submit" :label="__('lazy-admin::users.enable')" />
            </form>
        @else
            @if (! $user->two_factor_confirmed_at)
                <x-lazy-btn type="button" :label="__('lazy-admin::users.show_qr')" x-on:click="load(@js(route('two-factor.qr-code')), 'qr')" />
                <div x-show="qr" x-cloak class="inline-block rounded-xl bg-white p-4" x-html="qr"></div>
                <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-3">
                    @csrf
                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-medium">{{ __('lazy-admin::users.code') }}</span>
                        <x-lazy-input name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required />
                    </label>
                    <x-lazy-btn primary type="submit" :label="__('lazy-admin::users.confirm')" />
                </form>
            @else
                <x-lazy-btn type="button" :label="__('lazy-admin::users.show_codes')" x-on:click="load(@js(route('two-factor.recovery-codes')), 'codes')" />
                <div x-show="codes.length" x-cloak class="rounded-xl bg-base-200 p-4">
                    <p class="mb-3 text-sm">{{ __('lazy-admin::users.codes_help') }}</p>
                    <template x-for="code in codes" :key="code"><p class="font-mono text-sm" x-text="code"></p></template>
                    <button type="button" class="mt-3 text-sm underline" @click="codes = []">{{ __('lazy-admin::users.hide_codes') }}</button>
                </div>
                <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
                    @csrf
                    <x-lazy-btn type="submit" :label="__('lazy-admin::users.regenerate_codes')" />
                </form>
            @endif
            <form method="POST" action="{{ route('two-factor.disable') }}" onsubmit="return confirm(@js(__('lazy-admin::users.disable_confirm')))">
                @csrf
                @method('DELETE')
                <x-lazy-btn error type="submit" :label="__('lazy-admin::users.disable')" />
            </form>
        @endif
        <p x-show="failed" x-cloak class="text-sm text-error">{{ __('lazy-admin::users.confirm_first') }}</p>
    </div>
</section>
