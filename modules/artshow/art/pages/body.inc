<script type="text/javascript">
    var sidebarMainDiv = "main_content";

    userProfile.options({
        title: 'Artist Information',
        updateButtonText: '',
        inlineUpdateButton: false,
        panes: ['name'],
        readonly: true
    });

    userLookup.options({
        partialMatch: true,
        needForm: false,
        handler: artPage.lookup
        });

    hungArtTable.options({
        onclick: 'artPage.editPiece',
      columns: [{value: 'PieceID', title: 'Piece Number'},
        {value: 'Name', title:'Piece Name'}, 'Medium',
        'Edition', {value:'PieceType', title:'Type'},
        {value: 'NFS', title:'Not<br>For<br>Sale'}, 'Prices',
        {value:'Fee', title:'Hanging Fee'}, 'Location', 'Status', 'Notes'],
    });

    printArtTable.options({
        onclick: 'artPage.editPrint',
    });

    artshowPiece.options({
        additionalFields: ['Location', 'Notes', 'Charity']
    });
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Art Show Art</span>
        </div>

        <div id="headline_section" class=
        "UI-container UI-border UI-margin UI-padding UI-hide"></div>

        <div class="UI-center">
            <div class='UI-bar-item' id='userLookup_div'></div>
        </div>

        <div class="UI-container">
            <div id="userProfile"></div>

            <div class="UI-container UI-center">
                <button class="UI-eventbutton UI-margin" id=
                "artist_profile" onclick="artPage.artistProfile();"
                disabled="disabled">Edit Artist&quot;s Profile</button>
            </div>

            <div class="UI-continer">
                <div class="ARTSHOW-profile-company-div">
                    <div>
                        Company Name
                    </div>

                    <div class="UI-container">
                        <input type="text" class="UI-input" id="company"
                        value='' disabled="disabled" placeholder=
                        "Company Name">
                    </div>
                </div>
            </div>
        </div>

        <div class="UI-secondary-sectionbar">
            Art In Show
        </div>

        <div class="UI-container UI-margin">
            <div class="UI-secondary-sectionbar">
                Pieces Hung in Art Show
            </div>

            <div class="UI-center UI-padding">
                <button class="UI-eventbutton" name="art_button" onclick=
                "artPage.addHungArt();" disabled="disabled">Add Hung Art</button>
                <button class="UI-eventbutton" name="art_button" onclick=
                "artPage.tagHungArt();" disabled="disabled">Print Hung Art Tags</button>

                <div>
                    <div id="artshow_entered"></div>

                    <div class="UI-secondary-sectionbar">
                        Pieces in Art Show Print Shop
                    </div>

                    <div class="UI-center UI-padding">
                        <button class="UI-eventbutton" name="art_button"
                        onclick= "artPage.addPrintArt();" disabled="disabled">Add Print Shop
                        Art</button>

                        <div>
                            <div id="artshow_printshop"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="hung_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>Hung Art</h2>
        </div>

        <div id="hung_art_form"></div>

        <div class='UI-center'>
            <button class='UI-eventbutton' onclick=
            'artPage.savePiece();'>Save</button> <button class=
            'UI-redbutton' onclick='hideSidebar();'>Close</button>
            <button class='UI-redbutton' onclick=
            'artPage.deletePiece();'>Remove</button>
            <button class='UI-eventbutton' onclick='artPage.pieceBidTag();'>
            Print Bid Tag </button>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="shop_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>Print Shop Art</h2>
        </div>

        <div id="shop_art_form"></div>

        <div class='UI-center'>
            <button class='UI-eventbutton' onclick=
            'artPage.savePrint();'>Save</button> <button class=
            'UI-redbutton' onclick='hideSidebar();'>Close</button>
            <button class='UI-redbutton' onclick=
            'artPage.deletePrint();'>Remove</button>
        </div>
    </div>
</div>

<div id="enter_hung" class="UI-modal">
    <div class="ARTSHOW-modal-content">
        <div class="UI-container">
            <div class="UI-event-sectionbar UI-margin UI-padding">
                Enter Hung Art <span class="ARTSHOW-close-button" onclick=
                "artPage.closeHungArt();">&#215;</span>
            </div>

            <div class="ARTSHOW-modal-table">
                <div class="UI-table-all" id="new_hung_list"></div>
            </div>

            <div class='UI-center'>
                <button class="UI-eventbutton UI-margin" onclick=
                "artPage.submitHungArt();">Submit</button> <button class=
                "UI-redbutton UI-margin" onclick=
                "artPage.closeHungArt();">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="enter_printshop" class="UI-modal">
    <div class="ARTSHOW-modal-content">
        <div class="UI-container">
            <div class="UI-event-sectionbar UI-margin UI-padding">
                Enter Print Shop Art <span class="ARTSHOW-close-button"
                onclick="artPage.closePrintArt();">&#215;</span>
            </div>

            <div class="ARTSHOW-modal-table">
                <div class="UI-table-all" id="new_printshop_list"></div>
            </div>

            <div class="UI-secondary-sectionbar UI-margin UI-padding">
                To enter more, submit this set and re-click &quot;Add Print
                Shop Pieces&quot;
            </div>

            <div class='UI-center'>
                <button class="UI-eventbutton UI-margin" onclick=
                "artPage.submitPrintArt();">Submit</button> <button class=
                "UI-redbutton UI-margin" onclick=
                "artPage.closePrintArt();">Close</button>
            </div>
        </div>
    </div>
</div>
