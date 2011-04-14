<?php
/**!
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <crodas@php.net> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Cesar Rodas.
 * ----------------------------------------------------------------------------
 */

abstract class Bancard_Config
{
    // dominio de bancard
    public $domain = 'http://temp.bancard.com.py';
    public $ISP    = 901;

    final public function get($id)
    {
        $return = $this->dbGet($id);
        if (!is_array($return)) {
            throw new Bancard_Exception("dbGet retorno datos invalidos");
        }

        /* validate return */
        $check = array('monto', 'monto_orig', 'moneda', 'op' => 0, 'extra' => '');
        foreach ($check as $id => $value) {
            if (is_numeric($id)) {
                if (empty($return[$value])) {
                    throw new Bancard_Exception("dbGet no retorno {$value}");
                }
            } else {
                if (empty($return[$id])) {
                    $return[$id] = $value;
                }
            }
        }

        return $return;
    }

    final public function save(array $data)
    {
        return $this->dbSave($data);
    }


    final public function confirm($id, $transId, $nombre)
    {
        return $this->dbConfirm($id, $transId, $nombre);
    }

    abstract public function getComercioId();
    abstract public function dbGet($id);
    abstract public function dbSave(array $data);
    abstract public function dbConfirm($id, $transId, $nombre);
}

