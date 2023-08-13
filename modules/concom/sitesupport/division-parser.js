const isSpecialDivision = (name) => {
  return name === 'Committees' || name === 'Corporate Staff' || name === 'Board Departments';
}

const extractDepartment = (item) => {
  const hasParent = item.parent !== null;

  return {
    id: parseInt(item.id),
    name: item.name,
    email: item.email,
    ...hasParent && {
      parentId: parseInt(item.parent.id)
    }
  }
}

const extractDivisions = (departmentData) => {
  return departmentData.filter((item) => item.parent === null)
    .map((item) => {
      const mappedDivision = extractDepartment(item);
      return {
        ...mappedDivision,
        name: isSpecialDivision(item.name) ? mappedDivision.name : `${mappedDivision.name} Division`,
        departments: [ mappedDivision ]
      }
    })
    .reduce((prev, current) => {
      prev[current.id] = current;

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
      const parentDivision = divisionHierarchy[item.parentId];
      if (parentDivision !== undefined) {
        parentDivision.departments = [
          ...parentDivision.departments,
          item
        ];
      }
    });

  // Sort Divisions and Departments alphabetically
  const sortedDivisions = Object.keys(divisionHierarchy).map((item) => {
    return divisionHierarchy[item]
  }).sort((a, b) => a.name.localeCompare(b.name));

  sortedDivisions.forEach((division) => {
    division.departments.sort((a, b) => a.name.localeCompare(b.name));
  });

  return sortedDivisions;
}
