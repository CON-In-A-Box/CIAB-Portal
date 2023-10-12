/* globals describe, it, expect */
import { extractDepartmentStaff } from '../../../../../modules/concom/sitesupport/department-staff-parser';
import divisionStaff from './division_staff.json';
import departmentStaff from './department_staff.json';

describe('Department Staff Parser', () => {
  it.each([
    {
      input: divisionStaff,
      name: 'Division Staff'
    },
    {
      input: departmentStaff,
      name: 'Department Staff'
    }
  ])('parses $name', ({ input }) => {
    const result = extractDepartmentStaff(input.data);
    expect(result).toMatchSnapshot();
  });
});
