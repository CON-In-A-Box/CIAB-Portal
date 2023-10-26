/* globals describe, it, expect */
import { extractDivisionHierarchy, createDonutData } from '../../../../../modules/concom/sitesupport/division-parser';
import departmentList from './department_list.json';

describe('Staff Division Parser', () => {
  it('can parse the division hierarchy', () => {
    const result = extractDivisionHierarchy(departmentList.data);
    expect(result).toMatchSnapshot();
  });

  it('can parse donut data from the division data', () => {
    const divisions = extractDivisionHierarchy(departmentList.data);
    const result = createDonutData(divisions);

    result.forEach((item, idx) => {
      // Division at top level node data.
      expect(item.nodeData.colorIndex).toEqual(idx);
      expect(item.nodeData.dsize).toEqual(1);
      expect(item.nodeData.strokeWidth).toEqual(3);
      expect(item.nodeData).toHaveProperty('label');
      expect(item.nodeData.link.includes(item.nodeData.label.replaceAll(' ', '_'))).toEqual(true);

      const arrowNode = item.subData[0];
      // First level of "subData" always has one element. An arrow and then departments.
      expect(arrowNode.nodeData.colorIndex).toEqual(idx);
      expect(arrowNode.nodeData.dsize).toEqual(1);
      expect(arrowNode.nodeData.noRotate).toEqual(1);
      expect(arrowNode.nodeData.pieWidth).toEqual(10);
      expect(arrowNode.nodeData.strokeWidth).toEqual(2);
      expect(arrowNode.nodeData.label).toEqual('⌾');

      // Departments
      arrowNode.subData.forEach((dept) => {
        expect(dept.nodeData.colorIndex).toEqual(idx);
        expect(dept.nodeData.dsize).toBeCloseTo((1 / arrowNode.subData.length), 5);
        expect(dept.nodeData.strokeWidth).toEqual(1);
        expect(dept.nodeData).toHaveProperty('label');
        expect(dept.nodeData.link.includes(dept.nodeData.label.replaceAll(' ', '_'))).toEqual(true);
      });
    });
  });
});
