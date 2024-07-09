/* jshint esversion: 6 */
/* globals   */

export default {
  props: {
    modelValue: [String, Number]
  },
  emits: [
    'update:modelValue',
    'change'
  ],
  methods: {
    changed: function(evt) {
      evt.target.value = parseInt(this.hours) + parseInt(this.mins) / 60.0;
      this.$emit('update:modelValue', evt.target.value);
      this.$emit('change');
    },
  },
  computed: {
    value: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
        this.$emit('change');
      }
    },
    hours: {
      get() {
        return Math.floor(this.modelValue);
      },
      set(value) {
        const enterHours = Math.floor(value);
        value = enterHours + (this.modelValue % 1)
        this.$emit('update:modelValue', value);
      }
    },
    mins: {
      get() {
        return Math.floor((this.modelValue - Math.floor(this.modelValue)) * 60);
      },
      set(value) {
        const enterMin = Math.floor(value);
        value = Math.floor(this.modelValue) + (enterMin / 60);
        this.$emit('update:modelValue', value);
      }
    }
  },
  template: `
  <div class="UI-container">
    <input class="UI-input VOL-hour-component" type="number" min=0 v-model.number="hours" @change="changed">
    <span class="VOL-hour-component-label">hour<span v-if="hours!=1">s</span></span>
    <input class="UI-input VOL-hour-component" type="number" min=0 max=59 step=5 v-model.number="mins" @change="changed">
    <span class="VOL-hour-component-label">minute<span v-if="mins!=1">s</span></span>
  </div>
  `
}
