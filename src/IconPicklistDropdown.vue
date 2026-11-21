https://github.com/getkirby/kirby/blob/main/panel/src/components/Dropdowns/PicklistDropdown.vue

<template>
	<k-dropdown
		ref="dropdown"
		align-x="start"
		:disabled="disabled"
		:navigate="false"
		class="k-picklist-dropdown"
		@click.stop
	>
		<k-icon-picklist-input
			v-bind="$props"
			@click.stop
			@create="add"
			@input="input"
			@escape="$refs.dropdown.close()"
		/>
	</k-dropdown>
</template>

<script>
export default {
	extends: "k-picklist-dropdown"
}
</script>
