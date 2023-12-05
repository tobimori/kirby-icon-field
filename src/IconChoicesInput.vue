<template>
  <ul :style="{ '--columns': columns }" class="k-checkboxes-input k-grid" data-variant="choices">
    <li v-for="(choice, index) in choices" :key="index">
      <k-icon-choice-input v-bind="choice" @input="input(choice.value, $event)" />
    </li>
  </ul>
</template>

<script>
export default {
  extends: 'k-checkboxes-input',
  computed: {
    choices() {
      return this.options.map((option, index) => ({
        ...option,
        autofocus: this.autofocus && index === 0,
        checked: this.selected.includes(option.value),
        disabled: this.disabled || option.disabled,
        name: this.name ?? this.id,
        type: this.max === 1 ? 'radio' : 'checkbox',
        label: option.text
      }))
    }
  },
  methods: {
    input(key, value) {
      // allow toggling for single select
      if (this.max === 1) {
        this.selected = []
      }

      if (value === true) {
        this.selected.push(key)
      } else {
        const index = this.selected.indexOf(key)
        if (index !== -1) {
          this.selected.splice(index, 1)
        }
      }

      this.$emit('input', this.selected)
    }
  }
}
</script>
