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
        specialDivision: isSpecialDivision(item.name),
        departments: []
      }
    })
    .reduce((prev, current) => {
      prev[current.id] = current;

      return prev;
    }, {});
}

const remapItem = (item, idx, total) => {
  return {
    label: item.name,
    colorIndex: idx,
    link: `index.php?Function=concom#table_header_${item.name.replaceAll(' ', '_')}`,
    dsize: (1 / total)
  }
};

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

  const sortedDivisions = Object.keys(divisionHierarchy).map((item) => {
    return divisionHierarchy[item]
  }).sort((a, b) => {
    if (a.specialDivision && !b.specialDivision) {
      return 1;
    } else if (!a.specialDivision && b.specialDivision) {
      return -1;
    }

    return a.name.localeCompare(b.name);
  });

  sortedDivisions.forEach((division) => {
    division.departments.sort((a, b) => a.name.localeCompare(b.name));
  });

  return sortedDivisions;
}

/**
 * Takes the sorted division data and creates the necessary structure to render our organizational hierarchy donut.
 *
 * Data is returned in the following format:
 *
 * {
 *   "nodeData": {
 *     "label": "Activities",
 *     "colorIndex": 0,
 *     "link": "index.php?function=concom#Activities",
 *     "dsize": 1,
 *     "strokeWidth": 3
 *   },
 *   "subData": [
 *     {
 *       "nodeData": {
 *         "label": "arrow":,
 *         "colorIndex": 0,
 *         "strokeWidth": 2,
 *         "dsize": 1,
 *         "noRotate": 1,
 *         "pieWidth": 10
 *       },
 *       "subData": [
 *          {
 *            "nodeData": {
 *              "label": "Book Swap",
 *              "colorIndex": 0,
 *              "link": "index.php?function=concom#Book_Swap",
 *              "dsize": 0.125,
 *              "strokeWidth": 1
 *            }
 *          }
 *       ]
 *     }
 *   ]
 * }
 *
 * "label" is the name of the division or department, except for the arrow
 * "colorIndex" is the color index to use when rendering, departments should match with their division
 * "link" is the link on the ConCom List page to navigate to a section
 * "dsize" is the area allocated to a rendered division or department. If there are 8 departments,
 *   then each department should have a value equal to 1/8
 * "strokeWidth" is the line width on the rendered section area
 * @param {*} divisions
 * @returns
 */
export const createDonutData = (divisions) => {
  return divisions.map((division, idx) => {
    const mappedDivision = {
      // Special Case for Divisions: dsize always 1
      ...remapItem(division, idx, 1),
      strokeWidth: 3
    };

    // If for some reason we have additional staff in the "department" that is really the division, we don't want to double up.
    const filteredDivisionDepts = division.departments.filter((department) => department.id !== division.id);
    const mappedDepartments = filteredDivisionDepts.map((department) => {
      return {
        nodeData: {
          ...remapItem(department, idx, filteredDivisionDepts.length),
          strokeWidth: 1
        }
      }
    });

    return {
      nodeData: mappedDivision,
      subData: [
        {
          nodeData: {
            label: '\u{233E}',
            colorIndex: idx,
            strokeWidth: 2,
            dsize: 1,
            noRotate: 1,
            pieWidth: 10
          },
          subData: mappedDepartments
        }
      ]
    }
  });
}
