<template>
  <k-field
    v-bind="$props"
    :input="_uid"
    :counter="counterOptions"
    class="k-multiselect-field"
    :class="max == 1 && 'k-icon-no-counter'"
    @blur="blur"
    @keydown.native.enter.prevent="focus"
  >
    <k-input v-bind="$props" :id="_uid" ref="input" theme="field" type="icon" v-on="$listeners" />
  </k-field>
</template>

<script>
export default {
  extends: "k-multiselect-field"
}
</script>
