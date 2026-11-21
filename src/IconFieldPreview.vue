<template>
	<k-tags-field-preview
		v-bind="$attrs"
		:class="['k-icon-field-preview', $attrs.class]"
		:column="column"
		:field="{ ...field, options }"
		:value="value"
	/>
</template>

<script>
import { iconSvg } from "./svgs.js"

export default {
	inheritAttrs: false,
	props: {
		column: {
			default: () => ({}),
			type: Object
		},
		field: {
			default: () => ({}),
			type: Object
		},
		value: [Array, String]
	},
	computed: {
		options() {
			const values =
				typeof this.value === "string"
					? this.value.split(",").map((value) => value.trim())
					: (this.value ?? [])

			return (this.field.options ?? []).map((option) => {
				if (values.includes(option.value) === false) {
					return option
				}

				const svg = iconSvg(this.field, option.value) ?? ""
				const text = this.$helper.string.escapeHTML(option.text)

				return {
					...option,
					text: this.$panel.html(
						`<span class="k-icon-field-preview-icon">${svg}</span><span>${text}</span>`
					)
				}
			})
		}
	}
}
</script>

<style>
.k-icon-field-preview .k-tag-text {
	display: flex;
	align-items: center;
	gap: var(--spacing-2);

	.k-icon-field-preview-icon {
		flex-shrink: 0;
		width: 1rem;
		height: 1rem;
	}

	svg {
		display: block;
		width: 100%;
		height: 100%;
		filter: var(--icon-field-text-filter);
	}
}
</style>
