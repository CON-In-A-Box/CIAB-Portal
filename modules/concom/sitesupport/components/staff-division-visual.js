/* globals Vue, drawDonutChart */
import { createDonutData } from '../division-parser.js';
const PROPS = {
  divisions: Array
}

const TEMPLATE = `
  <div id="donut"></div>
`;

const staffDivisionVisualComponent = {
  props: PROPS,
  template: TEMPLATE,
  setup(props) {
    Vue.watchEffect(() => {
      if (props.divisions != null && Array.isArray(props.divisions)) {
        const donutData = createDonutData(props.divisions);
        drawDonutChart(window.innerWidth - 50, window.innerHeight - 50, donutData, '#donut');
      }
    });

    return {}
  }
};

export default staffDivisionVisualComponent;
