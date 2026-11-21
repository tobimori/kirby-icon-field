<script>
export default {
	extends: "k-bubbles-field-preview",
	computed: {
		html() {
			return true
		},
		bubbles() {
			if (!this.value) {
				return []
			}

			// this seems kind of hacky,
			// but it's the easiest way to get the svg to render
			// without duplicating too much code
			return this.value.map((value) => ({
				text: `<span class="k-icon-field-bubble">
					${this.findSvg(value)}
					<span>${value}</span>
				</span>`
			}))
		}
	},
	methods: {
		findSvg(value) {
			return this.field.options.find((option) => option.value === value)?.svg ?? null
		}
	}
}
</script>

<style>
.k-icon-field-bubble {
	display: flex;
	align-items: center;
	padding-inline-end: 1rem; /* width: min-content; on .k-bubble otherwise shrinks icon */

	svg {
		flex-shrink: 0;
		width: 1rem;
		height: 1rem;
		margin-inline-end: var(--spacing-2);
		fill: revert-layer;
		filter: var(--icon-field-text-filter);
	}
}
</style>
