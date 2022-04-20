<?php

use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;

class PageService
{
    /**
     * @var PageService объект класса
     */
    protected static $instance;

    /**
     * @var string символьный код инфоблока "Страницы"
     */
      private $iblockCode = "page";

    /**
     * @var Page класс модели
     */
    private $page;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!Loader::includeModule('iblock')) {
            exit('Модуль инфоблоки не установлен');
        }

        if (self::$instance === null) {
            self::$instance = new static;
        }

        return static::$instance;
    }

    public function __clone()
    {
    }

    public function __wakeup()
    {
    }

    /**
     * Метод, который непосредственно получает данные из инфоблока
     * @param string $code символьный код элемента инфоблока (CODE)
     * @throws \Exception
     */
    public function init($code = ''): void
    {
        global $APPLICATION;

        global $USER_FIELD_MANAGER;

        $page = $APPLICATION->getCurDir();

        if ($code) {
            $page = $code;
        }

        Loader::includeModule('iblock');
        $cacheDir = "/pages";

        $cache = Cache::createInstance();
        $cacheKey = self::class . $page;
        if ($cache->initCache(3600, $cacheKey, $cacheDir)) {
            $data = $cache->getVars();

            $this->page = new Page();
            $this->page->setFields($data['fields']);
            $this->page->setProperties($data['properties']);
            $this->page->setUfProperties($data['props']);
            $this->page->setSeoToPage($data['seo']);
        } elseif ($cache->startDataCache()) {
            $arFilter = [
                'IBLOCK_CODE' => $this->iblockCode,
                'CODE' => $page
            ];

            $rsPage = \CIBlockElement::GetList([], $arFilter, false, false, ['*']);
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cacheDir);

            if ($ob = $rsPage->getNextElement()) {

                $arFields = $ob->getFields();
                $arProperties = $ob->getProperties();
                $elementId = $arFields['ID'];

                $arUserFields = $USER_FIELD_MANAGER->GetUserFields('ELEMENT_PROP_' . $elementId, $elementId,
                    LANGUAGE_ID);
                $arProps = [];
                foreach ($arUserFields as $code => $arUserField) {
                    $arProps[$code] = $arUserField['VALUE'];
                }

                $seo = (new ElementValues($this->iBlockId, $elementId))->getValues();

                // Инициализация модели Page
                $this->page = new Page();
                $this->page->setFields($arFields);
                $this->page->setProperties($arProperties);
                $this->page->setUfProperties($arProps);
                $this->page->setSeoToPage($seo);

                $CACHE_MANAGER->RegisterTag("iblock_id_".$arFields["IBLOCK_ID"]);
                $CACHE_MANAGER->EndTagCache();

                $cache->endDataCache([
                    'fields' => $arFields,
                    'properties' => $arProperties,
                    'props' => $arProps,
                    'seo' => $seo
                ]);

            }
        }
    }

    /**
     * Метод для получения данных на странице
     * @param string $code Код элемента из инфоблока "Страницы"
     * @return Page модель страницы
     * @throws \Exception
     */
    public function get($code = ''): ?Page
    {
        if ($code) {
            $this->init($code);
        }

        return $this->page;
    }

    /**
     * Полное копирование всех пользовательских полей из одной одного элемента инфоблока в другой. Удобно при копировании страниц
     * @param int $entity_id исходная элемент
     * @param int $target_entity_id элемент в который будет копироваться
     */
    public function copyUF(int $entity_id, int $target_entity_id): void
    {
        $rsData = \CUserTypeEntity::GetList(array(), ['ENTITY_ID' => 'ELEMENT_PROP_' . $entity_id]);

        global $USER_FIELD_MANAGER;

        $aUserField = $USER_FIELD_MANAGER->GetUserFields(
            'ELEMENT_PROP_' . $entity_id,
            $entity_id
        );

        while ($arRes = $rsData->GetNext()) {
            $oUserTypeEntity = new \CUserTypeEntity();
            $aUserFields = array(
                'ENTITY_ID' => 'ELEMENT_PROP_' . $target_entity_id,
                'FIELD_NAME' => $arRes['FIELD_NAME'],
                'USER_TYPE_ID' => $arRes['USER_TYPE_ID'],
                'SORT' => $arRes['SORT'],
                'MULTIPLE' => $arRes['MULTIPLE'],
                'MANDATORY' => $arRes['MANDATORY'],
                'SHOW_FILTER' => $arRes['SHOW_FILTER'],
                'SHOW_IN_LIST' => $arRes['SHOW_IN_LIST'],
                'EDIT_IN_LIST' => $arRes['EDIT_IN_LIST'],
                'IS_SEARCHABLE' => $arRes['IS_SEARCHABLE'],
                'SETTINGS' => $arRes['SETTINGS']
            );
            $iUserFieldId = $oUserTypeEntity->Add($aUserFields);

            if ($iUserFieldId) {
                $USER_FIELD_MANAGER->Update('ELEMENT_PROP_' . $entity_id, $entity_id, array(
                    $arRes['FIELD_NAME'] => $aUserField[$arRes['FIELD_NAME']]['VALUE']
                ));
            }
        }
    }
}
