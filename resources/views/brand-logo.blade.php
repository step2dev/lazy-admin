@if($logoUrl)
    <img
        src="{{ $logoUrl }}"
        @if($darkLogoUrl && $darkLogoUrl !== $logoUrl)
            x-data="{
                observer: null,
                syncLogo() {
                    this.$el.src = document.documentElement.dataset.theme === 'dark'
                        ? @js($darkLogoUrl) : @js($logoUrl);
                },
                init() {
                    this.syncLogo();
                    this.observer = new MutationObserver(() => this.syncLogo());
                    this.observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
                },
                destroy() { this.observer?.disconnect(); }
            }"
        @endif
        alt="{{ $logoAlt }}"
        class="{{ $logoClass }}"
    />
@endif
