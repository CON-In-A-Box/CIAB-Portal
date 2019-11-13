/* global alertbox */
/* exported lockedTag */

function lockedTag() {
  alertbox('This tag can only be unset by an authorized user.');
  return false;
}
