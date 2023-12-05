<template>
  <k-dropdown-content
    ref="dropdown"
    align-x="start"
    :disabled="disabled"
    :navigate="false"
    class="k-picklist-dropdown"
    @click.native.stop
  >
    <k-icon-picklist-input
      v-bind="$props"
      @click.native.stop
      @create="add"
      @input="input"
      @escape="$refs.dropdown.close()"
    />
  </k-dropdown-content>
</template>

<script>
export default {
  extends: 'k-picklist-dropdown'
}
</script>
