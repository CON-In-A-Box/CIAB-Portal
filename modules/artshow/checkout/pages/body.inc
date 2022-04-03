<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Customer Checkout</span>
        </div>

        <div v-if="debugMessage" class="UI-container UI-border UI-margin UI-padding">
        {{ debugMessage }}
        </div>

        <div class="UI-full">
          <div class="UI-center">
            <div v-if="configuration.Artshow_LinkBuyers.value != 0" class='UI-bar-item'>
              <lookup-user @handler="lookupUser" partial-match prompt="Purchaser Badge, E-Mail or Full Name"> </lookup-user>
            </div>
            <div v-else class='UI-bar-item'>
              <lookup-buyer @handler=lookupUser> </lookup-buyer>
            </div>
          </div>
        </div>
        &nbsp;
        <won-art-table ref="won">
        </won-art-table>
        &nbsp;
        <div v-if="payments" class="UI-padding">
          <div class="UI-table-all">
            <div v-if="payments" class="UI-table-row">
              <div v-for="c in columns" class="UI-table-cell">{{c.title}}</div>
            </div>
            <div v-for="(p, index) in payments" class="UI-table-row">
              <div v-for="c in columns" class="UI-table-cell">{{p[c.field]}}</div>
            </div>
          </div>
        </div>
        &nbsp;
        <div class="UI-event-sectionbar UI-margin">
            <span>Pieces won:</span>&nbsp;<span>{{wonCount}}</span>&nbsp;&nbsp;
            <span>Total Cost:</span>&nbsp;${{wonCost}}</span>&nbsp;&nbsp;
            <span>Total Owed:</span>&nbsp;${{wonOwed}}</span>
        </div>
        &nbsp;
        <div class="UI-event-sectionbar UI-margin">
            Add Payment
        </div>
        &nbsp;
        <div class="UI-center">
        <div class="UI-bar">
          <label class="UI-padding UI-bar-item UI-margin">Payment Type:</label>
          <div class="UI-bar-item">
            <select v-if="configuration" class="UI-select UI-padding" v-model="paymentType">
              <option v-for="p in configuration.paymenttype">{{ p.payment }}</option>
            </select>
          </div>
          <label class="UI-padding UI-bar-item UI-margin">Payment Amount:</label>
          <input type=number :max="wonOwed" min=1 class="UI-input UI-bar-item UI-padding"
             autocomplete="off"  v-model="paymentAmount">
          <button type="button" class="UI-eventbutton UI-margin" @click="processPayment">
            Process Payment
          </button>
          <button type="button" class="UI-eventbutton UI-margin" @click="showInvoice">
            Get Invoice
          </button>
        </div>
        </div>

    </div>
</div>
