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

require_once("$CFG->libdir/formslib.php");

class notificationMailForm extends moodleform
{

    public function definition()
    {
        $mform = $this->_form;
        $mform->addElement('header', '', 'Добавление уведомления');
        $mform->addElement('text', 'name', 'Наименование уведомления');
        $mform->addElement('submit', 'button', get_string('add'));
        $mform->addElement('header', '', 'Добавление пользователей в рассылку');
        $mform->addElement('html', html_writer::table($this->_customdata->table));


    }

    function validation($data, $files)
    {
        return array();
    }
}