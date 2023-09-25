const extractStaff = (staff) => {
  return {
    id: parseInt(staff.member?.id) ?? -1,
    deptStaffId: parseInt(staff.id) ?? -1,
    deptId: parseInt(staff.department.id) ?? -1,
    departmentName: staff.department?.name ?? '',
    divisionName: staff.department?.parent?.name,
    firstName: staff.member?.first_name ?? '',
    lastName: staff.member?.last_name ?? '',
    pronouns: staff.member?.pronouns ?? '',
    position: staff.position,
    email: staff.member?.email ?? '',
    note: staff.note ?? ''
  }
};

export const sortStaffByPosition = (staff, otherStaff) => {
  if (staff.position === 'Head' && otherStaff.position !== 'Head') {
    return -1;
  } else if (staff.position === 'Sub-Head' && otherStaff.position !== 'Sub-Head') {
    return 0;
  }

  return 1;
};

export const extractDepartmentStaff = (staffData) => {
  return staffData.map((item) => extractStaff(item));
};
