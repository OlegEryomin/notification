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
require_once("PotencialNotificationUsersSelector.php");
require_once("SelectorNotificationUsers.php");
require_once("Notification.php");

require_login();

if (!is_siteadmin()){
    exit;
}

$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url('/blocks/bsu_other/notification_mail/users.php?id=' . $id);
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Управление рассылкой уведомлений');
$PAGE->set_heading($SITE->fullname);

$notification = new Notification();
$notification->setId($id);
$notification->getNotificationFromId();


$PAGE->navbar->add('Управление рассылкой уведомлений', new moodle_url("index.php"));
$PAGE->navbar->add($notification->getName());

$notificationuserselector = new SelectorNotificationUsers();
$notificationuserselector->setNotificationId($id);
$notificationuserselector->set_extra_fields(array('username', 'email'));


$potentialnotifuserselector = new PotencialNotificationUsersSelector();
$potentialnotifuserselector->setNotificationId($id);
$potentialnotifuserselector->set_extra_fields(array('username', 'email'));

if (optional_param('remove', false, PARAM_BOOL) and confirm_sesskey()) {
    global $DB;
    if ($userstoadd = $notificationuserselector->get_selected_users()) {
        $user = reset($userstoadd);
        $DB->delete_records('bsu_notification_mail_users', array('notification_id' => $id, 'user_id' => $user->id));
    }
} else if (optional_param('add', false, PARAM_BOOL) and confirm_sesskey()) {
    global $DB;
    if ($userstoadd = $potentialnotifuserselector->get_selected_users()) {
        $user = reset($userstoadd);
        $DB->insert_record('bsu_notification_mail_users', array('notification_id' => $id, 'user_id' => $user->id));
    }

}

echo $OUTPUT->header();
?>

    <div id="addadmisform">
        <h3 class="main"><?php echo $notification->getName(); ?></h3>

        <form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
            <div>
                <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>"/>

                <table class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
                    <tr>
                        <td id='existingcell'>
                            <p>
                                <label for="removeselect">Пользователи подключенные к рассылке</label>
                            </p>
                            <?php $notificationuserselector->display(); ?>
                        </td>
                        <td id='buttonscell'>
                            <p class="arrow_button">
                                <input name="add" id="add" type="submit"
                                       value="<?php echo $OUTPUT->larrow() . '&nbsp;' . get_string('add'); ?>"
                                       title="<?php print_string('add'); ?>"/><br/>
                                <input name="remove" id="remove" type="submit"
                                       value="<?php echo get_string('remove') . '&nbsp;' . $OUTPUT->rarrow(); ?>"
                                       title="<?php print_string('remove'); ?>"/>
                            </p>
                        </td>
                        <td id='potentialcell'>
                            <p>
                                <label for="addselect"><?php print_string('users'); ?></label>
                            </p>
                            <?php $potentialnotifuserselector->display(); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>

<?php


echo $OUTPUT->footer();