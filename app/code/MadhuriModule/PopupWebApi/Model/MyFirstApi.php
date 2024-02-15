<?php 
namespace MadhuriModule\PopupWebApi\Model;
use MadhuriModule\PopupWebApi\Api\MyFirstApiInterface;

    class MyFirstApi implements MyFirstApiInterface
    {
        public function customGetFunction()
        {
            echo "Hi From" .__CLASS__;
            exit;
        }
    }