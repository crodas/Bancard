<?php
/**!
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <crodas@php.net> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Cesar Rodas.
 * ----------------------------------------------------------------------------
 */
class Bancard_Exception extends Exception
{
}

class Bancard_ExceptionMonto extends Bancard_Exception
{
    public $cobrado;
    public $monto;
}

