https://github.com/getkirby/kirby/blob/main/panel/src/components/Forms/Field/MultiselectField.vue

<template>
	<k-field
		v-bind="$props"
		:class="['k-multiselect-field', $attrs.class]"
		:counter="null"
		:input="id"
		:style="$attrs.style"
	>
		<k-empty v-if="hasNoOptions" :icon="icon" :text="$t('options.none')" />
		<k-input
			v-else
			v-bind="$props"
			ref="input"
			:options="choices"
			type="icon"
			@input="$emit('input', $event)"
		/>
	</k-field>
</template>

<script>
export default {
	extends: "k-multiselect-field",
	provide() {
		return {
			iconField: this
		}
	},
	props: {
		revision: String
	},
	emits: ["input"]
}
</script>
