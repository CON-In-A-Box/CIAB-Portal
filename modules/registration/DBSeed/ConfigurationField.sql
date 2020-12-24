INSERT INTO ConfigurationField(Field, TargetTable, Type, InitialValue, Description) VALUES
    ('RegistrationOpen', 'Registration_Configuration', 'integer', 0, 'Scheduled hour on event days that online check-in opens (24-hour clock)'),
    ('RegistrationClose', 'Registration_Configuration', 'integer', 24, 'Scheduled hour on event days that online check-in closes (24-hour clock)'),
    ('ForceOpen', 'Registration_Configuration', 'boolean', 0, 'Online Check-In Registration is open. (Only valid during event days)'),
    ('badgeNotice', 'Registration_Configuration', 'text', '', 'Notice text presented at the top of the Checkin/Badge Pickup screen.'),
    ('passInstructions', 'Registration_Configuration', 'text', 'Please use this boarding pass to pick up your badge at registration.', 'Instructions for use of the boarding pass.')
