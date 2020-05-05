<!DOCTYPE html>
<script>
    var sidebarMainDiv = 'main_content';

    hungArtTable.options({
        onclick: 'artshowPage.editPiece',
    });

    printArtTable.options({
        onclick: 'artshowPage.editPrint',
    });
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Art Show</span>
        </div>
        <div id="headline_section" class="UI-container UI-border UI-margin UI-padding UI-hide">
        </div>
        <div class="UI-container UI-padding">
            <div class="UI-secondary-sectionbar">
                <span>Artist Registration</span>
            </div>
            <div class="UI-container UI-border UI-padding">
                <div class="UI-container UI-margin UI-center UI-hide" id="register_div">
                    <button class="UI-eventbutton"
                        onclick="artshowPage.artistShowRegister()">Register as an Artist</button>
                </div>
                <div class="UI-container UI-margin UI-hide" id="artist_info_div">
                    <label class="UI-label UI-padding" for="company">Company Name:</label>
                    <input class="UI-input UI-margin" id="company" value='' placeholder="<none>" onchange="artshowPage.updateCompanyName();" onkeypress="artshowPage.updateCompanyName();" onpaste="artshowPage.updateCompanyName();" oninput="artshowPage.updateCompanyName();">
                    <div class="UI-container UI-margin UI-border UI-hide" id="artist_company">
                        <input type="checkbox" class="UI-checkbox UI-margin" id="use_company" name="use_company">
                        <label class="UI-label UI-padding" for="use_company">Use company name on bid tags.</label>
                        <br>
                        <input type="checkbox" class="UI-checkbox UI-margin" id="use_company_check" name="use_company_check">
                        <label class="UI-label UI-padding" for="use_company_check">Make payments to company name.</label>
                    </div>
                    <label class="UI-label UI-padding" for="website">Artist WebSite:</label>
                    <input class="UI-input UI-margin" id="website" value='' placeholder="<none>">
                    <input type="radio" class="UI-radio UI-margin" id="professional" name="status"
                     value="professional">
                    <label for="professional">Professional</label>
                    <input type="radio" class="UI-radio UI-margin" id="amateur" name="status"
                     value="amateur" checked>
                    <label for="amateur">Amateur</label>


                    <div class="UI-container UI-margin UI-center UI-hide" id="register_button">
                        <button class="UI-eventbutton"
                            onclick="artshowPage.artistRegister();">Register</button>
                    </div>
                    <div class="UI-container UI-margin UI-center UI-hide" id="update_button">
                        <button class="UI-eventbutton"
                            onclick="artshowPage.artistUpdateRegister();">Update</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="UI-container UI-padding">
            <div class="UI-event-sectionbar">
                <span id="current_event">Event</span> Art Show
            </div>

            <div class="UI-container UI-border UI-padding">

                <div class="UI-container UI-center UI-hide" id="event_register_div">
                    <button class="UI-eventbutton"
                        onclick='artshowPage.eventShowRegister()'>Submit Art for this Event</button>
                </div>

                <div class="UI-container UI-margin UI-hide" id="event_register_info_div">
                    <div class="UI-secondary-sectionbar">
                        <span>Event Registration</span>
                    </div>
                    <div class="UI-container UI-border UI-padding">
                        <div class="UI-container UI-margin" id="artist_event_info_div">
                            <input type="checkbox" class="UI-checkbox UI-margin" id="reg_mailin" name="reg_mailin" onclick="artshowPage.clickMailIn();">
                            <label class="UI-label UI-padding" for="reg_mailin">I will be mailing art.</label>
                            <div class="UI-container UI-padding UI-margin UI-border UI-hide" id="mailing_info">
                                <label for="reg-return-type" class="UI-label">Please return my artwork via:</label>
                                <select name="reg-return-type" id="reg-return-type" class="UI-input">
                                </select>
                            </div>
                            <div id="artist_event_questions">
                            </div>

                            <div class="UI-container UI-margin UI-center UI-hide UI-show" id="event_register_button">
                                <button class="UI-eventbutton"
                                    onclick="artshowPage.eventRegister('POST');">Register</button>
                            </div>
                            <div class="UI-container UI-margin UI-center UI-hide" id="event_update_button">
                                <button class="UI-eventbutton"
                                    onclick="artshowPage.eventRegister('PUT');">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="UI-event-dropdown-bar UI-hide" onclick="expandSection('event-artshow')" id="art_in_show">
                    <span>Art In Show</span>
                    <span> [</span><span id="art_count">0</span><span> item(s)]</span>
                    <em id="event-artshow_arrow" class="fas fa-caret-down"></em>
                </button>

                <div class="UI-hide UI-padding UI-border" id="event-artshow">
                    <div class="UI-secondary-sectionbar">
                        <span>Pieces Hung in Art Show</span>
                    </div>
                    <div class="UI-container UI-border UI-margin">
                        <div id="artshow_entered">
                        </div>
                        <div class="UI-container UI-margin UI-center">
                            <button id="add_hung_art" class="UI-eventbutton"
                                onclick="artshowPage.addHungArt();">Register Hung Art</button>

                            <button id="artist_tags" class="UI-eventbutton"
                                onclick="artshowPage.artistHungTags();">Preview Bid Tags</button>
                        </div>
                    </div>
                    <div id="print_shop_div" class="UI-hide">
                        <div class="UI-secondary-sectionbar">
                            <span>Pieces in Art Show Print Shop</span>
                        </div>
                        <div class="UI-container UI-border UI-margin">
                            <div id="artshow_printshop">
                            </div>
                            <div class="UI-container UI-margin UI-center">
                                <button id="add_print_art" class="UI-eventbutton"
                                    onclick="artshowPage.addPrintArt();">Add Print Shop Pieces</button>
                            </div>
                        </div>
                    </div>
                    <div class="UI-margin">
                        <div class="UI-event-sectionbar">
                            <span>Pieces hung:</span>&nbsp;<span id="pieces_hung">0</span>&nbsp;&nbsp;
                            <span>Print Shop Count:</span>&nbsp;<span id="pieces_printshop">0</span>&nbsp;&nbsp;
                            <span>Pieces Sold:</span>&nbsp;<span id="pieces_sold">0</span>&nbsp;&nbsp;
                        </div>
                        <div class="UI-event-sectionbar">
                            <span>Gross Income:</span>&nbsp;$<span id="gross_income">0</span>
                            <span>Hanging Fees:</span>&nbsp;<span id="fee_span" class="">$<span id="fees">0</span></span>
                            <span>Commission:</span>&nbsp;<span id="comission_span" class="">$<span id="commission">0</span></span>
                            <span>Net Income:</span>&nbsp;$<span id="net_income">0</span>
                        </div>
                    </div>
                </div>
                <br>
                <button type="button" class="UI-event-dropdown-bar" onclick="expandSection('artshow_won_div')">
                    <span>Pieces Won From Art Show</span>
                    <span> [ </span><span id="won_count">0</span><span> / </span>
                    <span>$</span><span id="won_owed">0</span><span> ]</span>
                    <em id="artshow_won_div_arrow" class="fas fa-caret-down"></em>
                </button>
                <div class="UI-container UI-border UI-hide" id="artshow_won_div">
                    <br>
                    <div class="UI-table-all UI-padding" id="artshow_won">
                        <div class="UI-table-row">
                            <div class="UI-table-cell">
                                Piece Name
                            </div>
                            <div class="UI-table-cell">
                                Piece Artist
                            </div>
                            <div class="UI-table-cell">
                                Sale Type
                            </div>
                            <div class="UI-table-cell">
                                Winning Price
                            </div>
                            <div class="UI-table-cell">
                               Status
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="UI-event-sectionbar UI-margin">
                        <span>Pieces won:</span>&nbsp;<span id="pieces_won">0</span>&nbsp;&nbsp;
                        <span>Total Cost:</span>&nbsp;<span id="total_cost">$0</span>&nbsp;&nbsp;
                        <span>Total Owed:</span>&nbsp;<span id="total_owed">$0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="hung_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>Hung Art</h2>
        </div>
        <div id="hung_art_form">
        </div>
        <div class='UI-center'>
            <button class='UI-eventbutton'
                onclick='artshowPage.savePiece();'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
            <button class='UI-redbutton'
                onclick='artshowPage.deletePiece();'>
              Remove
            </button>
            <button class='UI-eventbutton'
                onclick='artshowPage.pieceBidTag();'>
              Bid Tag
            </button>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="shop_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>Print Shop Art</h2>
        </div>
        <div id="shop_art_form">
        </div>
        <div class='UI-center'>
            <button class='UI-eventbutton'
                onclick='artshowPage.savePrint();'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
            <button class='UI-redbutton'
                onclick='artshowPage.deletePrint();'>
              Remove
            </button>
        </div>
    </div>
</div>

<div id="enter_hung" class="UI-modal">
  <div class="ARTSHOW-modal-content">
    <div class="UI-container">
      <div class="UI-event-sectionbar UI-margin UI-padding">
         Enter Hung Art
         <span class="ARTSHOW-close-button" onclick="artshowPage.closeHungArt();">&times;</span>
      </div>
      <div class="ARTSHOW-modal-table">
          <div class="UI-table-all" id="new_hung_list">
          </div>
      </div>
      <div class='UI-center'>
        <button class="UI-eventbutton UI-margin" onclick="artshowPage.submitHungArt();">Submit</button>
        <button class="UI-redbutton UI-margin" onclick="artshowPage.closeHungArt();">Close</button>
      </div>
    </div>
  </div>
</div>


<div id="enter_printshop" class="UI-modal">
  <div class="ARTSHOW-modal-content">
    <div class="UI-container">
      <div class="UI-event-sectionbar UI-margin UI-padding">
         Enter Print Shop Art
         <span class="ARTSHOW-close-button" onclick="artshowPage.closePrintArt();">&times;</span>
      </div>
      <div class="ARTSHOW-modal-table">
          <div class="UI-table-all" id="new_printshop_list">
          </div>
      </div>
      <div class="UI-secondary-sectionbar UI-margin UI-padding">
      To enter more, submit this set and re-click 'Add Print Shop Pieces'
      </div>
      <div class='UI-center'>
        <button class="UI-eventbutton UI-margin" onclick="artshowPage.submitPrintArt();">Submit</button>
        <button class="UI-redbutton UI-margin" onclick="artshowPage.closePrintArt();">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="bidsheet" class="UI-modal">
  <div class="ARTSHOW-modal-content">
    <div class="UI-container">
      <div class="UI-event-sectionbar UI-margin UI-padding">
         Sample Bid Sheet
         <span class="ARTSHOW-close-button" onclick="artshowPage.closeBidSheet();">&times;</span>
      </div>
      <div id="bidsheet_content">
      </div>
      <div class='UI-center'>
        <button class="UI-redbutton UI-margin" onclick="artshowPage.closeBidSheet();">Close</button>
      </div>
    </div>
  </div>
<div>
