# bitrix.pages

Разработка, которая позволяет сделать инфоблок "Страницы" и добавить функционал пользовательских свойств (UF). Это удобно для заполнения контента страниц, заполнения SEO свойств, добавления картинок и т.д. без использования стандартного "Эрмитажа".

Для использования нужно будет добавить обработчик события в php_interface/init.php и создать инфоблок с символьным кодом "pages".

```php
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('main', 'OnAdminIBlockElementEdit', function () {
        $tabset = new TabProp();
        return [
            'TABSET' => 'custom_props',
            'Check' => [$tabset, 'check'],
            'Action' => [$tabset, 'action'],
            'GetTabs' => [$tabset, 'getTabList'],
            'ShowTab' => [$tabset, 'showTabContent'],
        ];
});
```

В результате у инфоблока "pages" появится новая вкладка с пользовательскими полями.

## Использование в коде

Чтобы получить объект страницы на index.php нужного раздела вызываем

```php
$page = PageService::getInstance()->get();
```
и далее через подобным образом $page->NAME получаем нужные поля и выводим в html шаблонах
