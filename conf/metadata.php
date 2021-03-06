<?php
/**
 * Options for the lsb plugin
 *
 * @author Fernando Ribeiro <pinguim.ribeiro@gmail.com>
 */

//$meta['fixme'] = array('string');


$meta['networks']           = array('string', '_pattern' => '/^[a-zA-Z\s]*$/');

$meta['display']            = array('multichoice','_choices' => array('name', 'icon', 'color'));

$meta['target']             = array('multichoice','_choices' => array('_blank', '_self', '_parent', '_top'));

$meta['twitter_user']       = array('string', '_pattern' => '/^[a-zA-Z0-9_]*$/');
