https://github.com/getkirby/kirby/blob/main/panel/src/components/Forms/Input/MultiselectInput.vue

<template>
	<div
		:class="['k-multiselect-input', $attrs.class]"
		:data-disabled="disabled"
		:style="$attrs.style"
	>
		<k-input-validator
			v-bind="{ min, max, required }"
			:value="JSON.stringify(value)"
			anchor=".k-multiselect-input-toggle"
		>
			<k-icon-tags
				ref="tags"
				v-bind="$props"
				:removable="true"
				@input="$emit('input', $event)"
				@click.stop="open"
			>
				<k-button
					v-if="!max || value.length < max"
					:id="id"
					ref="toggle"
					:autofocus="autofocus"
					:disabled="disabled"
					class="k-multiselect-input-toggle k-tags-navigatable"
					size="xs"
					icon="angle-down"
					@keydown.delete="$refs.tags.focus('prev')"
					@focus="open"
				/>
			</k-icon-tags>
		</k-input-validator>

		<k-icon-picklist-dropdown
			ref="dropdown"
			v-bind="$props"
			:options="options"
			@input="$emit('input', $event)"
		/>
	</div>
</template>

<script>
export default {
	extends: "k-multiselect-input",
	emits: ["input"]
}
</script>
