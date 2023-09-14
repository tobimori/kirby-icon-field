This component extends the default `k-tags` to include our SVG icon preview. Based on
https://github.com/getkirby/kirby/blob/v4/develop/panel/src/components/Navigation/Tags.vue

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
        :removable="!disabled"
        name="tag"
        class="k-icon-tag"
        :class="max == 1 ? 'k-tag-single' : null"
        @keypress.native.enter="edit(itemIndex, item, $event)"
        @click.native="max == 1 ? edit(itemIndex, item, $event) : $event.preventDefault()"
        @dblclick.native="edit(itemIndex, item, $event)"
        @remove="remove(itemIndex, item)"
      >
        <!-- eslint-disable-next-line vue/no-v-html -->
        <span v-if="findSvg(item.value)" class="k-tag-text-icon" v-html="findSvg(item.value)" />
        <span class="k-tag-text-label" v-html="item.text" />
      </k-tag>
      <template #footer>
        <!-- add selector -->
        <k-icon-selector-dropdown
          v-if="showSelector"
          ref="selector"
          v-bind="selectorOptions"
          :label="$t('add')"
          @create="add($event)"
          @select="add($event)"
        >
          <k-button
            :id="id"
            ref="toggle"
            :autofocus="autofocus"
            :disabled="disabled"
            icon="add"
            class="k-tags-toggle"
            size="xs"
            @click.native="$refs.selector.open()"
            @keydown.native="toggle"
            @keydown.native.delete="navigate(tags.length - 1)"
          />
        </k-icon-selector-dropdown>

        <!-- replace selector -->
        <k-icon-selector-dropdown
          ref="editor"
          v-bind="selectorOptions"
          :label="$t('replace.with')"
          :value="editing?.tag.text"
          @create="replace($event)"
          @select="replace($event)"
        />
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
    }
  },
  computed: {
    isDraggable() {
      if (
        this.max === 1 ||
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
      background: transparent;
      color: var(--color-text);
      margin: calc(var(--tags-gap) * -1);
      height: calc(var(--tag-height) + var(--tags-gap) * 2);
      flex-grow: 1;

      svg {
        filter: brightness(0);
      }
    }
    &-text {
      display: flex;
      align-items: center;
      padding-inline-start: var(--spacing-1);

      &-icon {
        display: block;
        margin-inline-end: var(--spacing-1);
        height: 1.125rem;
        width: 1.125rem;

        svg {
          height: 1.125rem;
          width: 1.125rem;
          filter: brightness(0) invert(1);
        }
      }
    }
  }
}
</style>
