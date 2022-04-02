/* jshint esversion: 6 */
/* globals apiRequest */

export default {
  props: {
    debug: Boolean,
    debugElement: String,
    clickTarget: String
  },
  mounted() {
  },
  created() {
    apiRequest('GET', 'artshow/', 'max_results=all')
      .then((response) => {
        this.configuration = JSON.parse(response.responseText);
        this.configLoaded();
      });
  },
  data() {
    return {
      columns: [],
      pieces: null,
      configuration: null
    };
  },
  methods: {
    debugmsg(message) {
      if (this.debug && this.debugElement) {
        var target = document.getElementById(this.debugElement);
        if (target) {
          target.classList.add('UI-show');
          target.innerHTML = message;
        }
      }
    },
    onClick(item) {
      var fn;
      if (this.clickTarget[0] == '$') {
        this.$parent[this.clickTarget.substring(1)](item);
      } else if (this.clickTarget[0] == '#') {
        fn = eval(this.clickTarget.substring(1));
        if (typeof fn === 'function') {
          fn.apply(null, [ item ]);
        } else {
          console.log(this.clickTarget + ' Callback Not Found');
        }
      } else {
        fn = window[this.clickTarget];
        if (typeof fn === 'function') {
          fn.apply(null, [ item ]);
        } else {
          console.log(this.clickTarget + ' Callback Not Found');
        }
      }

    },
    clear() {
      this.pieces = null;
    },
    count() {
      if (this.pieces !== null) {
        return this.pieces.length;
      }
      return 0;
    },
    getPiece(index) {
      if (this.pieces !== null) {
        return this.pieces[index];
      }
      return null;
    },
    configLoaded() {}
  }
}
