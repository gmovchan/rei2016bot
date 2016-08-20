<?php
define('BOT_TOKEN', '248879322:AAGlm0_-jcOVLxerv6A7x8GmG42Ooul8OBE');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
function apiRequestWebhook($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  $parameters["method"] = $method;
  header("Content-Type: application/json");
  echo json_encode($parameters);
  return true;
}
function exec_curl_request($handle) {
  $response = curl_exec($handle);
  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    error_log("Curl returned error $errno: $error\n");
    curl_close($handle);
    return false;
  }
  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);
  if ($http_code >= 500) {
    // do not wat to DDOS server if something goes wrong
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Request was successfull: {$response['description']}\n");
    }
    $response = $response['result'];
  }
  return $response;
}
function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);
  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  return exec_curl_request($handle);
}
function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  $parameters["method"] = $method;
  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
  return exec_curl_request($handle);
}
function processMessage($message) {
  // process incoming message
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    // incoming text message
    $text = $message['text'];
    if (strpos($text, "/start") === 0) {
      apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Я завелась'));
    } else if (strpos($text, "/hi") === 0) {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Привет, няша'));
    } else if (strpos($text, "/meeting") === 0) {
      require_once("congregation.php");
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $congregationClock));
    } else if (strpos($text, "/relationship") === 0) {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Я ламповая няша, ищу домоседа, который любит аниме и доту. Пишите в ЛС'));
    } else if (strpos($text, "/stop") === 0) {
      // stop now
    } else if (strpos($text, "/help") === 0) {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Правила конфы, нарушение которых карается баном:<br>. Постинг детской порнографии<br>. Вайп'));
    } else {
      apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => 'Продолжай'));
    }
  } else {
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Я не понимаю что происходит'));
  }
}
//надо добавить ссылку на бебхук, хз зачем
define('WEBHOOK_URL', 'https://api.telegram.org/bot248879322:AAGlm0_-jcOVLxerv6A7x8GmG42Ooul8OBE/setWebhook?url=https://reitelegram.herokuapp.com/robot.php');
if (php_sapi_name() == 'cli') {
  // if run from console, set or delete webhook
  apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
  exit;
}
$content = file_get_contents("php://input");
$update = json_decode($content, true);
if (!$update) {
  // receive wrong update, must not happen
  exit;
}
if (isset($update["message"])) {
  processMessage($update["message"]);
}
