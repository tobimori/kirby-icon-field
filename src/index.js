import IconField from "./IconField.vue"
import IconInput from "./IconInput.vue"
import IconTags from './IconTags.vue'
import IconSelector from './IconSelector.vue'
import IconSelectorDropdown from './IconSelectorDropdown.vue'

window.panel.plugin("tobimori/icon-field", {
  fields: {
    icon: IconField
  },
  components: {
		"k-icon-input": IconInput,
    'k-icon-tags': IconTags,
    'k-icon-selector-dropdown': IconSelectorDropdown,
		'k-icon-selector': IconSelector
  }
})
