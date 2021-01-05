
<script type="text/javascript">
    var sidebarMainDiv = 'main_content';
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Products for Store {{store.name}}</span>
            <button class="UI-button UI-right" id="store-sectionbar-add" onclick='newProduct()'>
                <em class="fas fa-plus-square"></em>
            </button>
        </div>

        <div class='UI-container event-color-secondary'>
            <div class='UI-secondary-sectionbar'>
                <div id="productsTable">
                    <product-list :products="products"></product-list>
                </div>
            </div>
        </div>

        <div class="UI-sidebar-hidden UI-fixed" id="edit_store">
            <div class="UI-center">
                <h2 class="UI-red">Store Details</h2>
            </div>
            <product-form :product='product' :action='action' />
        </div>
    </div>
</div>
