<?php

use Hexlet\Code\Request\StoreUrlRequest;

return [
    'site_title' => 'Анализатор страниц',
    'home_description' => 'Бесплатно проверяйте сайты на SEO-пригодность',
    'url_label' => 'Адрес сайта',
    'check_button' => 'Проверить',
    'home' => 'Главная',
    'not_found_title' => 'Страница не найдена',
    'not_found_description' => 'Проверьте адрес или вернитесь на главную.',
    'back_to_home' => 'На главную',
    'sites' => 'Сайты',
    'site_details' => 'Сайт: %s',
    'id' => 'ID',
    'name' => 'Имя',
    'created_at' => 'Дата создания',
    'checks' => 'Проверки',
    'run_check' => 'Запустить проверку',
    'last_check_at' => 'Последняя проверка',
    'check_added' => 'Страница успешно проверена',
    'check_failed' => 'Произошла ошибка при проверке, не удалось подключиться',
    'status_code' => 'Код ответа',
    'url_added' => 'Страница успешно добавлена',
    'url_exists' => 'Страница уже существует',
    'url_invalid' => 'Некорректный URL',
    'url_required' => 'URL не должен быть пустым',
    'url_too_long' => sprintf('URL превышает %d символов', StoreUrlRequest::MAX_URL_LENGTH),
];
