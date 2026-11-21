https://github.com/getkirby/kirby/blob/main/panel/src/components/Forms/Input/CheckboxesInput.vue

<template>
	<fieldset
		:disabled="disabled"
		:class="['k-checkboxes-input', $attrs.class]"
		:style="$attrs.style"
	>
		<legend class="sr-only">{{ $t("options") }}</legend>

		<ul :style="{ '--columns': columns }" class="k-grid" data-variant="choices">
			<li v-for="(choice, index) in choices" :key="index">
				<k-icon-choice-input v-bind="choice" @input="input(choice.value, $event)" />
			</li>
		</ul>
	</fieldset>
</template>

<script>
export default {
	extends: "k-checkboxes-input",
	emits: ["input"],
	computed: {
		choices() {
			return this.options.map((option, index) => {
				const checked = this.selected.includes(option.value)

				return {
					autofocus: this.autofocus && index === 0,
					checked,
					disabled: this.disabled || option.disabled || (!checked && this.isFull),
					id: `${this.id}-${index}`,
					info: option.info,
					label: option.text,
					name: this.name ?? this.id,
					svg: option.svg,
					type: this.isSingle ? "radio" : "checkbox",
					value: option.value
				}
			})
		},
		isFull() {
			return !this.isSingle && this.max && this.selected.length >= this.max
		},
		isSingle() {
			return this.max === 1
		}
	},
	methods: {
		input(key, value) {
			// allow toggling for single select
			if (this.isSingle) {
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

			this.$emit("input", this.selected)
		}
	}
}
</script>
