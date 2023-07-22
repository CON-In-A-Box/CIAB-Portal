/* jshint esversion: 6 */
/* globals apiRequest */

export default {
  props: {
    filter: {
      type: Function,
      default: null
    },
    nameAsValue: {
      type: Boolean,
      default: false
    },
    modelValue: String,
  },
  emits: [
    'update:modelValue',
    'change'
  ],
  mounted() {
  },
  created() {
    apiRequest('GET', '/department', 'max_results=all')
      .then((response) => {
        const result = JSON.parse(response.responseText);
        if (this.filter) {
          result.data.forEach(function(dept) {
            if (this.filter(dept)) {
              this.departments.push(dept);
            }
          });
        } else {
          this.departments = result.data;
        }
      })
      .catch(function(response) {
        throw (response.responseText);
      });
  },
  data() {
    return {
      departments: [],
    }
  },
  methods: {
    changed: function(evt) {
      this.$emit('update:modelValue', evt.target.value);
      this.$emit('change');
    }
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
    }
  },
  template: `
   <select class="UI-select" style="width:auto" @change="changed" :value="modelValue">
     <option v-if="nameAsValue" v-for="d in departments" :value="d.name">{{d.name}}</option>
     <option v-else v-for="d in departments" :value="d.id">{{d.name}}</option>
   </select>
  `
}
