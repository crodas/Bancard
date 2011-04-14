<?php
/**!
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <crodas@php.net> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Cesar Rodas.
 * ----------------------------------------------------------------------------
 */

$current = dirname(__FILE__);

require $current . "/Bancard/Exception.php";
require $current . "/Bancard/Config.php";

class Bancard
{
    protected $config;

    protected $nro_pedido;
    protected $clavecomercio;

    /**
     *  
     *
     */
    public function __construct(Bancard_Config $config)
    {
        $this->config = $config;
    }

    protected function validateInput($type)
    {
       return isset($_GET[$type . '_input']) && ctype_digit($_GET[$type . '_input']) && ($this->{$type} = $_GET[$type . '_input']);
    }

    protected function validate()
    {
        if (!$this->validateInput('clavecomercio') || !$this->validateInput('nro_pedido')) {
            throw new Bancard_Exception('Falta parametros');
        }
        if ($this->config->getComercioId() != $this->clavecomercio) {
            throw new Bancard_Exception('Comercio invalido');
        }
        $pedido = $this->config->get($this->nro_pedido);
        if (!is_array($pedido)) {
            throw new Bancard_Exception('No existe el pedido');
        }
        return $pedido;
    }

    public function format($value, $length=0)
    {
        return str_pad($value, $length, '0', STR_PAD_LEFT) . ':';
    }

    public function pedido()
    {
        header('content-type: text/plain');
        $pedido    = $this->validate();
        $response  = $this->format($this->config->ISP, 7);
        $response .= $this->format($pedido['monto'], 10);
        $response .= $this->format($pedido['monto_orig'] * 100, 12);
        $response .= $this->format($pedido['moneda'] == 'gs' ? 600 : 840);
        $response .= $this->format($pedido['op']);
        $response .= $this->format($pedido['extra']);
        die($response);
    }

    protected function parseResponse($response, $format)
    {
        $parts = explode(":", $response);
        foreach ($parts as $id => $part) {
            if (empty($format[$id])) {
                throw new Bancard_Exception('Respuesta invalida');
            }
            switch ($format[$id]) {
            case 'i':
                $parts[$id] = (int)$part;
                break;
            case 's':
                $parts[$id] = trim(preg_replace('/(_+)$/', ' ', $part));
                break;
            }
        }
        return $parts;
    }

    public function confirmacion()
    {
        $pedido = $this->validate();

        $responseUrl = $this->config->domain . "/webbancard/?MIval=/ECOM/pasa_respuesta.html&" . http_build_query($_GET);
        $responseStr = file_get_contents($responseUrl);
        $responseObj = $this->parseResponse($responseStr, 'isiiss');

        if ($responseObj[0] != 0 || $responseObj[2] <= 0) {
            throw new Bancard_Exception($responseObj[1]);
        }

        if ($responseObj[3] != $pedido['monto']) {
            $exception = new Bancard_Exception_Monto($responseStr);
            $exception->monto   = $pedido['monto'];
            $execption->cobrado = $responseObj[3];
            throw new $exception;
        }

        $this->config->confirm($this->nro_pedido, $responseObj[2], $responseObj[4]);
        return $responseObj;
    }

    public function cobrar($monto, $moneda='gs', $monto_orig=-1, $op = 0, $extra='')
    {
        if ($moneda != 'gs' && $monto_orig <= 0) {
            throw new Bancard_Exception('Si la moneda no es gs, se debe asignar \$monto_orig');
        }
        if ($monto_orig == -1) {
            $monto_orig = $monto;
        }
        $tID = $this->config->save(compact('monto', 'monto_orig', 'moneda', 'extra', 'op'));

        $url = $this->config->domain . '/cgi-bin/pagina_pagos?' . http_build_query(array('clavecomercio_input' => $this->config->getComercioId(), 'nro_pedido_input' => $tID)) ;

        header('location: ' . $url);
        exit;
    }
}
