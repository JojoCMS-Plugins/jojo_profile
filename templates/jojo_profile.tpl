{if $error}<div class="error">{$error}</div>{/if}
{if $jojo_profile}
   <div class="profile">
        {if $jojo_profile.pr_image}<img src="images/v60000/profiles/{$jojo_profile.pr_image}" alt="{$jojo_profile.pr_title}" class="right-image"/>{/if}
        {if $jojo_profile.pr_quote}<div class="profilebody">{$jojo_profile.pr_quote}</div>{/if}
        {if $jojo_profile.pr_title}<h4>{$jojo_profile.pr_title}</h4>{/if}
        {$jojo_profile.pr_description}
    </div>
<p class="links">&lt;&lt; <a href="{$indexurl}" title="back">{$pg_title} index</a>&nbsp; {if $prevprofile}&lt; <a href="{$prevprofile.url}" title="Previous">{$prevprofile.pr_name}</a>{/if}{if $nextprofile} | <a href="{$nextprofile.url}" title="Next">{$nextprofile.pr_name}</a> &gt;{/if}</p>
{if $tags}
    <p class="tags"><strong>Tags: </strong>
{if $itemcloud}
        {$itemcloud}
{else}
{foreach from=$tags item=tag}
        <a href="{if $multilangstring}{$multilangstring}{/if}tags/{$tag.url}/">{$tag.cleanword}</a>
{/foreach}
    </p>
{/if}
{/if}

{else}
	{if $pg_body && $pagenum==1}{$pg_body}{/if}
	{foreach from=$jojo_profiles item=p}
    <div  style="display: block;clear:both;">
        {if $p.pr_image}<img src="images/v10000/profiles/{$p.pr_image}" alt="{$p.pr_title}" class="right-image" />{/if}
        <h3><a href="{$p.url}" title="{$p.pr_name}">{$p.pr_name}</a>{if $p.pr_title && ($p.pr_title != $p.pr_name)} - {$p.pr_title}{/if}</h3>
        <p>{$p.pr_description|truncate:200}</p>
        <p><a href="{$p.url}" class="links" title="View full profile" rel="nofollow">&gt; Read More</a></p>
    </div>
	{/foreach}
	<div class="article-pagination links">
		{$pagination}
	</div>
{/if}