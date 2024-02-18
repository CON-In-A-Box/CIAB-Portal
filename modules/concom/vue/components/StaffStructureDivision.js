const PROPS = {
  divisions: Array
}

const TEMPLATE = `
<div class="UI-container UI-margin">
  <button class="CONCOM-new-division-button" @click="$emit('addDivision')">
    <span>Add New Division</span> <i class="fas fa-plus-square"></i>
  </button>
</div>

<div class="UI-container UI-margin" v-for="division in divisions" :key="division.name">
  <span class="CONCOM-division-span" @dblclick="$emit('editDepartment', division)">{{ division.name }}</span>
  <div class="CONCOM-division-drag-div">
    <div class="CONCOM-department" @dblclick="$emit('editDepartment', department)" v-for="department in division.departments" :key="department.name">
      {{ department.name }}
    </div>
    <div class="CONCOM-new-department-div">
      <button class="CONCOM-new-department-button" @click="$emit('addDepartment', division)"><i class="fas fa-plus-square"></i></button>
    </div>
  </div>
</div>
`;

const StaffStructureDivision = {
  props: PROPS,
  emits: ['addDivision', 'addDepartment', 'editDivision', 'editDepartment'],
  template: TEMPLATE
};

export default StaffStructureDivision;
