<script>
    var sidebarMainDiv = 'main_content';
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Art Show Admin</span>
        </div>
        &nbsp;
        <div v-if="debug && debugmsg" class= "UI-container UI-border UI-margin UI-padding"></div>
        &nbsp;
        <div class="UI-padding UI-center UI-border">
            <div class="UI-secondary-sectionbar">
            Sale Statistics
            </div>
            Artists: <b>{{ Number(stats['event_artist_count']).toLocaleString() }}</b><br>
            {{ configuration.Artshow_DisplayArtName.value }}: <b>{{ Number(stats['event_hung_count']).toLocaleString() }}</b> pieces<br>
            {{ configuration.Artshow_DisplayArtName.value }} sales: <b>${{ Number(stats['event_hung_sale_total']).toLocaleString() }}</b> in <b>{{ Number(stats['event_hung_sale_count']).toLocaleString() }}</b> sales<br>
            {{ configuration.Artshow_PrintArtName.value }}: <b>{{ Number(stats['event_print_count']).toLocaleString() }}</b> lots<br>
            {{ configuration.Artshow_PrintArtName.value }} sales: <b>${{ Number(stats['event_print_sale_total']).toLocaleString() }}</b> in <b>{{ Number(stats['event_print_sale_count']).toLocaleString() }}</b> sales
        </div>
        <div class="UI-center">
            <button class="UI-eventbutton UI-margin" @click="printTags">Print Unprinted Art Tags</button>
            <input type=number min=1 class="UI-edit" v-model="tagPrintCount"></input>
            &nbsp;
            <span>of {{stats['unprinted_tag_count']}} unprinted tags</span>
        <div>
        &nbsp;
        <div class="UI-center">
          <lookup-piece @handler="lookupPiece"> </lookup-piece>
        </div>
        &nbsp;
        <div v-show="piece">
            <art-piece ref=pce :requested-fields="['all']">
            </art-piece>

            <div class="UI-center">
                <button class="UI-eventbutton UI-margin" @click="savePiece">Save</button>
                <button class="UI-eventbutton UI-margin" @click="bidTag">Print Bid Tag </button>
            </div>

        </div>
        <hr>
        <div class="UI-center">
          <div class="UI-bar">
            <button class="UI-quarter UI-eventbutton UI-margin UI-bar-item" @click="auctionReport">Generate Auction List</button>
            <button class="UI-quarter UI-eventbutton UI-margin UI-bar-item" @click="saleReport">Generate Sales Report</button>
            <select class="UI-half UI-margin UI-bar-item UI-select" v-model="reportType">
                <option>json</option>
                <option>csv</option>
            </select>
          </div>
        </div>

        <hr>
        <div class="UI-center">
          <div class="UI-bar">
              <div class='UI-bar-item'>
                  <lookup-user partial-match prompt="Artist to find Invoice for" @handler="lookupUser"> </lookup-user>
              </div>
          </div>
        </div>


    </div>
</div>
