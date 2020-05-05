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
                    <select class="" id="PaymentType" onchange="artshowAdminPage.paymentChange();">
                        <option value="">&lt;New&gt;</option>
                    </select>
                    <input class="" id="new_PaymentType" value="">
                     <button class="UI-eventbutton" onclick="artshowAdminPage.updatePayment();">&plus;</button>
                     <button class="UI-eventbutton" onclick="artshowAdminPage.removePayment();">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Art Piece Types</span>
                </div>
                <div class="UI-padding">
                    <select class="" id="PieceType" onchange="artshowAdminPage.pieceChange();">
                        <option value="">&lt;New&gt;</option>
                    </select>
                    <input class="" id="new_PieceType" value="">
                     <button class="UI-eventbutton" onclick="artshowAdminPage.updatePiece();">&plus;</button>
                     <button class="UI-eventbutton" onclick="artshowAdminPage.removePiece();">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Return Methods</span>
                </div>
                <div class="UI-padding">
                    <select class="" id="ReturnMethod" onchange="artshowAdminPage.returnChange();">
                        <option value="">&lt;New&gt;</option>
                    </select>
                    <input class="" id="new_ReturnMethod" value="">
                     <button class="UI-eventbutton" onclick="artshowAdminPage.updateReturn();">&plus;</button>
                     <button class="UI-eventbutton" onclick="artshowAdminPage.removeReturn();">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Display Art Price Types</span>
                </div>
                <div class="UI-padding">
                    <select class="" id="PriceType" onchange="artshowAdminPage.priceChange();">
                        <option value="">&lt;New&gt;</option>
                    </select>
                    <input class="" id="new_PriceType" value="">
                    <span>Price Set By Artist and Appears on Bid Sheet:</span>
                    <input type="checkbox" class="UI-checkbox" id="PriceUser">
                     <button class="UI-eventbutton" onclick="artshowAdminPage.updatePrice();">&plus;</button>
                     <button class="UI-eventbutton" onclick="artshowAdminPage.removePrice();">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Artist Event Registration Questions</span>
                </div>
                <div class="UI-padding">
                    <select class="UI-select" id="RegistrationQuestion" onchange="artshowAdminPage.questionChange();">
                        <option value="">&lt;New&gt;</option>
                    </select>
                    <input class="UI-input" id="new_RegistrationQuestion" value="">
                    <span>(Yes/No) Question (aka. checkbox):</span>
                    <input type="checkbox" class="UI-checkbox" id="QuestionIsBoolean">
                     <br>
                     <button class="UI-eventbutton" onclick="artshowAdminPage.updateRegistrationQuestion();">&plus;</button>
                     <button class="UI-eventbutton" onclick="artshowAdminPage.removeRegistrationQuestion();">&minus;</button>
                </div>
            </div>

            <div class="UI-padding">
                <div class="UI-secondary-sectionbar">
                    <span>Artist Event Bid Tags</span>
                </div>

                <div class="UI-padding">
                    <button class="UI-eventbutton" onclick="artshowAdminPage.demoBidTag();">Demo Tag</button>
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
