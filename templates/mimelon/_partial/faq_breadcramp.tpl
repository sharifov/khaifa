            <!--starts faq breadcramp-->
            <header class="page-header faq_breadcramp">
                <ol class="breadcrumb page-breadcrumb">
                    <li class="active"><a href="{base_url()}">{translate('home',true)}</a></li>
                    <li class="active"><a href="{site_url_multi('faq')}">FAQ</a></li>
                    <li><a href="{site_url_multi('faq/category/')}{$current_faq_category->slug}">{$current_faq_category->name}</a></li>
                    {if isset($faq)}
                        <li><a href="{current_url()}">{$faq->name}</a></li>
                    {/if}
                </ol>
            </header>
            <!--ends faq breadcramp-->