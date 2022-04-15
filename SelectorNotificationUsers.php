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

require_once($CFG->dirroot.'/user/selector/lib.php');

class SelectorNotificationUsers extends user_selector_base {

    private $notificationId;

    /**
     * @return mixed
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @param mixed $notificationId
     */
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * @param string $name control name
     * @param array $options should have two elements with keys groupid and courseid.
     */
    public function __construct() {
        global $CFG, $USER;
        parent::__construct('removeselect', array('multiselect'=>false));
    }

    public function find_users($search) {
        global $DB;

        list($wherecondition, $params) = $this->search_sql($search, '');

        $fields      = 'SELECT ' . $this->required_fields_sql('');
        $countfields = 'SELECT COUNT(1)';


        $notifusers = $DB->get_fieldset_select('bsu_notification_mail_users', 'user_id', "notification_id = $this->notificationId" );
        $notifusers = implode(',',$notifusers);
        if (empty($notifusers)){
            return null;
        }
        if ($wherecondition) {
            $wherecondition = "$wherecondition AND id IN ($notifusers)";
        } else {
            $wherecondition = "id IN ($notifusers)";
        }

        $sql = " FROM {user}
                WHERE $wherecondition";
        $order = ' ORDER BY lastname ASC, firstname ASC';

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        $result = array();


        if ($availableusers) {
            if ($search) {
                $groupname = get_string('extusersmatching', 'role', $search);
            } else {
                $groupname = get_string('extusers', 'role');
            }
            $result[$groupname] = $availableusers;
        }
        return $result;
    }
    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        return $options;
    }
}

