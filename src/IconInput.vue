<template>
  <k-draggable
    :list="state"
    :options="dragOptions"
    :data-layout="layout"
    element="k-dropdown"
    class="k-multiselect-input"
    @click.native="$refs.dropdown.toggle"
    @end="onInput"
  >
    <k-tag
      v-for="tag in tags"
      :class="max == 1 ? 'k-tag-single' : 'k-tag-multi'"
      :ref="tag.value"
      :key="tag.value"
      :removable="max > 1"
      @click.native.stop
      @remove="remove(tag)"
      @keydown.native.left="navigate('prev')"
      @keydown.native.right="navigate('next')"
      @keydown.native.down="$refs.dropdown.open"
    >
      <!-- eslint-disable-next-line vue/no-v-html -->
      <span v-if="findSvg(tag.value)" class="k-tag-svg" v-html="findSvg(tag.value)" />
      <span v-html="tag.text" />
    </k-tag>

    <template #footer>
      <k-dropdown-content
        ref="dropdown"
        @open="onOpen"
        @close="onClose"
        @keydown.native.esc.stop="close"
      >
        <k-dropdown-item v-if="search" icon="search" class="k-multiselect-search">
          <input
            ref="search"
            :value="q"
            :placeholder="search.min ? $t('search.min', { min: search.min }) : $t('search') + ' …'"
            @input="q = $event.target.value"
            @keydown.esc.stop="onEscape"
          />
        </k-dropdown-item>

        <div class="k-multiselect-options k-icon-input-options scroll-y-auto">
          <k-dropdown-item
            v-for="option in visible"
            :key="option.value"
            :icon="isSelected(option) ? (max > 1 ? 'check' : 'circle-filled') : 'circle-outline'"
            :class="{
              'k-multiselect-option': true,
              selected: isSelected(option),
              disabled: !more && max > 1
            }"
            @click.prevent="select(option)"
            @keydown.native.enter.prevent.stop="select(option)"
            @keydown.native.space.prevent.stop="select(option)"
          >
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span class="k-button-text-icon" v-html="option.svg" />
            <span class="k-button-text-inner" v-html="option.text" />
          </k-dropdown-item>

          <k-dropdown-item
            v-if="filtered.length === 0"
            :disabled="true"
            class="k-multiselect-option"
          >
            {{ emptyLabel }}
          </k-dropdown-item>
        </div>

        <k-button
          v-if="visible.length < filtered.length"
          :text="`${$t('search.all')} (${filtered.length})`"
          class="k-multiselect-more"
          @click.stop="limit = false"
        />
      </k-dropdown-content>
    </template>
  </k-draggable>
</template>

<script>
export default {
  extends: "k-multiselect-input",
  methods: {
    findSvg(value) {
      return this.options.find((option) => option.value === value)?.svg ?? null
    },
    add(option) {
      if (this.more) {
        this.state.push(option.value)
        this.onInput()
        return
      }

      if (this.max === 1 && !this.more) {
        this.state = [option.value]
        this.onInput()
      }
    }
  }
}
</script>

<style lang="scss">
.k-icon-no-counter .k-field-counter {
  display: none !important;
}

.k-input[data-theme="field"][data-type="icon"] {
  .k-multiselect-input {
    min-height: 2.25rem;
    padding: 0.25rem 2rem 0 0.25rem;
    cursor: pointer;
  }

  .k-tag {
    height: 1.75rem;
    margin-bottom: 0.25rem;
    margin-right: 0.25rem;
    cursor: default;

    &.k-tag-single {
      background: transparent;
      color: var(--color-gray-900);
      font-size: var(--text-base);
      pointer-events: none;

      .k-tag-text {
        padding: 0 0.5rem;
      }
    }

    .k-tag-toggle {
      cursor: pointer;
    }

    .k-tag-text {
      display: flex;
      align-items: center;
    }

    .k-tag-svg {
      margin-right: 0.5rem;
      display: inline-block;
      line-height: 1;
      margin-top: 0.125rem;

      svg {
        height: 1rem;
        width: 1rem;
      }
    }
  }
}

.k-icon-input-options {
  .k-dropdown-item {
    display: flex;
    flex-direction: row-reverse;
  }

  .k-button-text {
    padding-left: 0;
    margin-inline-end: auto;
    opacity: 1;
    display: flex;

    &-inner {
      opacity: 0.75;
    }

    &-icon {
      display: block;
      margin-inline-end: 0.5rem;
      height: 1rem;
      width: 1rem;

      svg {
        height: 1rem;
        width: 1rem;
        filter: brightness(0) invert(1);
      }
    }
  }

  .k-button:hover .k-button-text-inner {
    opacity: 1;
  }
}
</style>
