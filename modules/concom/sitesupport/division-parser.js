const extractDepartment = (item) => {
  const hasParent = item.parent !== null;

  return {
    id: item.id,
    name: item.name,
    email: item.email,
    ...hasParent && {
      parent_name: item.parent.name
    }
  }
}

const extractDivisions = (departmentData) => {
  return departmentData.filter((item) => item.parent === null)
    .map((item) => {
      const mappedDivision = extractDepartment(item);
      return {
        ...mappedDivision,
        departments: [ mappedDivision ]
      }
    })
    .reduce((prev, current) => {
      prev[current.name] = current;

      return prev;
    }, {});
}

/**
 *
 * Data comes back in format as an array:
 *
 * {
 *   "id": "123",
 *   "parent": null OR {
 *     "id": "456",
 *     "parent": null OR nested department
 *     "name": "Parent Department",
 *     "fallback": null OR,
 *     "child_count": "2",
 *     "email": [
 *       "parent-dept@test-con.org"
 *     ],
 *     "type": "department"
 *   },
 *   "name": "Department A",
 *   "fallback": null OR,
 *   "child_count": "9",
 *   "email": [
 *     "department-a@test-con.org"
 *   ]
 * }
 *
 *
 * @param {*} departmentData
 * @returns array of divisions
 */
export const extractDivisionHierarchy = (departmentData) => {
  const divisionHierarchy = extractDivisions(departmentData);

  // Extract department data and apply to appropriate division based on parent name.
  departmentData.filter((item) => item.parent !== null)
    .map((item) => {
      return extractDepartment(item);
    })
    .forEach((item) => {
      const parentDivision = divisionHierarchy[item.parent_name];
      if (parentDivision !== undefined) {
        parentDivision.departments.push(item);
      }
    });

  return Object.keys(divisionHierarchy).map((item) => {
    return divisionHierarchy[item]
  });
}
