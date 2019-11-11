<?php
namespace Codeception\Module;

class UrlHelper extends \Codeception\Module
{
    /**
     * Get current url from WebDriver
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function getCurrentUrl()
    {
        return preg_replace('/\/$/','',$this->getModule('WebDriver')->_getUrl());
    }
}