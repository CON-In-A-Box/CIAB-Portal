<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div id="left_side" class="ARTSHOW-left-bar">
            <div class='UI-bar-item'>
                <lookup-user partial-match @handler="lookup"> </lookup-user>
            </div>

            <select v-if="artists" class="ARTSHOW-artist-list"  size=50 @change="selectArtist" v-model="selectedArtist">
                <option v-for="(a, i) in artists" :value="i"> {{generateArtistName(a)}} </option>
            </select>
            <div class="UI-center">
                <button class="UI-eventbutton UI-margin" id="new_artist_button"
                    @click="newArtist(true);">New Artist</button>
                <button class="UI-redbutton UI-margin" :disabled="!modified" @click="saveArtist">Save</button>
            </div>
        </div>

    <div id="right_side" class="ARTSHOW-main-page">
        <div class="UI-event-sectionbar">
            <span>Art Show Artists</span>
        </div>

        <div v-if="debug && debugMessage" class="UI-container UI-border UI-margin UI-padding">
            {{debugMessage}}
        </div>

        <div>
            <div v-if="'hung_count' in stats" class="UI-container UI-center UI-border">
                {{ configuration.Artshow_DisplayArtName.value }}: <b>{{ Number(stats['hung_count']).toLocaleString() }}</b> pieces: 
                sold: <b>${{ Number(stats['hung_sale_total']).toLocaleString() }}</b>
                    in <b>{{ Number(stats['hung_sale_count']).toLocaleString() }}</b> sales
                    commission: <b>${{ parseFloat(stats['hung_commission']).toLocaleString() }}</b>
                fees: <b>${{ Number(stats['hung_hanging_fee']).toLocaleString() }}</b>
                net: <b>${{ parseFloat(stats['hung_sales_net']).toLocaleString() }}</b><br>
                {{ configuration.Artshow_PrintArtName.value }}: <b>{{ Number(stats['print_lot_count']).toLocaleString() }}</b> lots: 
                sold: <b>${{ Number(stats['print_sale_total']).toLocaleString() }}</b>
                    in <b>{{ Number(stats['print_sale_count']).toLocaleString() }}</b> sales
                    commission: <b>${{ parseFloat(stats['print_commission']).toLocaleString() }}</b>
                    net: <b>${{ parseFloat(stats['print_sales_net']).toLocaleString() }}</b><br>
                Sales Total: <b>${{ (parseFloat(stats['hung_sales_net']) + parseFloat(stats['print_sales_net'])).toLocaleString() }}</b>
                Payments: <b>${{ parseFloat(stats['distribution_total']).toLocaleString() }}</b>
                Balance: <b>${{ (parseFloat(stats['hung_sales_net'])+parseFloat(stats['print_sales_net']) - parseFloat(stats['distribution_total'])).toLocaleString() }}</b>
            </div>

        <div class="UI-container UI-center">
            <button class="UI-eventbutton UI-margin" @click="artistArt" :disabled="!('id' in artist)">Artist's Art</button>
            <button class="UI-eventbutton UI-margin" @click="artistInventory" :disabled="!('id' in artist)">Artist's check-in/out Inventory</button>
            <button class="UI-eventbutton UI-margin" @click="artistInvoice" :disabled="!('id' in artist)">Artist's Invoice</button>
            <button class="UI-eventbutton UI-margin" @click="artistDistribution" :disabled="!('id' in artist)">Artist Payment</button>
        </div>

        <div id="userProfile">
        </div>

        <p></p>

        <div class="ARTSHOW-profile-company-div">
            <div>
              Company Name
            </div>
            <div class="UI-container">
                <div class="ARTSHOW-profile-white-div">
                    <limited-text-field
                        class="UI-container UI-margin"
                        :char-limit=configuration.Artshow_ComanyName_Charlimit.value
                        v-model="artist.company_name"
                        @change="artistDataChange"
                        >
                    </limited-text-field>
                    <div class="UI-container UI-border UI-padding" id="artist_company">
                        <input type="checkbox" class="UI-checkbox" v-model="artist.company_name_on_sheet" @change="artistDataChange" true-value="1" false-value="0">
                        <label class="UI-label UI-padding">Use company name on bid tags.</label>
                        <br>
                        <input type="checkbox" class="UI-checkbox" v-model="artist.company_name_on_payment" @change="artistDataChange" true-value="1" false-value="0">
                        <label class="UI-label UI-padding">Make payments to company name.</label>
                    </div>
                </div>
            </div>
        </div>

        <p></p>

        <div class="ARTSHOW-profile-website-div">
            <div>
              WebSite
            </div>
            <div class="UI-container">
              <div class="ARTSHOW-profile-white-div">
                <limited-text-field
                    class="UI-container UI-margin"
                    :char-limit=configuration.Artshow_Website_Charlimit.value
                    v-model="artist.website"
                    @change="artistDataChange">
                </limited-text-field>
              </div>
            </div>
        </div>
        <p></p>
        <div class="ARTSHOW-profile-status-div">
            <div>
               Status
            </div>
            <div class="UI-container">
                <div class="ARTSHOW-profile-white-div">
                    <input type="radio" class="UI-radio UI-margin" v-model="artist.professional" value="1" @change="artistDataChange">
                    <label>Professional</label>
                    <input type="radio" class="UI-radio UI-margin" v-model="artist.professional" value="0" @change="artistDataChange">
                    <label>Amateur</label>
                </div>
            </div>
        </div>
        <p></p>
        <div v-if='configuration.Artshow_MailInAllowed.value == "1"' class="ARTSHOW-mail-div">
            <div>
                Mail In Art
            </div>
            <div class="UI-container">
              <div class="ARTSHOW-profile-white-div">
                <div class="UI-container UI-padding">
                    <input type="checkbox" class="UI-checkbox UI-padding" @change="artistDataChange" v-model="show.mail_in" true-value="1" false-value="0">
                    <label class="UI-label UI-padding">Artist will be mailing art.</label>
                </div>
                <div class="UI-container">
                <label class="UI-label UI-quarter">Return artwork via:</label>
                <select class="UI-input UI-threequarter" v-model="show.return_method" @change="artistDataChange">
                <option v-for="m in configuration.returnmethod" :key='m.type'>{{m.method}}</option>
                </select>
                </div>
              </div>
            </div>
        </div>
        <p></p>
        <div class="ARTSHOW-questions-div">
            <div>
                Artist Questions
            </div>
            <div class="UI-container">
              <div class="ARTSHOW-profile-white-div">
                  <div v-for="q in configuration.registrationquestion" class="UI-padding">
                    <label class="UI-margin"> {{q.text}} </label>
                    <input v-if="q.boolean ==  '1'" type=checkbox class="UI-padding UI-checkbox" v-model="show['custom_question_'+q.id]" true-value="1" false-value="0" @change="artistDataChange"></input>
                    <input v-else type=input class="UI-input" v-model="show['custom_question_'+q.id]" @change="artistDataChange"></input>
                </div>
              </div>
            </div>
         </div>
      </div>
    </div>
    </div>

    <div class="UI-modal" id="pay_artist">
      <div class="ARTSHOW-modal-content">
        <div class="UI-container">
           <div class="UI-event-sectionbar UI-margin UI-padding">
             Artist Distribution
             <span class="ARTSHOW-close-button" @click="closeDistribution">&times;</span>
           </div>

           <div class="UI-padding">
               <div class="UI-padding">
               Total Owed to Artist: ${{ (Number(stats['hung_sale_total']) + Number(stats['print_sale_total']) 
               - Number(stats['hung_commission']) - Number(stats['print_commission'])).toLocaleString() }}
               </div>
               <div class="UI-padding">
               Total Paid to Artist: ${{Number(stats['distribution_total']).toLocaleString()}}
               </div>
               <div class="UI-padding">
               Outstanding Payment: ${{(Number(stats['hung_sale_total']) + Number(stats['print_sale_total'])
               - Number(stats['hung_commission']) - Number(stats['print_commission'])
               - Number(stats['distribution_total'])).toLocaleString() }}
               </div>

               <div v-if="payments" class="UI-padding">
                <div class="UI-table-all">
                  <div class="UI-table-cell"> Date </div>
                  <div class="UI-table-cell"> Check # </div>
                  <div class="UI-table-cell"> Amount </div>
                  <div v-for="p in payments" class="UI-table-row">
                    <div class="UI-table-cell"> {{ p.date }} </div>
                    <div class="UI-table-cell"> {{ p.check_number}} </div>
                    <div class="UI-table-cell"> ${{ p.amount }} </div>
                  </div>
                </div>
             </div>
          </div>

           <div class="UI-center">
              $<input type=number min=1 class="UI-edit" v-model="distributionAmount"></input>
              <span class="UI-padding">Check Number</span>
              <input type=edit class="UI-edit" v-model="distributionCheck"></input>
              <button class= "UI-eventbutton UI-margin" @click="recordDistribution">Record Distribution</button>
              <button class= "UI-redbutton UI-margin" @click="closeDistribution">Close</button>
           </div>
         </div>
      </div>
    </div>

</div>
