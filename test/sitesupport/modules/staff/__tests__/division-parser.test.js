/* globals describe, it, expect */
import { extractDivisionHierarchy } from '../../../../../modules/concom/sitesupport/division-parser';
import departmentList from './department_list.json';

describe('Staff Division Parser', () => {
  it('can parse the division hierarchy', () => {
    const result = extractDivisionHierarchy(departmentList.data);
    expect(result).toMatchSnapshot();
  });
});
