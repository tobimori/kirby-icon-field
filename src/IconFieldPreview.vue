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
			return (this.field.options ?? []).map((option) => ({
				...option,
				text: this.$panel.html(
					`${option.svg ?? ""}<span>${this.$helper.string.escapeHTML(option.text)}</span>`
				)
			}))
		}
	}
}
</script>

<style>
.k-icon-field-preview .k-tag-text {
	display: flex;
	align-items: center;
	gap: var(--spacing-2);

	svg {
		flex-shrink: 0;
		width: 1rem;
		height: 1rem;
		filter: var(--icon-field-text-filter);
	}
}
</style>
