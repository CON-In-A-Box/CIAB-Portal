<script type="text/javascript">
    var sidebarMainDiv = "main_content";

    userProfile.options({
        title: 'Artist Information',
        updateButtonText: '',
        inlineUpdateButton: false,
        panes: ['name'],
        readonly: true
    });
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Art Show Art</span>
        </div>

        <div v-if="debug && debugMessage" class="UI-container UI-border UI-margin UI-padding">
            {{debugMessage}}
        </div>

        <div class="UI-center">
            <div class='UI-bar-item'>
                <lookup-user partial-match @handler="lookup"> </lookup-user>
            </div>
        </div>

        <div class="UI-container">
            <div id="userProfile"></div>

            <div class="UI-container UI-center">
                <button class="UI-eventbutton UI-margin" @click="artistProfile" :disabled="!('id' in artist)">Edit Artist&quot;s Profile</button>
            </div>

            <div class="UI-continer">
                <div class="ARTSHOW-profile-company-div">
                    <div>
                        Company Name
                    </div>

                    <div class="UI-container">
                        <input type="text" class="UI-input" disabled="disabled" placeholder=
                        "Company Name" v-model="artist.company_name">
                    </div>
                </div>
            </div>
        </div>

        <div class="UI-secondary-sectionbar">
            Art In Show
        </div>

        <div class="UI-container UI-margin UI-center">
            <button class="UI-eventbutton"
            @click="generateInventory">Generate an Artist Inventory Report</button>
        </div>

        <div class="UI-container UI-margin">
            <div class="UI-secondary-sectionbar">
                {{ configuration.Artshow_DisplayArtName.value }} in Art Show
            </div>

            <div class="UI-center UI-padding">
                <button class="UI-eventbutton" name="art_button" @click=
                "addHungArt" disabled="disabled">Add {{ configuration.Artshow_DisplayArtName.value }}</button>
                <button class="UI-eventbutton" name="art_button" @click=
                "tagHungArt" disabled="disabled">Print {{ configuration.Artshow_DisplayArtName.value }} Tags</button>

                <div>
                    <hung-art-table ref=tbl
                        :requested-columns="['id', 'name', 'medium', 'edition', 'art_type', 'not_for_sale', 'prices', 'fee', 'location', 'status', 'auction', 'notes']"
                        click-target="$editPiece"
                        selectable
                    >
                    </hung-art-table>
                    <div class="UI-padding">
                      <div class="UI-padding">
                        <i class="w3-left fas fa-level-up-alt fa-flip-horizontal"></i>
                        <input type="edit"  class="UI-edit" id="location" placeholder="<location>"></input>
                        <button class="UI-eventbutton" @click="$refs.tbl.withSelected('$markAsHung')">Mark selected as displayed</button>
                      </div>
                    </div>

                    <div class="UI-secondary-sectionbar">
                        {{ configuration.Artshow_PrintArtName.value }} Pieces
                    </div>

                    <div class="UI-center UI-padding">
                        <button class="UI-eventbutton" name="art_button"
                        @click= "addPrintArt" disabled="disabled">Add {{ configuration.Artshow_PrintArtName.value }}
                        Art</button>

                        <div>
                            <print-art-table ref=prnt
                                click-target="$editPrint"
                                :requested-columns="['id', 'name', 'type', 'quantity', 'price', 'sold', 'status', 'location', 'notes']"
                                selectable>
                            </print-art-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="hung_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>{{ configuration.Artshow_DisplayArtName.value }}</h2>
        </div>

        <art-piece ref=pce
            :requested-fields="['name', 'artist', 'medium', 'edition', 'art_type', 'not_for_sale', 'prices', 'location', 'notes', 'auction', 'charity']"
        >
        </art-piece>

        <div class='UI-center'>
            <button class='UI-eventbutton' @click=
            'savePiece'>Save</button> <button class=
            'UI-redbutton' onclick='hideSidebar();'>Close</button>
            <button class='UI-redbutton' @click=
            'deletePiece'>Remove</button>
            <button class='UI-eventbutton' @click='pieceBidTag'>
            Print Bid Tag </button>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="shop_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>{{ configuration.Artshow_PrintArtName.value }} Art</h2>
        </div>

        <art-print ref=prt
            :requested-fields="['name', 'artist', 'art_type', 'not_for_sale', 'price',  'quantity', 'sold', 'location', 'notes', 'charity']"
        >
        </art-print>

        <div class='UI-center'>
            <button class='UI-eventbutton' @click=
            'savePrint'>Save</button> <button class=
            'UI-redbutton' onclick='hideSidebar();'>Close</button>
            <button class='UI-redbutton' @click=
            'deletePrint'>Remove</button>
        </div>
    </div>

    <div id="enter_hung" class="UI-modal">
        <div class="ARTSHOW-modal-content">
            <div class="UI-container">
                <div class="UI-event-sectionbar UI-margin UI-padding">
                    Enter {{ configuration.Artshow_DisplayArtName.value }}<span class="ARTSHOW-close-button" @click=
                    "closeHungArt">&#215;</span>
                </div>

                <hung-art-entry-table ref=hung_entry_tbl>
                </hung-art-entry-table>

                <div class='UI-center'>
                    <button class="UI-eventbutton UI-margin" @click=
                    "submitHungArt">Submit</button> <button class=
                    "UI-redbutton UI-margin" @click=
                    "closeHungArt">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="enter_printshop" class="UI-modal">
        <div class="ARTSHOW-modal-content">
            <div class="UI-container">
                <div class="UI-event-sectionbar UI-margin UI-padding">
                    Enter {{ configuration.Artshow_PrintArtName.value }} Art <span class="ARTSHOW-close-button"
                    @click="closePrintArt">&#215;</span>
                </div>

                <print-art-entry-table ref=prt_entry_tbl>
                </print-art-entry-table>

                <div class="UI-secondary-sectionbar UI-margin UI-padding">
                    To enter more, submit this set and re-click &quot;Add {{ configuration.Artshow_PrintArtName.value }}
                    Pieces&quot;
                </div>

                <div class='UI-center'>
                    <button class="UI-eventbutton UI-margin" @click=
                    "submitPrintArt">Submit</button> <button class=
                    "UI-redbutton UI-margin" @click=
                    "closePrintArt">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
