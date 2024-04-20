<?php

namespace App\Telegram;

use App\Telegram\Keyboards\CarBrandKb;
use App\Telegram\Keyboards\CarModelKb;
use App\Telegram\Keyboards\StarKb;
use App\Telegram\Keyboards\SettingKb;


// use App\Telegram\Commands\Command;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;


class Handler extends WebhookHandler
{
    private CarBrandKb $carBrandKb;

    private CarModelKb $carModelKb;
    private StarKb $startKb;

    private SettingKb $settingKb;


    // private Command $command;

    public function __construct(CarBrandKb $carBrandKb, CarModelKb $carModelKb, StarKb $startKb, SettingKb $settingKb)
    {
        parent::__construct();
        $this->carBrandKb = $carBrandKb;
        $this->carModelKb = $carModelKb;
        $this->startKb = $startKb;
        $this->settingKb = $settingKb;
        // $this->command = $command;
    }

    public function start(): void
    {
        Telegraph::message('*Привет!*')->replyKeyboard(
            $this->startKb->getStartKb()
        )->send();
    }




    public function setting(): void
    {
        $mess = "
*Настройки*\n
Добавление нескольких фильтров позволит Вам создавать комбинации из разных параметров.\n 
👁 - посмотреть текущую настройку
⚙ - настроить фильтр
📑 - создать копию фильтра
❌ - удалить фильтр
";



        Telegraph::message($mess)->keyboard(
            $this->settingKb->getSettings()
        )->send();
    }

    public function add_filter(): void
    {
        $mess = "*Выбырите марку машины*";

        $kb = $this->carBrandKb->getInlineKb();
        Telegraph::message($mess)->keyboard($kb)->send();
    }

    public function car_brand(): void
    {
        $car_brand_text = $this->data->get("car_brand");

        $this->bot->storage()->set('car_brand_text', $car_brand_text);

        $mess = "$car_brand_text*Выбырите модель машины*";

        $kb = Keyboard::make()
            ->row([
                Button::make('Audi 100')->action('set_car_model')->param('car_model_name', 'Audi 100'),
            ]);
        Telegraph::message($mess)->keyboard($kb)->send();
    }

    public function set_car_model(): void
    {
        $car_model_name = $this->data->get("car_model_name");
        $this->bot->storage()->set('car_model_name', $car_model_name);

        $mess = "$car_model_name*Выбырите цену*";

        $kb = Keyboard::make()
            ->row([
                Button::make('100$')->action('set_car_price')->param('car_price', '100$'),
            ]);
        Telegraph::message($mess)->keyboard($kb)->send();
    }


    public function set_car_price(): void
    {
        $car_price = $this->data->get("car_price");
        $car_model = $this->bot->storage()->get('car_model_name');
        $car_brand = $this->bot->storage()->get('car_brand_text');
        $mess = "$car_price*Настройка завершена!*

        Ваши настройки️:
        Предпочитаемые машины:
        $car_price
        $car_model
        $car_brand 

        ";

        Telegraph::message($mess)->send();
    }


    public function help(): void
    {
        $this->reply("I will help you");
    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        if ($text->value() === 'Настройки') {
            $this->setting();
        }
        if ($text->value() === 'Начать поиск') {
            $this->add_filter();
        }
        if ($text->value() === 'Справка') {
            $this->help();
        }
    }
}
