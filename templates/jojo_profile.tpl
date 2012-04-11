{if $error}<div class="error">{$error}</div>{/if}
{if $jojo_profile}
   <div class="profile">
        {if $jojo_profile.image && $jojo_profile.mainimage}<img src="images/{if $jojo_profile.mainimage}{$jojo_profile.mainimage}{else}v60000{/if}/{$jojo_profile.image}" alt="{$jojo_profile.title}" class="float-right"/>{/if}
        {if $jojo_profile.pr_quote}<div class="profilebody">{$jojo_profile.pr_quote}</div>{/if}
        {if $jojo_profile.title}<h4>{$jojo_profile.title}</h4>{/if}
        {$jojo_profile.pr_description}
        {if $jojo_profile.phone || $jojo_profile.pr_email}<p>{if $jojo_profile.phone}Phone: {$jojo_profile.phone}<br />{/if}{if $jojo_profile.fax}Fax: {$jojo_profile.fax}<br />{/if}{if $jojo_profile.pr_email}Email: <a href="mailto:{$jojo_profile.pr_email}">{$jojo_profile.pr_email}</a>{/if}</p>{/if}
    </div>
{if $tags}<p class="tags"><strong>Tags: </strong>
    {if $itemcloud}{$itemcloud}
    {else}{foreach from=$tags item=tag}<a href="{if $multilangstring}{$multilangstring}{/if}tags/{$tag.url}/">{$tag.cleanword}</a>
    {/foreach}</p>
    {/if}
{/if}
{if $commenthtml}{$commenthtml}{/if}
{if $jojo_profile.nextprevious}<p class="links">&lt;&lt; <a href="{$jojo_profile.pageurl}" title="back">{$jojo_profile.pagetitle}</a>&nbsp; {if $prevprofile}&lt; <a href="{$prevprofile.url}" title="Previous">{$prevprofile.fullname}</a>{/if}{if $nextprofile} | <a href="{$nextprofile.url}" title="Next">{$nextprofile.fullname}</a> &gt;{/if}</p>{/if}

{else}
	{if $pg_body && $pagenum==1}{$pg_body}{/if}
	{foreach from=$jojo_profiles item=p}
    <h3 class="clear">{if $p.snippet!='full'}<a href="{$p.url}" title="{$p.fullname}">{/if}{$p.fullname}{if $p.quals} <span class="qualifications">{$p.quals}</span>{/if}{if $p.snippet!='full'}</a>{/if}{if $p.title && ($p.title != $p.name)} - {$p.title}{/if}</h3>
    <div class="profile">
        {if $p.image && $p.thumbnail}{if $p.snippet!='full'}<a href="{$p.url}" title="{$p.title}">{/if}<img src="{$SITEURL}/images/{if $p.snippet=='full' && $p.mainimage}{$p.mainimage}{elseif $p.thumbnail}{$p.thumbnail}{else}s135{/if}/{$p.image}" class="index-thumb" alt="{$p.title}" />{if $p.snippet!='full'}</a>{/if}{/if}
        {if $p.snippet=='full' || $p.bodyplain|strlen < $p.snippet}{$p.pr_description}{$p.pr_quote}
        {else}<p>{$p.bodyplain|truncate:$p.snippet}
        {if $p.readmore}<a href="{$p.url}" title="{$p.title}" class="more">##{$p.readmore}##</a></p>{/if}{/if}
    </div>
	{/foreach}
	<div class="article-pagination links">
		{$pagination}
	</div>
{/if}
