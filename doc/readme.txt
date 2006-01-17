In your pagelayout template, place the following template code between the head tags:

{def $xajaxjs=xajax_javascript()}
{$xajaxjs}
{undef $xajaxjs}

You won't register new functions with xajax regularly, so you can place it in a non-expiring cache block:

{cache-block ignore_content_expiry expiry=0}
{def $xajaxjs=xajax_javascript()}
{$xajaxjs}
{undef $xajaxjs}
{/cache-block}

Make sure your users have access to the xajax module (add a policy for the module).

There are currently three functions registered with xajax by default. One of them is setPreferences. In JavaScript you can call it with xajax_setPreferences. It takes a hash of user preferences and stores them.

A little example:

Suppose you have a div you want to hide or show, like the content structure menu in the admin interface. It's useless that the whole page needs to be reloaded to show or hide the div and to remember the current state.

<script type="text/javascript">
<!--
{literal}
function toggleDisplayBlock(id)
{
    var el = document.getElementById(id);
    
    if ( el != null )
    {
        var style = el.style;
        var currentDisplay;
        var display;

        if ( el.currentStyle )
        {
            // Internet Explorer way
            currentDisplay = el.currentStyle.display;
        }
        else
        {
            // W3C DOM way
            currentDisplay = document.defaultView.getComputedStyle(el, null).getPropertyValue( 'display' );
        }

        if ( currentDisplay == 'none' )
        {
            style.display = 'block';
        }
        else
        {
            style.display = 'none';
        }
        
        return style.display;
    }
}

function toggleAndRememberState(id, linkid)
{
    var newDisplayValue = toggleDisplayBlock(id);
    
    var linkel = document.getElementById(linkid)
    if ( linkel != null )
    {
        if ( newDisplayValue == 'block' )
        {
            linkel.innerHTML = 'less';
        }
        else
        {
            linkel.innerHTML = 'more';
        }
    }
    
    var prefs = new Array(1);
    prefs[id + '_display'] = newDisplayValue;
    xajax_setPreferences(prefs);
}
{/literal}
-->
</script>
        
{def $alertDisplay=ezpreference('alert_more_display')}
{if $alertDisplay|eq(false())}
{set $alertDisplay = 'none'}
{/if}
<div class="alert" id="alert">
    <div class="title" id="alert_title">
    Warning (<a href="#" id="alert_more_link" onclick="toggleAndRememberState( 'alert_more', 'alert_more_link' );return false;">{if $alertDisplay|eq('block')}less{else}more{/if}</a>)
    </div>
    <div class="more" id="alert_more" style="display:{$alertDisplay};">
        <p>More information on this alert.</p>
    </div>
</div>
{undef $alertDisplay}



The two other functions registered with xajax are addNotification and removeNotification. They are used to handle subtree notifications. They both take the node id and an optional piece of JavaScript to call if they succeed.

A little example:

<script type="text/javascript">
<!--

function notificationRemoved()
{ldelim}
    var divid = 'notification_div';
    var nodeid = {$node.node_id};
    var el = document.getElementById(divid);

    if ( el != null )
    {ldelim}
        var html = '<input id="notification_button" class="button" type="button" value="Keep me updated" onclick="xajax_addNotification(' + nodeid + ', \'notificationAdded()\' );">';
        el.innerHTML = html;
    {rdelim} 
{rdelim}

function notificationAdded()
{ldelim}
    var divid = 'notification_div';
    var nodeid = {$node.node_id};
    var el = document.getElementById(divid);

    if ( el != null )
    {ldelim}
        var html = '<input id="notification_button" class="button" type="button" value="Remove notification" onclick="xajax_removeNotification(' + nodeid + ', \'notificationRemoved()\' );">';
        el.innerHTML = html;
    {rdelim}
{rdelim}

-->
</script>
{def $subscriptions=fetch( 'notification', 'subscribed_nodes' ) $exists=false()}
{foreach $subscriptions as $subscription}
    {if $subscription.node.node_id|eq($node.node_id)}{set $exists=true()}{/if}
{/foreach}

<div id="notification_div">
{if $exists}
<input id="notification_button" class="button" type="button" value="Remove notification" onclick="xajax_removeNotification({$node.node_id}, 'notificationRemoved()');" />
{else}
<input id="notification_button" class="button" type="button" value="Keep me updated" onclick="xajax_addNotification({$node.node_id}, 'notificationAdded()');" />
{/if}
</div>
{undef $subscriptions $exists}



Both examples have been tested with Mozilla Firefox 1.5, Internet Explorer 6 and Opera 8.5.1 on Windows XP with Service Pack 2.
