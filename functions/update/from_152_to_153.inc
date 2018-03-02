<?php

/*.
    require_module 'standard';
.*/

function _updatetables153_DBSchema()
{
    global $db;
    echo '<div id="update_DatabaseSchema" class="w3-container">';

      // Update ConComList
      $sql = "ALTER TABLE ConComList";
      $sql .= " ADD FOREIGN KEY (DepartmentID) REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE,";
      $sql .= " ADD FOREIGN KEY (PositionID) REFERENCES ConComPositions (PositionID) ON DELETE RESTRICT ON UPDATE CASCADE,";
      $sql .= " ADD FOREIGN KEY (YearID) REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated ConComList Foreign Keys</span><br>';

      // Update Departments
      $sql = "ALTER TABLE Departments";
      $sql .= " ADD FOREIGN KEY (ParentDepartmentID) REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated Division Foreign Keys</span><br>';

      // Update ElegibleVoters
      $sql = "ALTER TABLE ElegibleVoters";
      $sql .= " ADD FOREIGN KEY (YearID) REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated Division Foreign Keys</span><br>';

      // Update EMails
      $sql = "ALTER TABLE EMails";
      $sql .= " ADD FOREIGN KEY (DepartmentID) REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated EMails Foreign Keys</span><br>';

      // Update HourRedemptions
      $sql = "ALTER TABLE HourRedemptions";
      $sql .= " ADD FOREIGN KEY (PrizeID) REFERENCES VolunteerRewards (PrizeID) ON DELETE RESTRICT ON UPDATE CASCADE,";
      $sql .= " ADD FOREIGN KEY (YearID) REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated HourRedemptions Foreign Keys</span><br>';

      // Update MeetingAttendance
      $sql = "ALTER TABLE MeetingAttendance";
      $sql .= " ADD FOREIGN KEY (MeetingID) REFERENCES OfficialMeetings (MeetingID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated MeetingAttendance Foreign Keys</span><br>';

      // Update OfficialMeetings
      $sql = "ALTER TABLE OfficialMeetings";
      $sql .= " ADD FOREIGN KEY (YearID) REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated OfficialMeetings Foreign Keys</span><br>';

      // Update VolunteerHours
      $sql = "ALTER TABLE VolunteerHours";
      $sql .= " ADD FOREIGN KEY (DepartmentID) REFERENCES Departments (DepartmentID) ON DELETE RESTRICT ON UPDATE CASCADE,";
      $sql .= " ADD FOREIGN KEY (YearID) REFERENCES ConventionYear (YearID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated VolunteerHours Foreign Keys</span><br>';

      // Update VolunteerRewards
      $sql = "ALTER TABLE VolunteerRewards";
      $sql .= " ADD FOREIGN KEY (RewardGroupID) REFERENCES RewardGroup (RewardGroupID) ON DELETE RESTRICT ON UPDATE CASCADE;";
      $db->run($sql);
      echo '<span style="color:green">Updated VolunteerRewards Foreign Keys</span><br>';

    echo '</div>';
}

function from_152_to_153()
{
    _updatetables153_DBSchema();

}
