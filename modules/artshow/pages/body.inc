<!DOCTYPE html>
<script>
    var sidebarMainDiv = 'main_content';
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
                <div class="UI-container UI-margin UI-center">
                    <button v-if="!artist" class="UI-eventbutton"
                        @click="artistRegister">Register as an Artist</button>
                </div>
                <div v-if="artist" class="UI-container UI-margin">
                    <label class="UI-label UI-padding">Company Name:</label>
                    <limited-text-field
                        class="UI-container UI-margin"
                        :char-limit=configuration.Artshow_ComanyName_Charlimit.value
                        v-model=artist.company_name>
                    </limited-text-field>
                    <div v-if="artist.company_name" class="UI-container UI-margin UI-border">
                        <input type="checkbox" class="UI-checkbox UI-margin" v-model="artist.company_name_on_sheet" true-value="1" false-value="0">
                        <label class="UI-label UI-padding">Use company name on bid tags.</label>
                        <br>
                        <input type="checkbox" class="UI-checkbox UI-margin" v-model="artist.company_name_on_payment" true-value="1" false-value="0" >
                        <label class="UI-label UI-padding">Make payments to company name.</label>
                    </div>
                    <label class="UI-label UI-padding">Artist WebSite:</label>
                    <limited-text-field
                        class="UI-container UI-margin"
                        :char-limit=configuration.Artshow_Website_Charlimit.value
                        v-model=artist.website>
                    </limited-text-field>
                    <div class="UI-container">
                        <input type="radio" class="UI-radio UI-margin" v-model="artist.professional" value="1">
                        <label>Professional</label>
                        <input type="radio" class="UI-radio UI-margin" v-model="artist.professional" value="0">
                        <label>Amateur</label>
                    </div>

                    <div v-show="artist" class="UI-container UI-margin UI-center">
                        <button class="UI-eventbutton"
                            @click="artistUpdateRegister">Update</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="UI-container UI-padding">
            <div class="UI-event-sectionbar">
                <span>{{configuration['event']['name']}}</span> Art Show
            </div>

            <div class="UI-container UI-border UI-padding">

                <div v-show="artist && selfRegistration && !show" class="UI-container UI-center">
                    <button class="UI-eventbutton"
                        @click='eventShowRegister'>Submit Art for this Event</button>
                </div>

                <div v-if="show" class="UI-container UI-margin">
                    <div class="UI-secondary-sectionbar">
                        <span>{{configuration['event']['name']}}</span> Registration
                    </div>
                    <div class="UI-container UI-border UI-padding">
                        <div class="UI-container UI-margin">
                            <div v-if='configuration.Artshow_MailInAllowed.value == "1"'>
                                <input type="checkbox" class="UI-checkbox UI-margin" v-model="show.mail_in" true-value="1" false-value="0">
                                <label class="UI-label UI-padding">I will be mailing art.</label>
                                <div v-if="show.mail_in == '1'" class="UI-container UI-padding UI-margin UI-border">
                                    <label class="UI-label">Please return my artwork via:</label>
                                    <select class="UI-input" v-model="show.return_method">
                                    <option v-for="m in configuration.returnmethod" :key='m.type'>{{m.method}}</option>
                                    </select>
                                </div>
                              </div>
                            <div v-for="q in configuration.registrationquestion" class="UI-padding">
                                <label class="UI-margin"> {{q.text}} </label>
                                <input v-if="q.boolean ==  '1'" type=checkbox class="UI-padding UI-checkbox" v-model="show['custom_question_'+q.id]" true-value="1" false-value="0"></input>
                                <input v-else type=input class="UI-input" v-model="show['custom_question_'+q.id]"></input>
                            </div>

                            <div class="UI-container UI-margin UI-center">
                                <button class="UI-eventbutton"
                                    @click="eventRegister">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button v-show="artist" type="button" class="UI-event-dropdown-bar" onclick="expandSection('event-artshow')">
                    <span>Art In Show</span>
                    <span> [</span><span>{{ totalArtCount }}</span><span> item(s)]</span>
                    <em id="event-artshow_arrow" class="fas fa-caret-down"></em>
                </button>

                <div class="UI-hide UI-padding UI-border" id="event-artshow">

                    <div v-if="closeDate == null" class="UI-center UI-bold UI-margin">Online Art Registartion is closed.<br>Please talk to the Art Show Staff</div>
                    <div v-else class="UI-center UI-bold UI-margin">Online Art Registartion is open until: {{ closeDate }}</div>

                    <div class="UI-secondary-sectionbar">
                        <span>{{ configuration.Artshow_DisplayArtName.value }} Pieces</span>
                    </div>
                    <div class="UI-container UI-border UI-margin">
                        <hung-art-table ref=tbl
                            :requested-columns="['id', 'name', 'medium', 'edition', 'art_type', 'not_for_sale', 'prices', 'fee', 'status']"
                            click-target="$editPiece"
                            @update-art-sold="updateArtSold"
                            @update-fee="(f) => hangingFee = f"
                            @update-commission="updateArtCommission"
                        >
                        </hung-art-table>
                        <div class="UI-container UI-margin UI-center">
                            <button v-show="selfRegistration" class="UI-eventbutton"
                                @click="addHungArt();">Register {{ configuration.Artshow_DisplayArtName.value }}</button>

                            <button v-show="selfRegistration" class="UI-eventbutton"
                                @click="artistHungTags">Preview Bid Tags</button>
                        </div>
                    </div>
                    <div v-show="configuration['Artshow_PrintShop'].value = '1'">
                        <div class="UI-secondary-sectionbar">
                            <span>{{ configuration.Artshow_PrintArtName.value }} Pieces</span>
                        </div>
                        <div class="UI-container UI-border UI-margin">

                            <print-art-table ref=prt_tbl
                                click-target="$editPrint"
                                @update-print-sold="updatePrintSold"
                                @update-commission="updatePrintCommission"
                                >
                            </print-art-table>

                            <div class="UI-container UI-margin UI-center">
                                <button v-show="selfRegistration" class="UI-eventbutton"
                                    @click="addPrintArt();">Add {{ configuration.Artshow_PrintArtName.value }} Pieces</button>
                            </div>

                            <div class="UI-container UI-margin UI-center">
                                <button class="UI-eventbutton"
                                    @click="generateInventory">Generate an Art Inventory</button>
                            </div>
                        </div>
                    </div>
                    <div class="UI-margin">
                        <div class="UI-event-sectionbar">
                            <span>{{ configuration.Artshow_DisplayArtName.value }} Count:</span>&nbsp;<span>{{ hungCount }}</span>&nbsp;&nbsp;
                            <span>{{ configuration.Artshow_PrintArtName.value }} Count:</span>&nbsp;<span>{{ printCount }}</span>&nbsp;&nbsp;
                            <span>Pieces Sold:</span>&nbsp;<span>{{ itemsSold }}</span>&nbsp;&nbsp;
                        </div>
                        <div class="UI-event-sectionbar">
                            <span>Gross Income:</span>&nbsp;$<span>{{ soldValue }}</span>&nbsp;&nbsp;
                            <span>Hanging Fees:</span>&nbsp;<span :class="{ 'UI-red': hangingFee > 0}">${{ hangingFee }}</span>&nbsp;&nbsp;
                            <span>Commission:</span>&nbsp;<span :class="{ 'UI-red': commission > 0}">$<span>{{ commission }}</span></span>&nbsp;&nbsp;
                            <span>Net Income:</span>&nbsp;$<span>{{ netIncome }}</span>
                        </div>
                    </div>
                </div>
                <br>
                <button type="button" class="UI-event-dropdown-bar" onclick="expandSection('artshow_won_div')">
                    <span>Pieces Won From Art Show</span>
                    <span> [ </span><span>{{wonCount}}</span><span> / </span>
                    <span></span><span>${{wonOwed}}</span><span> ]</span>
                    <em id="artshow_won_div_arrow" class="fas fa-caret-down"></em>
                </button>
                <div class="UI-container UI-border UI-hide" id="artshow_won_div">
                    &nbsp;
                    <won-art-table ref="won">
                    </won-art-table>
                    &nbsp;
                    <div v-if="payments" class="UI-padding">
                      <div class="UI-table-all">
                        <div v-if="payments" class="UI-table-row">
                          <div class="UI-table-cell">Payment Date</div>
                          <div class="UI-table-cell">Payment Type</div>
                          <div class="UI-table-cell">Amount</div>
                        </div>
                        <div v-for="(p, index) in payments" class="UI-table-row">
                          <div class="UI-table-cell">{{ p['date'] }}</div>
                          <div class="UI-table-cell">{{ p['payment_type'] }}</div>
                          <div class="UI-table-cell">{{ p['amount'] }}</div>
                        </div>
                      </div>
                    </div>
                    &nbsp;
                    <div class="UI-event-sectionbar UI-margin">
                        <span>Pieces won:</span>&nbsp;<span>{{wonCount}}</span>&nbsp;&nbsp;
                        <span>Total Cost:</span>&nbsp;${{wonCost}}</span>&nbsp;&nbsp;
                        <span>Total Owed:</span>&nbsp;${{wonOwed}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="hung_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>{{ configuration.Artshow_DisplayArtName.value }}</h2>
        </div>

        <art-piece ref=pce>
        </art-piece>

        <div class='UI-center'>
            <button class='UI-eventbutton edit_enabled'
                @click='savePiece'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
            <button class='UI-redbutton edit_enabled'
                @click='deletePiece'>
              Remove
            </button>
            <button class='UI-eventbutton edit_enabled'
                @click='pieceBidTag();'>
              Bid Tag
            </button>
        </div>
    </div>

    <div class="UI-sidebar-hidden UI-fixed" id="shop_art">
        <div class='UI-center'>
            <h2 class='UI-event-sectionbar'>{{ configuration.Artshow_PrintArtName.value }} Art</h2>
        </div>

        <art-print ref=pnt>
        </art-print>

        <div class='UI-center'>
            <button class='UI-eventbutton edit_enabled'
                @click='savePrint'>
              Save
            </button>
            <button class='UI-redbutton'
                onclick='hideSidebar();'>
              Close
            </button>
            <button class='UI-redbutton edit_enabled'
                @click='deletePrint'>
              Remove
            </button>
        </div>
    </div>

    <div id="enter_hung" class="UI-modal">
      <div class="ARTSHOW-modal-content">
        <div class="UI-container">
          <div class="UI-event-sectionbar UI-margin UI-padding">
             Enter {{ configuration.Artshow_DisplayArtName.value }}
             <span class="ARTSHOW-close-button" @click="closeHungArt">&times;</span>
          </div>

          <hung-art-entry-table ref=hung_entry_tbl>
          </hung-art-entry-table>

          <div class='UI-center'>
            <button class="UI-eventbutton UI-margin" @click="submitHungArt">Submit</button>
            <button class="UI-redbutton UI-margin" @click="closeHungArt">Close</button>
          </div>
        </div>
      </div>
    </div>


    <div id="enter_printshop" class="UI-modal">
      <div class="ARTSHOW-modal-content">
        <div class="UI-container">
          <div class="UI-event-sectionbar UI-margin UI-padding">
             Enter {{ configuration.Artshow_PrintArtName.value }} Art
             <span class="ARTSHOW-close-button" @click="closePrintArt">&times;</span>
          </div>
          <div class="ARTSHOW-modal-table">
            <print-art-entry-table ref=prt_entry_tbl>
            </print-art-entry-table>
          </div>
          <div class="UI-secondary-sectionbar UI-margin UI-padding">
          To enter more, submit this set and re-click 'Add {{ configuration.Artshow_PrintArtName.value }} Pieces'
          </div>
          <div class='UI-center'>
            <button class="UI-eventbutton UI-margin" @click="submitPrintArt">Submit</button>
            <button class="UI-redbutton UI-margin" @click="closePrintArt">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div id="bidsheet" class="UI-modal">
      <div class="ARTSHOW-modal-content">
        <div class="UI-container">
          <div class="UI-event-sectionbar UI-margin UI-padding">
             Sample Bid Sheet
             <span class="ARTSHOW-close-button" @click="closeBidSheet">&times;</span>
          </div>
          <div id="bidsheet_content">
          </div>
          <div class='UI-center'>
            <button class="UI-redbutton UI-margin" @click="closeBidSheet">Close</button>
          </div>
        </div>
      </div>
    <div>

</div>
