<?php

namespace App\Telegram\Message;

class CarPrefMessage
{
    public function get(array $car) : string {
        return  "
Продавец: {$car['sellername']}
Город: {$car['locationname']}
Бренд: {$car['brand']}
Модель: {$car['model']}
Поколение: {$car['generation']}
Год: {$car['year']}
Цена: {$car['price']}$
Ссылка: {$car['publicurl']}";
    }

}
