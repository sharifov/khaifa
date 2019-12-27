            <nav class="profile_side_menu">
                <h1 class="profile-head"><span class="profile-head-name">FAQ</span></h1>
                <div class="nav-list">
                    <ul>
                        {if $faq_categories}
                        {foreach from=$faq_categories item=faq_category}
                            <li {if trim($faq_category->slug) eq trim($current_faq_category->slug)} class="active" {/if}><a href="{site_url_multi('faq/category/')}{$faq_category->slug}">{$faq_category->name}</a></li>
                        {/foreach}
                        {/if}
                    </ul>
                </div>
            </nav>  