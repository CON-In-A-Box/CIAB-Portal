<script type="text/javascript">
    var sidebarMainDiv = "main_content";
</script>

<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Art Show Sales</span>
        </div>

        <div id="headline_section" class= "UI-container UI-border UI-margin UI-padding UI-hide">
        </div>
        &nbsp;

      <div class="UI-container">
          <div class="UI-center">
            <div v-if="configuration.Artshow_LinkBuyers.value != 0" class='UI-bar-item'>
              <lookup-user @handler="lookupUser" partial-match prompt="Purchaser Badge, E-Mail or Full Name"> </lookup-user>
            </div>
            <div v-else class='UI-bar-item'>
              <lookup-buyer @handler=lookupUser add-new> </lookup-buyer>
            </div>
          </div>
        </div>

        <div class="UI-event-sectionbar">
            <span>{{ configuration.Artshow_DisplayArtName.value }} Sales</span>
        </div>
        &nbsp;
        <div class="UI-padding">
          <div v-if="piece" class="UI-event-sectionbar">
              <span>Record '</span>{{prices[salesTypeIndex].price}}'</span> Sale</span>
          </div>


            <div class="UI-center">
                <lookup-piece @handler=lookupPiece prompt="Sold Piece ID"> </lookup-piece>
            </div>
          </div>
          <div v-show="piece">
          <div class="UI-border">
            <piece-info ref=pce read-only
                :requested-fields="['name', 'artist', 'prices']"
            ></piece-info>
          </div>

          <div v-show="piece" class="UI-padding UI-center">
            <span>Sales Type&nbsp;</span>
            <select class="UI-select" v-model="salesTypeIndex" @change="typeChanged" :disabled="artSold">
                <option v-for="(p,i) in prices" :value=i>{{p.price}}</option>
            </select>
          </div>


          <div class="UI-center UI-padding">
            <span>Final Price: </span>
            <input type=number class="UI-edit" v-model="salePrice" @change="checkEnableSale" :disabled="artSold || priceLocked()"></input>
            &nbsp;&nbsp
            <span v-show="artSold" class="UI-red UI-large">Sold to buyer ID {{buyer}}</span>
          </div>

          <div class="UI-center UI-padding">
            <button class="UI-eventbutton" @click="doSale" :disabled="!saleReady">Record Purchase</button>
          </div>
          </div>
       </div>

        &nbsp;
        <div class="UI-event-sectionbar">
            <span>{{ configuration.Artshow_PrintArtName.value }} Sales</span>
        </div>
        &nbsp;
        <div class="UI-padding">
          <div v-if="print" class="UI-event-sectionbar">
              <span>Record {{ configuration.Artshow_PrintArtName.value }} Sale</span>
          </div>

          <div class="UI-container">
            <div class="UI-center">
                <lookup-print @handler=lookupPrint prompt="Sold Item ID"> </lookup-print>
            </div>
          </div>
          <div v-show="print">
          <div class="UI-border">
            <print-info ref=pnt read-only
                :requested-fields="['name', 'artist', 'price', 'quantity', 'sold']"
            ></print-info>
          </div>

          <div class="UI-center UI-padding">
            <span>Quantity Sold: </span>
            <input type=number class="UI-edit" v-model="printQuantity" @change="checkPrintQuantity"></input>
            <span v-show="printSold" class="UI-red UI-large">Sold Out</span>
            &nbsp;
            <span>Final Price: </span>
            <input type=number class="UI-edit" v-model="printPrice" disabled></input>
            <span v-show="printSold" class="UI-red UI-large">Sold Out</span>
          </div>

          <div class="UI-center UI-padding">
            <button class="UI-eventbutton" @click="doPrintSale" :disabled="!printSaleReady">Record Purchase</button>
          </div>
          <div class="UI-center UI-padding">
            <button class="UI-eventbutton" @click="proceedToCheckout">Proceed to Checkout</button>
          </div>
          </div>
       </div>

    </div>
</div>
