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

class PotencialNotificationUsersSelector extends user_selector_base {

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
        $admins = explode(',', $CFG->siteadmins);
        parent::__construct('addselect', array('multiselect'=>false, 'exclude'=>$admins));
    }

    public function find_users($search) {
        global $CFG, $DB;

        list($wherecondition, $params) = $this->search_sql($search, '');

        $notifusers = $DB->get_fieldset_select('bsu_notification_mail_users', 'user_id', "notification_id = $this->notificationId" );
        $notifusers = implode(',',$notifusers);
        if (empty($notifusers)) {
            $notifusers = '';
        } else {
            $notifusers = "AND id NOT IN($notifusers)";
        }

        $fields      = 'SELECT ' . $this->required_fields_sql('');
        $countfields = 'SELECT COUNT(1)';

        if (empty($search)) {
             $wherecondition ='';
        } else {
             $wherecondition = "AND CONCAT (lastname, ' ', firstname) LIKE '$search%'";
        }
        $sql = " FROM {user}
                WHERE     
                username NOT REGEXP '^[0-9]+$'
                $wherecondition
                $notifusers
                AND deleted = 0
                AND confirmed = 1
              ";


        $order = ' ORDER BY lastname ASC, firstname ASC';
        $params['localmnet'] = $CFG->mnet_localhost_id;


        if (!$this->is_validating()) {
            $potentialcount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialcount > 100) {
                return $this->too_many_results($search, $potentialcount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('potusersmatching', 'role', $search);
        } else {
            $groupname = get_string('potusers', 'role');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        return $options;
    }
}
