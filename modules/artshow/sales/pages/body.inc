<script type="text/javascript">
    var sidebarMainDiv = "main_content";

    userLookup.options({
        partialMatch: true,
        needForm: false,
        handler: artSale.lookupUser
        });

    lookupPiece.options({
        needForm: false,
        handler: artSale.lookupPiece
    });

    artshowPiece.options({
        additionalFields: ['Location', 'Notes', 'Charity']
    });
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Art Show Sales</span>
        </div>

        <div id="headline_section" class= "UI-container UI-border UI-margin UI-padding UI-hide">
        </div>

        <div class="UI-container">
         <div class="UI-half">
            <div class="UI-center">
                <div class='UI-bar-item' id='userLookup_div'></div>
            </div>
        </div>

        <div class="UI-half">
            <div class="UI-center">
                <div class='UI-bar-item' id='lookupPiece_div'></div>
            </div>
        </div>
&nbsp;
       </div>

    <hr>
        <div class="UI-container">
        </div>
    </div>
</div>
