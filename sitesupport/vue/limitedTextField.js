/*
 * Vue component for limited text fields
 */
/* jshint esversion: 6 */
/* globals */

export default {
  // normal Vue component options here
  props: {
    charLimit: {
      type: String,
      default: '255'
    },
    modelValue: String,
    classObject: String,
    placeholder: {
      type: String,
      default: '<none>'
    },
    counterSize: {
      type: Number,
      default: 10
    }
  },
  emits: ['update:modelValue', 'change'],
  data() {
    return {
      counterLength: 0,
      calc: '',
      counterMetric: '',
    }
  },
  created() {
    var width = ((Math.log(parseInt(this.charLimit)) * Math.LOG10E + 1 | 0) * 2 + 1);
    var length = width * this.counterSize;
    this.counterLength = length.toString() + 'px';
    this.calc = 'calc(100% - ' + this.counterLength + ')';
    this.counterMetric = this.counterSize + 'px';
  },
  methods:{
    inputChanged: function(evt) {
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
<div :class="classObject">
  <input class="UI-input"
    :style="{'float': 'left', 'max-width': calc}"
    :maxlength=charLimit
    :value="modelValue"
    :placeholder="placeholder"
    @input="inputChanged"
    @change="$emit('change')"
  >
    <svg v-if="modelValue" :width=counterLength height="40px" style="{'float': 'left'}">
      <text x="0" y="25" style="fill:lightgray;" :font-size=counterMetric>
        {{ modelValue.length }}/{{ charLimit }}
      </text>
    </svg>
</div>
`
}

