<script>
export default {
  extends: 'k-bubbles-field-preview',
  methods: {
    findSvg(value) {
      return this.field.options.find((option) => option.value === value)?.svg ?? null
    }
  },
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
  }
}
</script>

<style lang="scss">
.k-icon-field-bubble {
  display: flex;
  align-items: center;
  padding-right: 1rem; // width: min-content; on .k-bubble otherwise shrinks icon

  svg {
    flex-shrink: 0;
    width: 1rem;
    height: 1rem;
    margin-right: var(--spacing-2);
    filter: brightness(0);
  }
}
</style>
