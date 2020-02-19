<?php
    use GuzzleHttp\Client;

    require __DIR__.'/vendor/autoload.php';
    require __DIR__.'/telegramBot.php';
    require __DIR__.'/Db.php';
    require __DIR__.'/User.php';

    $config = require __DIR__.'/config.php';



    use Telegram\Bot\Api;


    $telegramUpdate = new TelegramBot($config['BOT_TOKEN']);
    $telegram = new Api($config['BOT_TOKEN']);



    while (true) {

      $result = $telegramUpdate->getUpdates();
      foreach ($result as $update){

        if (isset($update->message->text)) {

          $text = $update->message->text; // Переменная с текстом сообщения
          $chat_id = $update->message->chat->id; // Чат ID пользователя
          $first_name = $update->message->chat->first_name; //Имя пользователя
          $user_id = $update->message->from->id; //Имя пользователя
          $username = $update->message->chat->username; //Юзернейм пользовате

          $db = new Db($config['db']);
          $User = new User($user_id, $db);


          if (!$User->isSet()){
            if (!$User->set()){
              $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Не удалось зарегистрировать вас в нашей базе! Попробуйте снова или обратитесь к администратору"]);
              continue;
            }
          }


          $Menu = new Menues($User);
          $req = $Menu->action(mb_strtolower($text));

          if (isset($req['keyboard']))
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $req['keyboard'], 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
          else
            $reply_markup = "";

          $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $req['reply'], 'reply_markup' => $reply_markup, 'parse_mode' => "HTML"]);


        }

      }

      sleep(0.5);
    }
