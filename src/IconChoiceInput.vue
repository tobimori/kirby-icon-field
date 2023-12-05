<template>
  <label class="k-choice-input k-icon-choice-input" :aria-disabled="disabled">
    <input
      v-bind="{
        autofocus,
        id,
        checked,
        disabled,
        name,
        required,
        type,
        value
      }"
      :data-variant="variant"
      :class="{ 'sr-only': variant === 'invisible' }"
      @input="$emit('input', $event.target.checked)"
    />
    <span v-if="label || info" class="k-choice-input-label k-icon-choice-input-label">
      <!-- eslint-disable-next-line vue/no-v-html -->
      <span v-if="$attrs.svg" class="k-choice-input-label-icon" v-html="$attrs.svg" />
      <!-- eslint-disable-next-line vue/no-v-html -->
      <span class="k-choice-input-label-text" v-html="label" />
      <!-- eslint-disable-next-line vue/no-v-html -->
      <span v-if="info" class="k-choice-input-label-info" v-html="info" />
    </span>
  </label>
</template>

<script>
export default {
  extends: 'k-choice-input'
}
</script>

<style lang="scss">
.k-icon-choice-input {
  flex-direction: row-reverse;
  justify-content: space-between;

  &-label {
    flex-direction: row;
    align-items: center;

    svg {
      height: 1.125rem;
      width: 1.125rem;
      margin-inline-end: var(--spacing-2);
      display: block;

      filter: brightness(0) invert(1);
    }
  }
}
</style>
