<?php
/**!
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <crodas@php.net> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Cesar Rodas.
 * ----------------------------------------------------------------------------
 */

require "../demo_config.php";

try {
    $bancard = new Bancard(new Bancard_Config_Demo);
    $confirmacion = $bancard->confirmacion();
    // hacer algo con este array 
    var_Dump($confirmacion);
} catch (Bancard_Exception_Monto $e) {
    /* bancard cobro menos de lo que pedimos */
    exit;
} catch (Bancard_Exception $e) {
    /* otro error */
    exit;
}
