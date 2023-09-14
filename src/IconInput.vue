https://github.com/getkirby/kirby/blob/v4/develop/panel/src/components/Forms/Input/MultiselectInput.vue

<template>
  <div class="k-multiselect-input">
    <k-icon-tags ref="tags" v-bind="$props" @input="$emit('input', $event)" @click.native.stop />
  </div>
</template>

<script>
export default {
  extends: 'k-multiselect-input'
}
</script>
