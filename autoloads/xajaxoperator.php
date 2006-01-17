<?php

class XajaxOperator
{
    var $Operators;

    function XajaxOperator( )
    {
        $this->Operators = array( 'xajax_javascript' );
    }

    function &operatorList( )
    {
        return $this->Operators;
    }

    /*!
     \return true to tell the template engine that the parameter list exists per operator type.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

        /*!
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array
        (
            'xajax_javascript' => array()
        );
    }

    /*!
     \reimp
    */
    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'xajax_javascript':
                {
                    include_once( 'extension/xajax/lib/xajax/xajax.inc.php' );
                    include_once( 'lib/ezutils/classes/ezuri.php' );
                    $xajaxModuleView = '/xajax/call';
                    eZURI::transformURI( $xajaxModuleView );
                    $xajax = new xajax( $xajaxModuleView );
                    
                    include_once( 'lib/ezutils/classes/ezextension.php' );
                    include_once( 'lib/ezutils/classes/ezini.php' );
                
                    $ini =& eZINI::instance( 'xajax.ini' );
                    
                    if ( $ini->variable( 'DebugSettings', 'DebugAlert' ) == 'enabled' )
                    {
                        $xajax->debugOn();
                    }
                    
                    $functionFiles = $ini->variable( 'ExtensionSettings', 'AvailableFunctions' );
                    $extensionDirectories = array_merge( 'xajax', $ini->variable( 'ExtensionSettings', 'ExtensionDirectories' ) );
                    $directoryList = eZExtension::expandedPathList( $extensionDirectories, 'xajax' );
                
                    if ( count( $functionFiles ) > 0 )
                    {
                        foreach ( $functionFiles as $function => $functionFile )
                        {
                            foreach ( $directoryList as $directory )
                            {
                                $handlerFile = $directory . '/' . strtolower( $functionFile ) . '.php';
                                if ( file_exists( $handlerFile ) )
                                {
                                    $xajax->registerExternalFunction( $function, $handlerFile );
                                }
                            }
                        }
                    }
                    
                    include_once( 'lib/ezutils/classes/ezsys.php' );
                    $sys =& eZSys::instance();
                    $operatorValue = $xajax->getJavascript( $sys->wwwDir() . '/extension/xajax/design/standard/javascript/', 'xajax.js', 'extension/xajax/design/standard/javascript/xajax.js' );

                }break;
            default:
                {
                    eZDebug::writeError( 'Unknown operator: ' . $operatorName, 'xajaxoperator.php' );
                }
        }
    }

    /// \privatesection
    var $Operators;
};

?>
