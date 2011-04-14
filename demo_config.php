<?php
/**!
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <crodas@php.net> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Cesar Rodas.
 * ----------------------------------------------------------------------------
 */

require "../lib/Bancard.php";

/**
 *  Demostracion de como configurar Bancard con MySQL
 *
 */
class Bancard_Config_Demo extends Bancard_Config
{
    protected $link;
    protected $tabla = 'foobar';

    public function __construct()
    {
        $this->link = mysql_connect('localhost', 'root', 'password');
        mysql_select_db('algo', $this->link);
    }

    public function getComercioId()
    {
        return 9343;
    }

    public function dbSave(array $data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = addslashes($value);
        }

        mysql_query("INSERT INTO `{$this->tabla}`(`moneda`, `monto`, `monto_orig`, `op`, `extra`) VALUES('{$data['moneda']}', '{$data['monto']}', '{$data['monto_orig']}', '{$data['op']}', '{$data['extra']}')", $this->link);

        return mysql_insert_id();
    }

    public function dbConfirm($id, $transId, $nombre)
    {
        $id = (int)$id;
        $transId = (int)$transId;
        $nombre = addslashes($nombre);

        mysql_query("UPDATE `{$this->tabla}` SET nombre='{$nombre}', confirmacion='{$transId}', cobrado=now() WHERE id = {$id}", $this->link);
    }

    public function dbGet($id) 
    {
        $id = (int)$id;
        $query = mysql_query("SELECT * FROM `{$this->tabla}` WHERE id = {$id}", $this->link);
        $result = mysql_fetch_array($query);
        mysql_free_result($query);
        return $result;
    }
}

