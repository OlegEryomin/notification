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
class Notification {

    private $id;
    private $name;
    private $active;

    /**
     * @param $name
     * @param $active
     */
    public function __construct() {

    }


    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /** Добавить уведомление в БД
     *  @throws mixed ошибка записи в БД
     */
    public function addNotification() {
        global $DB, $OUTPUT;

        try {
            $DB->insert_record('bsu_notification_mail', array('name' => $this->name));
            redirect('index.php', $OUTPUT->notification('Уведомление добавлено', 'notifysuccess'), 1);
        } catch (Exception $e) {
            redirect('index.php', $OUTPUT->notification($e->getMessage()), 5);
        }

    }

    /** Выбор всех записей из таблицы с уведомлениями
     *  @throws mixed ошибка чтения из БД
     *  @return object список записей в виде массива объектов
     */
    public function getNotifications() {
        global $DB, $OUTPUT;

        try {
           return $DB->get_records('bsu_notification_mail');
        } catch (Exception $e) {
            echo $OUTPUT->notification($e->getMessage());
        }
    }

    /** Формируем таблицу для отрисовки на страницу
     *  @return object объект типа html таблицы
     */
    public function getTableNotifications() {
        global $OUTPUT;

        $table = new html_table();
        $table->align = array('left', 'left', 'left');
        $table->size = array('10%', '40%', '5%');
        $table->attributes['style'] = 'width:70%';
        $table->attributes['align'] = 'center';
        $table->head = array("ID:","Наименование:", "Статус:");
        foreach($this->getNotifications() as $res) {
            $tabledata = array();
            $tabledata[] = $res->id;
            $tabledata[] = "<a href='users.php?id=$res->id'>$res->name</a>";

            if ($res->activ == true) {
                $tabledata[] = html_writer::link(new moodle_url('index.php', array('id' => $res->id, 'status' => 0)), html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/hide'), 'alt' => 'Отключить', 'class' => 'iconsmall')), array('title' => 'Отключить'));
            } else {
                $tabledata[] = html_writer::link(new moodle_url('index.php', array('id' => $res->id, 'status' => 1)), html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/show'), 'alt' => 'Включить', 'class' => 'iconsmall')), array('title' => 'Включить'));
            }


            $table->data[] = $tabledata;
        }
        return $table;
    }

    /** По id обновляем статус работы уведомления
     *  @throws mixed ошибка записи в БД
     */
    public function updateActive() {
        global $OUTPUT, $DB;

        try {
            $DB->update_record('bsu_notification_mail', array('id' => $this->id, 'activ' => $this->active));
            redirect('index.php', $OUTPUT->notification('Статус уведомления изменен', 'notifysuccess'), 1);
        } catch (Exception $e) {
            redirect('index.php', $OUTPUT->notification($e->getMessage()), 5);
        }
    }

    /** Получить уведомление по id
     *  @throws mixed ошибка чтения из БД
     */
    public function getNotificationFromId() {
        global $DB, $OUTPUT;

        try {
            $notification = $DB->get_record('bsu_notification_mail', array('id' => $this->id));
            $this->name = $notification->name;
            $this->active = $notification->activ;
        } catch (Exception $e) {
            redirect('index.php', $OUTPUT->notification($e->getMessage()), 5);
        }
    }

    /** Отправка сообщений
     * @param $message string тело сообщения
     * @param $message_html string тело сообщения в формате HTML
     */
    public function sendNotification($message) {
        global $DB;

        $this->getNotificationFromId($this->id);

        if ($this->active == true) {
            $touser = new stdClass();
            $touser->id = 0;
            $touser->mailformat = 1;

            $fromuser = "ИнфоБелГУ: Учебный процесс - рассылка уведомлений";
            $subject = $this->name;


            $notifusers = $DB->get_fieldset_select('bsu_notification_mail_users', 'user_id', "notification_id = $this->id");
            $notifusers = implode(',',$notifusers);
            $emails = $DB->get_fieldset_select('user', 'email', "id IN($notifusers)");

            foreach ($emails as $em){
                $touser->email = strtolower(trim($em));
                email_to_user($touser, $fromuser, $subject, '', $message);
            }
        }

    }

}
