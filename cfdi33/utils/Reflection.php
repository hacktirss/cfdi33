<?php
/*
 * Reflection
 * cfdi®
 * © 2018, Softcoatl 
 * http://www.softcoatl.mx
 * @author Rolando Esquivel Villafaña, Softcoatl
 * @version 1.0
 * @since dic 2017
 */
namespace com\softcoatl\utils;

class Reflection {

    /**
     * 
     * @param Object $Object
     * @param DOMElement $node
     */
    public static function setAttributes($Object, $node) {

        for ($i=0; $i<$node->attributes->length; $i++) {
            $attr = $node->attributes->item($i)->nodeName;
            $setter = "set" . $attr;
            if (method_exists($Object, $setter)) {
                $Object->{$setter}($node->getAttribute($attr));
            }
        }
    }
}
