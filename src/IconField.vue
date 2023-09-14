https://github.com/getkirby/kirby/blob/v4/develop/panel/src/components/Forms/Field/MultiselectField.vue

<template>
  <k-field v-bind="$props" :input="_uid" :counter="null" class="k-multiselect-field">
    <k-empty v-if="hasNoOptions" :icon="icon" :text="$t('options.none')" />
    <k-input
      v-else
      v-bind="$props"
      :id="_uid"
      ref="input"
      theme="field"
      type="icon"
      v-on="$listeners"
    />
  </k-field>
</template>

<script>
export default {
  extends: 'k-multiselect-field'
}
</script>
