const TEMPLATE = `
<div class="UI-container UI-margin">
  <button class="CONCOM-new-division-button">
    <span>Add New Division</span> <i class="fas fa-plus-square"></i>
  </button>
</div>

<div class="UI-container UI-margin">
  <span class="CONCOM-division-span">Activities</span>
  <div class="CONCOM-division-drag-div">
    <div class="CONCOM-department">Book Swap</div>
    <div class="CONCOM-department">Connies Quantum Sandbox</div>
    <div class="CONCOM-department">Exhibits</div>
    <div class="CONCOM-department">Gaming</div>
    <div class="CONCOM-department">Invited Participants</div>
    <div class="CONCOM-department">Programming</div>
    <div class="CONCOM-department">Spoken Word</div>
    <div class="CONCOM-department">Teen Room</div>
    <div class="CONCOM-new-department-div">
      <button class="CONCOM-new-department-button"><i class="fas fa-plus-square"></i></button>
    </div>
  </div>
</div>
`;

const StaffStructureDivision = {
  template: TEMPLATE
};

export default StaffStructureDivision;
