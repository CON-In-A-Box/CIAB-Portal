/* exported PERMISSIONS */

var PERMISSIONS = [
  {
    permission:'admin.sudo',
    description:'Allowed to change login id to any other users login.'
  },
  {
    permission:'api.get.deadline.all',
    description:'Able to get deadlines from all departments.'
  },
  {
    permission:'api.delete.deadline.all',
    description:'Able to delete deadlines from all departments.'
  },
  {
    permission:'api.post.deadline.all',
    description:'Able to modify deadlines from all departments.'
  },
  {
    permission:'api.put.deadline.all',
    description:'Able to add deadlines to all departments.'
  },
  {
    permission:'asset.admin',
    description:'Able to upload and change site graphical assets'
  },
  {
    permission:'concom.reports',
    description:'Generate a CSV report of the ConCom Membership'
  },
  {
    permission:'concom.view',
    description:'View the ConCom list'
  },
  {
    permission:'concom.modify.all',
    description:'Remove/modify the concom membership in any department.'
  },
  {
    permission:'concom.add.all',
    description:'Add member to the concom of any department.'
  },
  {
    permission:'registration.reports',
    description:'Allowed generate reports from convention registration data.'
  },
  {
    permission:'site.admin',
    description:'Access to the main site administrator page(Superuser)'
  },
  {
    permission:'site.concom.permissions',
    description:'Allowed to change role permissions'
  },
  {
    permission:'site.concom.structure',
    description:'Allowed to change concom structure'
  },
  {
    permission:'site.email_lists',
    description:'Allowed to change all email lists on system'
  },
  {
    permission:'volunteers.admin',
    description:'Allowed administrate volunteer data.'
  },
  {
    permission:'volunteers.reports',
    description:'Allowed generate reports from convention volunteer data.'
  }
];
