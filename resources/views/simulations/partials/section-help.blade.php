<span class="sim-help-wrap" onmousedown="event.preventDefault(); event.stopPropagation();">
	<span
		class="info-bubble sim-run-section-help"
		tabindex="0"
		role="button"
		aria-label="{{ $label ?? __('Explain this section') }}"
		data-tooltip="{{ $tooltip }}"
	>
		<span class="sim-run-section-help__glyph" aria-hidden="true">?</span>
	</span>
</span>
