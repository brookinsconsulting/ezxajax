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

There's currently only one function registered with xajax by default, and it's called setPreferences. In JavaScript you can call it with xajax_setPreferences. It takes a hash of user preferences and stores them.

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