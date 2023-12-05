https://github.com/getkirby/kirby/blob/main/panel/src/components/Forms/Input/MultiselectInput.vue

<template>
  <div class="k-multiselect-input">
    <k-icon-tags
      ref="tags"
      v-bind="$props"
      @input="$emit('input', $event)"
      @click.native.stop="open"
    >
      <k-button
        v-if="!max || value.length < max"
        :id="id"
        ref="toggle"
        :autofocus="autofocus"
        :disabled="disabled"
        class="k-multiselect-input-toggle k-tags-navigatable"
        size="xs"
        icon="triangle-down"
        @keydown.native.delete="$refs.tags.focus('prev')"
        @focus.native="open"
      />
    </k-icon-tags>
    <k-icon-picklist-dropdown
      ref="dropdown"
      v-bind="$props"
      :options="options"
      @input="$emit('input', $event)"
    />
  </div>
</template>

<script>
export default {
  extends: 'k-multiselect-input'
}
</script>
