# Интерфейс назначения пользователей на e-mail рассылку
<img width="1552" alt="Снимок экрана 2022-04-18 в 13 44 49" src="https://user-images.githubusercontent.com/39495665/163797984-ef2822a5-5ca9-42cd-9a17-589ba42cceae.png">
<img width="1552" alt="Снимок экрана 2022-04-18 в 13 46 34" src="https://user-images.githubusercontent.com/39495665/163798012-d49c20db-e1db-4fa5-a2dc-95ed6186e878.png">

## Использование
1. Создаем новое уведомление через GUI
2. Назначаем на созданное уведомление пользователей
3. Подключаем класс с уведомлением require_once("Notification.php");
4. Создаем экземпляр класса $notification = new Notification();
5. Передаем id созданного уведомления $notification->setId($id);
5. Отправляем сообщение в формате HTML $notification->sendNotification($message)
