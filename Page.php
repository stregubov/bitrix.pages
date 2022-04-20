<?php

use Bitrix\Main\Application;

class Page
{
    /**
     * @var array свойства элемента инфоблока
     */
    private $properties;
    /**
     * @var array стандартные поля элемента инфоблока
     */
    private $fields;
    /**
     * @var array пользовательские поля
     */
    private $ufProperties;
    /**
     * @var array seo свойства
     */
    private $seo;

    /**
     * Page constructor.
     * @param array $properties
     * @param array $fields
     * @param array $ufProperties
     * @param array $seo
     */
    public function __construct(array $fields = [], array $properties = [], array $ufProperties = [], array $seo = [])
    {
        $this->properties = $properties;
        $this->fields = $fields;
        $this->ufProperties = $ufProperties;
        $this->seo = $seo;
    }

    /**
     * @return array|null
     */
    public function getProperties(): ?array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @param string $propertyName
     * @return array|null
     */
    public function getProperty(string $propertyName): ?array
    {
        return $this->properties[$propertyName];
    }

    /**
     * @return array|null
     */
    public function getUfProperties(): ?array
    {
        return $this->ufProperties;
    }

    /**
     * @param array $ufProperties
     */
    public function setUfProperties(array $ufProperties): void
    {
        $this->ufProperties = $ufProperties;
    }

    /**
     * @param string $ufPropertyName
     * @return mixed
     */
    public function getUfProperty(string $ufPropertyName)
    {
        return $this->ufProperties[$ufPropertyName];
    }

    /**
     * @return array|null
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @param string $fieldName
     * @return mixed
     */
    public function getField(string $fieldName)
    {
        return $this->fields[$fieldName];
    }

    /**
     * @return array|null
     */
    public function getSeo(): ?array
    {
        return $this->seo;
    }

    /**
     * @param array $seo
     */
    public function setSeoToPage(array $seo): void
    {
        $this->seo = $seo;
        global $APPLICATION;

        if ($this->seo['ELEMENT_PAGE_TITLE']) {
            $APPLICATION->SetTitle($this->seo['ELEMENT_PAGE_TITLE']);
        }

        if ($this->seo['ELEMENT_META_DESCRIPTION']) {
            $APPLICATION->SetPageProperty('description', $this->seo['ELEMENT_META_DESCRIPTION']);
        }

        if ($this->seo['ELEMENT_META_TITLE']) {
            $APPLICATION->SetPageProperty('title', $this->seo['ELEMENT_META_TITLE']);
        }
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->fields)) {
            return $this->getField($name);
        }

        if (array_key_exists($name, $this->properties)) {
            return $this->getProperty($name);
        }

        if (array_key_exists($name, $this->ufProperties)) {
            return $this->getUfProperty($name);
        }

        $trace = debug_backtrace();
        trigger_error(
            self::class.' '.'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_ERROR);
    }
}
