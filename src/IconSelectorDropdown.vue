This component just replaces the default `k-selector` with `k-icon-selector` that adds our SVG icon
preview. Based on https://github.com/getkirby/kirby/blob/v4/develop/panel/src/components/Dropdowns/SelectorDropdown.vue
Let me know if there is a better solution to this!

<template>
  <k-dropdown class="k-selector-dropdown">
    <slot />
    <k-dropdown-content
      ref="dropdown"
      class="k-selector-dropdown-content"
      :align-x="align"
      :autofocus="false"
      :disabled="disabled"
      :navigate="false"
      @close="reset"
    >
      <k-icon-selector
        ref="selector"
        v-bind="$props"
        @create="create"
        @escape="$refs.dropdown.close()"
        @select="select"
      />
    </k-dropdown-content>
  </k-dropdown>
</template>

<script>
export default {
  extends: 'k-selector-dropdown'
}
</script>
