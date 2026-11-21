https://github.com/getkirby/kirby/blob/main/panel/src/components/Forms/Input/PicklistInput.vue

<template>
	<k-navigate
		:class="['k-picklist-input', $attrs.class]"
		:style="$attrs.style"
		element="nav"
		axis="y"
		select="input[type=search], label, .k-picklist-input-body button"
		@prev="$emit('escape')"
	>
		<header v-if="search" class="k-picklist-input-header">
			<div class="k-picklist-input-search">
				<k-search-input
					ref="search"
					:autofocus="autofocus"
					:disabled="disabled"
					:placeholder="placeholder"
					:value="query"
					@input="query = $event"
					@keydown.escape.prevent="escape"
					@keydown.enter.prevent="add"
				/>
			</div>
		</header>

		<template v-if="filteredOptions.length">
			<div class="k-picklist-input-body">
				<k-input-validator v-bind="{ min, max, required }" :value="JSON.stringify(value)">
					<k-icon-choices-input
						ref="options"
						:disabled="disabled"
						:max="max"
						:options="choices"
						:value="value"
						class="k-picklist-input-options"
						@input="input"
						@keydown.enter.prevent="enter"
					/>
				</k-input-validator>
				<k-button
					v-if="display !== true && filteredOptions.length > display"
					class="k-picklist-input-more"
					icon="angle-down"
					@click="display = true"
				>
					{{ $t("options.all", { count: filteredOptions.length }) }}
				</k-button>
			</div>
		</template>

		<template v-else-if="showEmpty">
			<div class="k-picklist-input-body">
				<p class="k-picklist-input-empty">
					{{ $t("options.none") }}
				</p>
			</div>
		</template>
	</k-navigate>
</template>

<script>
export default {
	extends: "k-picklist-input",
	emits: ["create", "escape", "input"],
	computed: {
		isFull() {
			return this.max > 1 && this.value.length >= this.max
		}
	}
}
</script>
