<?php
// Подключаем файлы PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['status' => 'error', 'message' => 'Неверный метод запроса.']);
  exit;
}

// Получаем данные
$name = $_POST['name'] ?? '';
$tel = $_POST['tel'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

if (empty($tel) || empty($email)) {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните телефон и email.']);
  exit;
}

$mail = new PHPMailer(true);

try {
  // --- НАСТРОЙКИ ДЛЯ TIMEWEB (БЕЗ АВТОРИЗАЦИИ) ---
  // Мы НЕ вызываем $mail->isSMTP(). Это заставит PHPMailer
  // использовать внутреннюю программу sendmail, как рекомендует Timeweb.

  // --- НАСТРОЙКИ ПИСЬМА ---
  $mail->CharSet = 'UTF-8';

  // ВАЖНО: Timeweb требует, чтобы адрес отправителя был
  // ящиком, созданным на вашем аккаунте.
  // Укажите здесь ящик, который вы создадите в панели Timeweb.
  // Например, info@nice-dev.ru.
  $mail->setFrom('info@nice-dev.ru', 'Новая заявка с сайта!');

  // Кому письмо (ваша основная почта)
  $mail->addAddress('nice-dev@list.ru');

  // --- КОНТЕНТ ПИСЬМА ---
  $mail->isHTML(true);
  $mail->Subject = 'Заявка с сайта!';
  $mail->Body    = " <div style='border: 1px solid #ccc; border-radius: 10px; padding: 10px; margin-inline: auto; max-width: 600px;'>
                        <h2 style='text-align: center;' >Заявка от " . htmlspecialchars($email) . "</h2>
                        <p><b>Имя:</b> " . htmlspecialchars($name) . "</p>
                        <p><b>Телефон:</b> " . htmlspecialchars($tel) . "</p>
                        <p><b>E-mail:</b> " . htmlspecialchars($email) . "</p>
                        <p><b>Сообщение:</b><br>" . nl2br(htmlspecialchars($message)) . "</p>
                      </div> ";

  $mail->send();

  http_response_code(200);
  echo json_encode(['status' => 'success', 'message' => 'Спасибо! Ваша заявка отправлена.']);
} catch (Exception $e) {
  http_response_code(500);
  // Если что-то пойдет не так, мы увидим ошибку
  echo json_encode(['status' => 'error', 'message' => "Ошибка при отправке: {$mail->ErrorInfo}"]);
}
