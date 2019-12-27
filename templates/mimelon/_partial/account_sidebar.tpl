{if $customer}
<nav class="profile_side_menu">
    <h1 class="profile-head"><span class="profile-head-name">{translate('hi',true)} {$customer->firstname} {$customer->lastname} !</span></h1>
    <div class="nav-list">
        <ul>
            <li {if $controller eq 'account' && $method eq 'index'} class="active" {/if}><a href="{site_url_multi('account')}">{translate('my_account',true)}</a></li>
            <li {if $method eq 'orders'} class="active" {/if}><a href="{site_url_multi('account/orders')}">{translate('my_orders',true)}</a></li>
            <li {if $method eq 'address_book'}  class="active" {/if}><a href="{site_url_multi('account/address_book')}">{translate('address_book',true)}</a></li>
            <li {if $controller eq 'faq'}  class="active" {/if} ><a href="{site_url_multi('faq')}">{translate('faq',true)}</a></li>
            <li class="passive"><a href="{site_url_multi('account/logout')}">{translate('sign_out',true)}</a></li>
        </ul>
    </div>
</nav>
{/if}