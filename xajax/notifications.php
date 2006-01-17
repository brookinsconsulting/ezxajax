<?php

function addNotification( $nodeID, $script = false )
{
    $objResponse = new xajaxResponse();

    $message = false;

    include_once( 'kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php' );
    include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );

    $user =& eZUser::currentUser();

    if ( !$user->isLoggedIn() )
    {
        $message= 'You have to be logged in to set notifications.';
    }
    else
    {
        include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
        $contentNode =& eZContentObjectTreeNode::fetch( $nodeID );

        if ( !$contentNode or !$contentNode->attribute( 'can_read' ) )
        {
            $message = 'You do not have access to subscribe for this notification.';
        }
        else
        {
            $nodeIDList =& eZSubtreeNotificationRule::fetchNodesForUserID( $user->attribute( 'contentobject_id' ), false );
            if ( !in_array( $nodeID, $nodeIDList ) )
            {
                $rule =& eZSubtreeNotificationRule::create( $nodeID, $user->attribute( 'contentobject_id' ) );
                $rule->store();
                
                if ( $script )
                {
                    $objResponse->addScript( $script );
                }

                // Clean up content cache
                include_once( 'kernel/classes/ezcontentcachemanager.php' );
                eZContentCacheManager::clearContentCache( $contentNode->attribute( 'contentobject_id' ) );
            }
            else
            {
                $message = 'A notification for this node already exists.';
            }
        }
    }

    if ( $message )
    {
        $objResponse->addAlert( $message );
    }

    return $objResponse->getXML();
}

function removeNotification( $nodeID, $script = false )
{
    $objResponse = new xajaxResponse();

    include_once( 'kernel/classes/notification/handler/ezsubtree/ezsubtreenotificationrule.php' );
    include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );

    $user =& eZUser::currentUser();

    include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
    $contentNode =& eZContentObjectTreeNode::fetch( $nodeID );

    eZSubtreeNotificationRule::removeByNodeAndUserID( $user->attribute( 'contentobject_id' ), $nodeID );

    if ( $contentNode )
    {
        // Clean up content cache
        include_once( 'kernel/classes/ezcontentcachemanager.php' );
        eZContentCacheManager::clearContentCache( $contentNode->attribute( 'contentobject_id' ) );
    }

    if ( $script )
    {
        $objResponse->addScript( $script );
    }

    return $objResponse->getXML();
}

?>
