<?php
define('BOT_TOKEN', '248879322:AAGlm0_-jcOVLxerv6A7x8GmG42Ooul8OBE');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('HOSTING_URL', 'https://reitelegram.herokuapp.com/robot.php');
define('WEBHOOK_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/setWebhook?url='.HOSTING_URL);
?>
