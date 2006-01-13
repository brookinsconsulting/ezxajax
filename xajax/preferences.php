<?php

function setPreferences( $arg )
{
    $objResponse = new xajaxResponse();

    include_once( 'kernel/classes/ezpreferences.php' );

    if ( is_array( $arg ) )
    {
        foreach ( array_keys( $arg ) as $key )
        {
            eZPreferences::setValue( $key, $arg[$key] );
        }
    }

    return $objResponse->getXML();
}

?>
