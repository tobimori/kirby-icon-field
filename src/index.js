import IconField from './IconField.vue'
import IconInput from './IconInput.vue'
import IconTags from './IconTags.vue'
import IconPicklistDropdown from './IconPicklistDropdown.vue'
import IconPicklistInput from './IconPicklistInput.vue'
import IconChoicesInput from './IconChoicesInput.vue'
import IconChoiceInput from './IconChoiceInput.vue'
import IconFieldPreview from './IconFieldPreview.vue'

panel.plugin('tobimori/icon-field', {
  fields: {
    icon: IconField
  },
  components: {
    'k-icon-field-preview': IconFieldPreview,
    'k-icon-input': IconInput,
    'k-icon-tags': IconTags,
    'k-icon-picklist-dropdown': IconPicklistDropdown,
    'k-icon-picklist-input': IconPicklistInput,
    'k-icon-choices-input': IconChoicesInput,
    'k-icon-choice-input': IconChoiceInput
  }
})
