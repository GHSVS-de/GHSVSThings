<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Einen Node erfinden, der beim Lesen und Schreiben verwendet wird.
$node = 'tralalaNode';

// Session verfügbar machen.
$session = Factory::getSession();

// Der Wert, den man schreiben möchte.
$wert = 'hallo';

// In der Session ablegen:
$session->set($node, $wert);

// Anderstwo den Wert auslesen. Zuvor wie oben initialisieren, die Session und den Node.
$sessionData = $session->get($node);


echo ' DEBUG $sessionData <pre>' . print_r($sessionData, true) . '</pre>';exit;
