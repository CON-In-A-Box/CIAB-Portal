const isDivisionStaff = (staff) => {
  return staff.department && staff.department.parent === null;
};

const getDivisionPosition = (staff) => {
  if (staff.position === 'Head') {
    return 'Director';
  } else if (staff.position === 'Specialist') {
    return 'Support';
  }

  return '';
};

const extractStaff = (staff) => {
  return {
    id: parseInt(staff.member?.id) ?? -1,
    departmentName: staff.department?.name ?? '',
    divisionName: staff.department?.parent?.name ?? '',
    firstName: staff.member?.first_name ?? '',
    lastName: staff.member?.last_name ?? '',
    pronouns: staff.member?.pronouns ?? '',
    position: isDivisionStaff(staff) ? getDivisionPosition(staff) : staff.position,
    email: staff.member?.email ?? '',
    note: staff.note ?? ''
  }
};

const sortAlphabetical = (staff, otherStaff) => {
  if (staff.position === 'Head' && otherStaff.position !== 'Head') {
    return -1;
  } else if (staff.position === 'Director' && otherStaff.postion !== 'Director') {
    return -1;
  } else if (staff.position === 'Sub-Head' && otherStaff.position !== 'Sub-Head') {
    return 0;
  }

  return 1;
};

export const extractDepartmentStaff = (staffData) => {
  return staffData.map((item) => extractStaff(item))
    .sort(sortAlphabetical);
};
