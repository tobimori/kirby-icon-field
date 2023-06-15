import IconField from "./IconField.vue"
import IconInput from "./IconInput.vue"

window.panel.plugin("tobimori/icon-field", {
  fields: {
    icon: IconField
  },
  components: {
    "k-icon-input": IconInput
  }
})
