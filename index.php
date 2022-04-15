<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * notification_mail file description here.
 *
 * @package    notification_mail
 * @copyright  2022 Eryomin Oleg eremin_o@bsu.edu.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../config.php");
require_once("NotificationMailForm.php");
require_once("Notification.php");


global $PAGE, $SITE, $OUTPUT;

require_login();

if (!is_siteadmin()){
    exit;
}

$id      = optional_param('id', 0, PARAM_INT);
$status  = optional_param('status', null, PARAM_ACTION);

$PAGE->set_url('/blocks/bsu_other/notification_mail/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Управление рассылкой уведомлений');
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add('Управление рассылкой уведомлений');

echo $OUTPUT->header();

$notification = new Notification();
if (!empty($id)) {
    $notification->setId($id);
    $notification->setActive($status);
    $notification->updateActive();
}

$customdata->table = $notification->getTableNotifications();

$mform = new notificationMailForm(null, $customdata);

if ($mform->is_cancelled()) {

} else if ($data = $mform->get_data()) {

    $notification = new Notification();
    $notification->setName($data->name);
    $notification->addNotification();

} else {
    $mform->set_data();
    $mform->display();
}

echo  $OUTPUT->footer();



