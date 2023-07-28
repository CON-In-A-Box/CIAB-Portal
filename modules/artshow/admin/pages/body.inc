<div id="page" class="UI-container">
    <div id="main_content" class="UI-maincontent">
        <div class="UI-event-sectionbar">
            <span>Art Show Admin</span>
        </div>
        <div id="headline_section" class="UI-container UI-border UI-margin UI-padding UI-hide">
        </div>
        <div class="UI-container UI-padding">
            <div class="UI-secondary-sectionbar">
                <span>Art Show Settings</span>
            </div>

            <div class="UI-border UI-padding UI-margin UI-container">
                <div id="settings" class="UI-table-all">
                    <div class="UI-table-row">
                        <div class="UI-table-cell UI-center">
                            <span class="UI-center">Nothing to Set...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Accepted Payment Types</span>
                </div>
                <div class="UI-padding">
                    <select class="" id="PaymentType" @change="paymentChange">
                        <option value="">&lt;New&gt;</option>
                        <option v-for="m in configuration.paymenttype">{{m.payment}}</option>
                    </select>
                    <input class="" id="new_PaymentType" value="">
                     <button class="UI-eventbutton" @click="updatePayment">&plus;</button>
                     <button class="UI-eventbutton" @click="removePayment">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Art Piece Types</span>
                </div>
                <div class="UI-padding">
                    <select class="" id="PieceType" @change="pieceChange">
                        <option value="">&lt;New&gt;</option>
                        <option v-for="m in configuration.piecetype">{{m.piece}}</option>
                    </select>
                    <input class="" id="new_PieceType" value="">
                     <button class="UI-eventbutton" @click="updatePiece">&plus;</button>
                     <button class="UI-eventbutton" @click="removePiece">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Return Methods</span>
                </div>
                <div class="UI-padding">
                    <select class="" id="ReturnMethod" @change="returnChange">
                        <option value="">&lt;New&gt;</option>
                        <option v-for="m in configuration.returnmethod">{{m.method}}</option>
                    </select>
                    <input class="" id="new_ReturnMethod" value="">
                     <button class="UI-eventbutton" @click="updateReturn">&plus;</button>
                     <button class="UI-eventbutton" @click="removeReturn">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Display Art Price Types</span>
                </div>
                <div class="UI-padding">
                    <select class="" id="PriceType" @change="priceChange">
                        <option value="">&lt;New&gt;</option>
                        <option v-for="m in configuration.pricetype" :value="JSON.stringify(m)">{{m.price}}</option>
                    </select>
                    <input class="" id="new_PriceType" value="">
                    <span>Price Set By Artist and Appears on Bid Sheet:</span>
                    <input type="checkbox" class="UI-checkbox" id="PriceUser">
                    <span>If sold this way the sale will always be for this price:</span>
                    <input type="checkbox" class="UI-checkbox" id="PriceFixed">
                     <button class="UI-eventbutton" @click="updatePrice">&plus;</button>
                     <button class="UI-eventbutton" @click="removePrice">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Artist Event Registration Questions</span>
                </div>
                <div class="UI-padding">
                    <select class="UI-select" id="RegistrationQuestion" @change="questionChange">
                        <option value="">&lt;New&gt;</option>
                        <option v-for="m in configuration.registrationquestion" :value="JSON.stringify(m)">{{m.text}}</option>
                    </select>
                    <input class="UI-input" id="new_RegistrationQuestion" value="">
                    <span>(Yes/No) Question (aka. checkbox):</span>
                    <input type="checkbox" class="UI-checkbox" id="QuestionIsBoolean">
                     <br>
                     <button class="UI-eventbutton" @click="updateRegistrationQuestion">&plus;</button>
                     <button class="UI-eventbutton" @click="removeRegistrationQuestion">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Artist Event Bid Tags</span>
                </div>

                <div class="UI-padding">
                    <button class="UI-eventbutton" @click="demoBidTag">Demo Tag</button>
                </div>
            </div>

        </div>

        <div class="UI-container UI-padding">
            <div class="UI-secondary-sectionbar">
                <span>&nbsp;</span>
            </div>
            <div class="UI-padding">
            </div>
        <div>
    </div>
</div>
