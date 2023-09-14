https://github.com/getkirby/kirby/blob/v4/develop/panel/src/components/Forms/Selector.vue

<template>
  <nav class="k-selector" role="search" :data-has-current="filtered?.includes(selected)">
    <header class="k-selector-header">
      <h2 v-if="label" class="k-selector-label">
        {{ label }}
      </h2>
      <div v-if="showSearch" class="k-selector-search">
        <input
          ref="input"
          :value="query"
          :placeholder="inputPlaceholder + ' …'"
          class="k-selector-input"
          type="search"
          @click="pick(-1)"
          @input="filter($event.target.value)"
          @keydown.down.prevent="down"
          @keydown.escape.prevent="escape()"
          @keydown.enter.prevent="select(selected)"
          @keydown.tab="tab"
          @keydown.up.prevent="up"
        />
      </div>
    </header>

    <div v-if="filtered.length || options.length" class="k-selector-body">
      <template v-if="filtered.length">
        <k-navigate ref="results" axis="y" class="k-selector-results">
          <k-button
            v-for="(option, key) in filtered"
            :key="key"
            :current="selected === key"
            :disabled="option.disabled"
            :icon="option.icon ?? icon"
            class="k-selector-button"
            @click="select(key)"
            @focus.native="pick(key)"
          >
            <!-- eslint-disable-next-line vue/no-v-html -->
            <span class="k-button-text-icon" v-html="option.svg" />
            <span v-html="highlight(option.text)" />
          </k-button>
        </k-navigate>
      </template>
      <template v-else-if="options.length">
        <p class="k-selector-empty">{{ empty }}</p>
      </template>
    </div>
  </nav>
</template>

<script>
export default {
  extends: 'k-selector'
}
</script>

<style lang="scss">
.k-input[data-type='icon'] {
  .k-selector-button {
    padding-inline-start: var(--spacing-1);
  }

  .k-button-text {
    display: flex;
    align-items: center;

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
</style>
