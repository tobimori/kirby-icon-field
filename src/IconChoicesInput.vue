https://github.com/getkirby/kirby/blob/main/panel/src/components/Forms/Input/CheckboxesInput.vue

<template>
	<fieldset
		:disabled="disabled"
		:class="['k-checkboxes-input', $attrs.class]"
		:data-virtual="choices.length > count"
		:style="$attrs.style"
	>
		<legend class="sr-only">{{ $t("options") }}</legend>

		<ul
			ref="list"
			:style="{
				'--columns': columns,
				paddingBlock: `${start * pitch}px ${(choices.length - end) * pitch}px`
			}"
			class="k-grid"
			data-variant="choices"
		>
			<li v-for="choice in choices.slice(start, end)" :key="choice.value">
				<k-icon-choice-input v-bind="choice" @input="input(choice.value, $event)" />
			</li>
		</ul>
	</fieldset>
</template>

<script>
export default {
	extends: "k-checkboxes-input",
	emits: ["input"],
	data() {
		return {
			offset: 0,
			pitch: 0,
			scrollTop: 0,
			viewport: 0
		}
	},
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
					type: this.isSingle ? "radio" : "checkbox",
					value: option.value
				}
			})
		},
		// only the rows in and around the visible part of the dropdown are rendered
		count() {
			return this.pitch ? Math.ceil(this.viewport / this.pitch) + 20 : 50
		},
		end() {
			const first = this.pitch ? Math.floor((this.scrollTop - this.offset) / this.pitch) - 10 : 0
			return Math.min(this.choices.length, Math.max(0, first) + this.count)
		},
		start() {
			return Math.max(0, this.end - this.count)
		},
		isFull() {
			return !this.isSingle && this.max && this.selected.length >= this.max
		},
		isSingle() {
			return this.max === 1
		}
	},
	mounted() {
		this.scroller = this.$el.closest(".k-picklist-input-body")
		this.scroller.addEventListener("scroll", this.onScroll, { passive: true })
		this.observer = new ResizeObserver(this.measure)
		this.observer.observe(this.scroller)
	},
	beforeUnmount() {
		this.scroller.removeEventListener("scroll", this.onScroll)
		this.observer.disconnect()
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
		},
		measure() {
			const list = this.$refs.list
			const [first, second] = list.children
			const pitch = second ? second.offsetTop - first.offsetTop : first?.offsetHeight

			if (!pitch) {
				return
			}

			this.pitch = pitch
			this.offset =
				list.getBoundingClientRect().top -
				this.scroller.getBoundingClientRect().top +
				this.scroller.scrollTop
			this.viewport = this.scroller.clientHeight
			this.onScroll()
		},
		onScroll() {
			this.scrollTop = this.scroller.scrollTop
		}
	}
}
</script>

<style>
/* the dropdown would change its width with the rendered labels while scrolling */
.k-picklist-dropdown:has(.k-checkboxes-input[data-virtual="true"]) {
	width: 20rem;
}
</style>
