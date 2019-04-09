<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$permNames = array(
    '0' => 'Administrator',
    '1' => 'Allow Make Admin',
    '2' => 'Edit/Delete Other Admins',
    '3' => 'Edit/Delete Users',
    '4' => 'Create Users',
    '5' => 'Edit Self',
    '6' => 'Edit Groups',
    '7' => 'Execute stop Command',
    '8' => 'Execute restart Command',
    '9' => 'Execute start Command',
    '10' => 'Execute clientkick Command',
    '11' => 'Execute status Command'
);
function hasPermission($permission, $permissionsJson) {
  if (isset($permissionsJson)) {
    $permissionsJson = json_decode($permissionsJson);
    
    if ($permissionsJson->permissions[0]->{$permission} == 1) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}
?>