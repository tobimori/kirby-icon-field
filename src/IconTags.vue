https://github.com/getkirby/kirby/blob/main/panel/src/components/Navigation/Tags.vue

<template>
	<k-navigate
		ref="navigate"
		:axis="layout === 'list' ? 'y' : 'x'"
		select=":where(.k-tag, .k-tags-navigatable):not(:disabled)"
	>
		<k-draggable
			:class="['k-tags', $attrs.class]"
			:data-layout="layout"
			:element="element"
			:list="tags"
			:options="dragOptions"
			:style="$attrs.style"
			@end="input"
		>
			<k-tag
				v-for="(item, itemIndex) in tags"
				:key="item.id ?? item.value ?? item.text"
				:class="{ 'k-tag-single': isSingle }"
				:disabled="disabled"
				:element="element"
				:link="item.link"
				:removable="removable && !disabled"
				:theme="theme"
				name="tag"
				@click="isSingle || $event.stopPropagation()"
				@keypress.enter="edit(itemIndex, item, $event)"
				@dblclick="edit(itemIndex, item, $event)"
				@remove="remove(itemIndex, item)"
			>
				<!-- eslint-disable-next-line vue/no-v-html -->
				<span v-if="option(item)" class="k-tag-text-icon" v-html="svg(item.value)" />
				<span v-safe-html="item.text" />
			</k-tag>
			<template #footer>
				<!-- @slot Place stuff here in the non-draggable footer -->
				<slot />
			</template>
		</k-draggable>
	</k-navigate>
</template>

<script>
import { iconSvg } from "./svgs.js"

export default {
	extends: "k-tags",
	inject: {
		iconField: { default: null }
	},
	computed: {
		isDraggable() {
			if (
				this.isSingle ||
				this.sort === true ||
				this.draggable === false ||
				this.tags.length === 0 ||
				this.disabled === true
			) {
				return false
			}

			return true
		},
		isSingle() {
			return this.$attrs.max === 1
		}
	},
	methods: {
		remove(index) {
			this.tags.splice(index, 1)
			this.input()
		},
		svg(value) {
			return iconSvg(this.iconField, value)
		}
	}
}
</script>

<style>
.k-input[data-type="icon"] {
	.k-tags {
		min-width: 100%;
	}

	.k-tag.k-tag-single {
		cursor: pointer !important;
		background: transparent;
		color: var(--color-text);
		margin: calc(var(--tags-gap) * -1);
		height: calc(var(--tag-height) + var(--tags-gap) * 2);
		flex-grow: 1;
		outline: none;
		width: calc(100% + var(--tags-gap));

		svg {
			filter: var(--icon-field-text-filter);
		}
	}

	.k-tag-text {
		display: flex;
		align-items: center;
		padding-inline-start: var(--spacing-2);
	}

	.k-tag-text-icon {
		display: block;
		flex-shrink: 0;
		margin-inline-end: var(--spacing-2);
		height: 1rem;
		width: 1rem;

		svg {
			height: 1rem;
			width: 1rem;
			filter: brightness(0) invert(1);
		}
	}
}
</style>
