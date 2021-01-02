<script type="text/javascript">
    var sidebarMainDiv = 'main_content';
</script>


<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Stores</span>
            <button class="UI-button UI-right" id="store-sectionbar-add" onclick='newStore()'>
                <em class="fas fa-plus-square"></em>
            </button>
        </div>

        <div class='UI-container event-color-secondary'>
            <div class='UI-secondary-sectionbar'>
                <div id="storesTable">
                    <store-list :stores="stores"></store-list>                    
                </div>
            </div>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="edit_store">
        <div class="UI-center">
            <h2 class="UI-red">Store Details</h2>
        </div>
        <store-form v-bind:store='store' v-bind:action='action' />
    </div>
</div>

<script type="text/javascript">

</script>
