https://github.com/getkirby/kirby/blob/main/panel/src/components/Forms/Input/ChoiceInput.vue

<template>
	<label
		:aria-disabled="disabled"
		:class="['k-choice-input', 'k-icon-choice-input', $attrs.class]"
		:style="$attrs.style"
	>
		<input
			v-bind="{
				autofocus,
				id,
				checked,
				disabled,
				name,
				required,
				type,
				value
			}"
			:class="variant === 'invisible' ? 'sr-only' : null"
			:data-variant="variant"
			@click="$emit('click', $event)"
			@input="$emit('input', $event.target.checked)"
		/>

		<span v-if="label || info" class="k-choice-input-label k-icon-choice-input-label">
			<!-- eslint-disable-next-line vue/no-v-html -->
			<span v-if="svg" class="k-choice-input-label-icon" v-html="svg" />
			<span v-safe-html="label" class="k-choice-input-label-text" />
			<span v-if="info" v-safe-html="info" class="k-choice-input-label-info" />
		</span>
	</label>
</template>

<script>
export default {
	extends: "k-choice-input",
	props: {
		svg: String
	},
	emits: ["click", "input"]
}
</script>

<style>
.k-icon-choice-input {
	flex-direction: row-reverse;
	justify-content: space-between;
}

.k-icon-choice-input-label {
	flex-direction: row;
	align-items: center;

	svg {
		height: 1.125rem;
		width: 1.125rem;
		margin-inline-end: var(--spacing-2);
		display: block;
		filter: brightness(0) invert(1);
	}
}
</style>
