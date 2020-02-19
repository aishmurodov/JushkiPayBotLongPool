<?php


class User{

  public $db;
  protected $user_id;

  public function __construct($user_id, $db) {
    $this->user_id = $user_id;
    $this->db = $db;
  }

  public function isSet() {
    return $this->db->column("SELECT id FROM users WHERE tg_id = :id", ["id" => $this->user_id]);
  }

  public function set($root = 0, $on_menu = "main", $balance = "0") {
    $params = [
      "id" => NULL,
      "tg_id" => $this->user_id,
      "root" => $root,
      "on_menu" => $on_menu,
      "balance" => $balance,
    ];

    $this->db->query("INSERT INTO users (id, tg_id, root, on_menu, balance, created_at) VALUES(:id, :tg_id, :root, :on_menu, :balance, NOW())", $params);

    return $this->isSet();
  }

  public function get($col = "*") {
    return $this->db->row("SELECT $col FROM users WHERE tg_id = :id", ["id" => $this->user_id])[0];
  }

  public function update($string, $par) {
    $par['id'] = $this->user_id;
    $this->db->query("UPDATE users SET $string WHERE tg_id = :id", $par);
  }

  public function setMenu($to) {
    $this->update("on_menu = :to", ["to" => $to]);
  }
}

class Menues {

  protected $user;

  public function __construct($user) {
    $this->user = $user;
  }

  public function action($command) {

    if ($command == "/r" || $command == "отмена")
      return $this->mainAction($command);

    $on = $this->user->get("on_menu")['on_menu'];
    if (!method_exists($this, $on . "Action")){
      return ["reply" => "Ошибка, я не могу понять, где вы находитесь сейчас.\r\nНапишите /r, чтобы вернуться на главный экран"];
    }
    $action = $on."Action";

    return $this->$action($command);
  }

  public function mainAction($command = "", $rep = "Меню") {

    $keyboard = [["Купить Юань", "Резерв/Курсы"], ["Оператор", "Партнёрка"]];

    if ($command == "/r" || $command == "отмена"){
      $this->user->setMenu("main");
      $reply = $rep;
    }else if ($command == "купить юань") {

      $this->user->setMenu("buy_uan_1"); // Переход к другому меню
      $reply = "<b>Выберите платёжную систему</b>";
      $keyboard = [["КУПИТЬ ЗА ГРН ВИЗА/МК", "Купить за КЕШ"]];

    }else if ($command == "резерв/курсы") {

      // Получения информации о курсе и о резерве из базы данных из таблицы system
      $kurs_rezerv = $this->user->db->row("SELECT * FROM system");
      $reply = "<b>Курс юаня к гривне:</b> 1 юань = " . $kurs_rezerv[0]['val'] . ' грн' . PHP_EOL; // $kurs_rezerv[0]['val'] - курс юаня к гривне
      $reply.= "<b>Курс доллара к юаню:</b> 1 доллар = " . $kurs_rezerv[2]['val'] . ' юаней' . PHP_EOL; // $kurs_rezerv[2]['val'] - курс доллара к юаню
      $reply.= "<b>Резерв:</b> " . $kurs_rezerv[1]['val'] . ' юаней'; // $kurs_rezerv[1]['val'] - резерв юаней

    }else if ($command == "оператор") {
      $reply = "Присоединяется оператор";
    }else if ($command == "партнёрка") {

      $unique_id = $this->user->get("id")['id']; // Получение уникального ID пользователя
      $reply = "<b>Ваш партнерский баланс:</b> 0 грн." . PHP_EOL;
      $reply.= "<b>Сумма партнерских операций:</b> 0 шт." . PHP_EOL;
      $reply.= "<b>Количество ваших рефералов:</b> 0 человек. " . PHP_EOL;
      $reply.= "<b>Ваша скидка:</b> 0%" . PHP_EOL;
      $reply.= "<b>Ваш партнерский%:</b> 15%" . PHP_EOL;
      $reply.= "" . PHP_EOL;
      $reply.= "Для получения скидки на обменные операции а так же для получения повышенного партнерского % привлекайте рефералов с помощью Вашей уникальной партнерской ссылки:  <b>http://ssilka.ru/$unique_id</b>. " . PHP_EOL;
      $reply.= "" . PHP_EOL;
      $reply.= "<b>Уровни лояльности/партнерства</b>" . PHP_EOL;
      $reply.= "" . PHP_EOL;
      $reply.= "<b>0 рефереалов:</b>  0,15% партнерские; 0% скидка на обмен" . PHP_EOL;
      $reply.= "<b>10 рефереалов:</b> 0,25% партнерские; 0,15% скидка на обмен" . PHP_EOL;
      $reply.= "<b>20 рефералов:</b>  0,35% партнерские; 0,30% скидка на обмен" . PHP_EOL;
      $reply.= "<b>30 рефералов:</b>  0,50% партнерские; 0,50% скидка на обмен" . PHP_EOL;

    }else{
      $reply = "Не понимаю";
    }

    return ["reply" => $reply, "keyboard" => $keyboard];
  }

  public function buy_uan_1Action($command = "") {

    $keyboard = [["Назад", "Отмена"]];

    if ($command == "купить за грн виза/мк") {
      $reply = "<b>Введите сумму в юанях которую хотите получить</b>";
      $this->user->setMenu("buy_uan_2"); // Переход к другому меню
    }else if ($command == "купить за кеш") {
      // // // // // // // // // // // // // // // // /
      /////////////////////////////////////////////////
      $reply = "Инструкция обюмена за настоящие денги";
      return $this->mainAction('/r', $reply); // Возврат на начальное меню
      /////////////////////////////////////////////////
      // // // // // // // // // // // // // // // // /
    }else {
      $reply = "Не понимаю";
    }

    return ["reply" => $reply, "keyboard" => $keyboard];
  }

  public function buy_uan_2Action($command = "") {
    $keyboard = [["Назад", "Отмена"]];
    if ($command == 'назад')
      return $this->mainAction('купить юань');


    $payment = $command + 0;

    if (!$payment) {
      $reply = "<b>Напишите сумму корректно</b>";
    }else {
      $this->user->setMenu("buy_uan_3"); // Переход к другому меню
      $this->user->update("payment = :payment", ["payment" => $payment]);
      $kurs = $this->user->db->column("SELECT val FROM system WHERE name = 'cny_to_uah'"); // Получение курса юаня к гривне
      $reply = "Для получения <b>$payment</b> юаней вам необходимо внести <b>" . ($payment * $kurs ) . "</b> грн!\r\n";
      $reply.= "Вы полнить Платеж Да/Нет?";
      $keyboard = [["Да", "Нет"], ["Назад"]];
    }

    return ["reply" => $reply, "keyboard" => $keyboard];
  }

  public function buy_uan_3Action($command = "") {
    $keyboard = [["Назад", "Отмена"]];
    if ($command == "да") {
      $this->user->setMenu("buy_uan_4");
      $reply = "Введите реквезиты получетяля.  Имя получателя, название банка получателя и Номер карты получателя";
    }else if ($command == "нет") {
      $this->user->update("payment = :payment, requisites = :requisites", ["payment" => '', "requisites" => '']);
      return $this->mainAction('/r', 'Отмена операции пополенения');
    }else if ($command == "назад") {
      return $this->buy_uan_1Action('купить за грн виза/мк');
    }else {
      $reply = "Не понимаю";
    }

    return ["reply" => $reply, "keyboard" => $keyboard];
  }


  public function buy_uan_4Action($command = "") {
    if ($command == "назад") {
      return $this->buy_uan_2Action($this->user->get("payment")['payment']);
    }
    $this->user->update("requisites = :requisites", ["requisites" => $command]);
    $reply = "Для завершения оплаты внесите платежи по платежным ссылкам. Ваша оплата разбита на 2 платежа.\r\n\r\nссылка 1.ру/пей30000грн.\r\nссылка 2.ру/пей20000грн.";
    return $this->mainAction('/r', $reply);

  }
}
