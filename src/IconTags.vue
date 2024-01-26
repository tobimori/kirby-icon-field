https://github.com/getkirby/kirby/blob/main/panel/src/components/Navigation/Tags.vue

<template>
  <k-navigate ref="navigation" :axis="layout === 'list' ? 'y' : 'x'">
    <k-draggable
      :list="tags"
      :options="dragOptions"
      :data-layout="layout"
      class="k-tags"
      @end="save"
    >
      <k-tag
        v-for="(item, itemIndex) in tags"
        :key="itemIndex"
        :disabled="disabled"
        :image="item.image"
        :removable="!disabled"
        name="tag"
        :class="$attrs.max == 1 ? 'k-tag-single' : null"
        @keypress.native.enter="edit(itemIndex, item, $event)"
        @click.native="max == 1 ? edit(itemIndex, item, $event) : $event.preventDefault()"
        @dblclick.native="edit(itemIndex, item, $event)"
        @remove="remove(itemIndex, item)"
      >
        <!-- eslint-disable-next-line vue/no-v-html -->
        <span v-if="findSvg(item.value)" class="k-tag-text-icon" v-html="findSvg(item.value)" />
        <!-- eslint-disable-next-line vue/no-v-html -->
        <span class="k-tag-text-label" v-html="item.text" />
      </k-tag>
      <template #footer>
        <!-- @slot Place stuff here in the non-draggable footer -->
        <slot />
      </template>
    </k-draggable>
  </k-navigate>
</template>

<script>
export default {
  extends: 'k-tags',
  methods: {
    findSvg(value) {
      return this.options.find((option) => option.value === value)?.svg ?? null
    },
    remove(index) {
      this.tags.splice(index, 1)
      this.input()
    }
  },
  computed: {
    isDraggable() {
      if (
        this.$attrs.max === 1 ||
        this.sort === true ||
        this.draggable === false ||
        this.tags.length === 0 ||
        this.disabled === true
      ) {
        return false
      }

      return true
    }
  }
}
</script>

<style lang="scss">
.k-icon-tag, // add icon tag class to also target if dragged object
.k-input[data-type='icon'] {
  .k-tags {
    min-width: 100%;
  }

  .k-tag {
    &.k-tag-single {
      cursor: pointer !important;
      background: transparent;
      color: var(--color-text);
      margin: calc(var(--tags-gap) * -1);
      height: calc(var(--tag-height) + var(--tags-gap) * 2);
      flex-grow: 1;
      outline: none;
      width: calc(100% + var(--tags-gap));

      svg {
        filter: brightness(0);
      }
    }

    &-text {
      display: flex;
      align-items: center;
      padding-inline-start: var(--spacing-2);

      &-icon {
        display: block;
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
  }
}
</style>
