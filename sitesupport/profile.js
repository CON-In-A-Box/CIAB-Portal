/*
 * User profile section
 */

/* jshint browser: true */
/* globals apiRequest, showSpinner, hideSpinner, basicBackendRequest,
           alertbox, confirmbox */

var userProfile = (function(options) {
  'use strict';

  const map = [
    ['id', 'badgeNumber'], ['email', 'email1'],
    ['legal_first_name', 'firstName'], ['legal_last_name', 'lastName'],
    ['middle_name', 'middleName'], ['suffix', 'suffix'], ['email2', 'email2'],
    ['email3', 'email3'], ['phone', 'phone1'], ['phone2', 'phone2'],
    ['address_line1', 'addressLine1'], ['address_line2', 'addressLine2'],
    ['city', 'city'], ['state', 'state'], ['zip_code', 'zipCode'],
    ['zip_plus4', 'zipPlus4'], ['country', 'countryName'],
    ['province', 'province'], ['preferred_first_name', 'preferredFirstName'],
    ['preferred_last_name', 'preferredLastName'],
    ['concom_display_phone', 'conComDisplayPhone'],
  ];

  var accountId;

  var settings = Object.assign(
    {
      prefix: 'test',
      urlTag : 'userProfile',
      title: 'Your current profile information',
      updateButtonText: 'Update Profile',
      inlineUpdateButton: true,
      panes: ['name', 'prefName', 'badge', 'emailAll', 'phone', 'addr'],
      onChange: null,
      noTitleBlock: false
    }, options);

  return {
    options: function(opts) {
      settings = Object.assign(settings, opts);
    },

    getElementById: function(id) {
      var realId = id;
      if (settings.prefix) {
        realId = settings.prefix + '_' + id;
      }
      return document.getElementById(realId);
    },

    expandOut: function(val, id) {
      var x = userProfile.getElementById(id);
      if (val === '') {
        if (x.className.indexOf('UI-show') == -1) {
          x.className += ' UI-show';
        }
      } else {
        if (x.className.indexOf('UI-show') != -1) {
          x.className = x.className.replace(' UI-show', '');
        }
      }
    },

    populate: function(member) {
      this.accountId = member['id'];
      map.forEach(function(data) {
        if (userProfile.getElementById(data[1])) {
          var element = userProfile.getElementById(data[1]);
          if (data[0] in member && member[data[0]]) {
            element.value = member[data[0]];
            element['originalValue'] = member[data[0]];
          } else {
            if (userProfile.getElementById(data[1]).nodeName == 'INPUT') {
              element.value = '';
              element['originalValue'] = '';
            }
          }
        }
      });
    },

    clear: function() {
      this.accountId = -1;
      map.forEach(function(data) {
        if (userProfile.getElementById(data[1])) {
          var element = userProfile.getElementById(data[1]);
          element.value = '';
          element['originalValue'] = '';
        }
      });
      userProfile.getElementById('state').value = 'MN';
      userProfile.getElementById('countryName').value =
        'United States of America';
    },

    getMember: function() {
      var member = {};
      map.forEach(function(data) {
        if (userProfile.getElementById(data[1]).value) {
          member[data[0]] = userProfile.getElementById(data[1]).value;
        }
      });
      return member;
    },

    serializeUpdate: function() {
      var output = [];
      map.forEach(function(data) {
        var element = userProfile.getElementById(data[1]);
        if (element) {
          if (element.value != element.originalValue) {
            var v = encodeURI(element.value);
            output.push(data[0] + '=' + v);
          }
        }
      });
      return output.join('&');
    },

    validateRequired: function(field, alerttxt)
    {
      if (field.value === null || field.value === '') {
        alertbox(alerttxt);
        return false;
      } else {
        return true;
      }
    },

    validateEmail: function(field, alerttxt)
    {
      var apos = field.value.indexOf('@');
      var dotpos = field.value.lastIndexOf('.');
      if (apos < 1 || dotpos - apos < 2) {
        alertbox(alerttxt);
        return false;
      } else {
        return true;
      }
    },

    validateForm: function() {
      if (settings.panes.includes('name')) {
        if (userProfile.validateRequired(
          userProfile.getElementById('firstName'),
          'Please enter a legal first name') === false) {
          userProfile.getElementById('firstName').focus();
          return false;
        }
        if (userProfile.validateRequired(userProfile.getElementById('lastName'),
          'Please enter a legal last name') === false) {
          userProfile.getElementById('lastName').focus();
          return false;
        }
      }
      if (settings.panes.includes('emailPrimary') ||
          settings.panes.includes('emailAll')) {
        if (userProfile.validateEmail(userProfile.getElementById('email1'),
          'Must have a valid Primary Email address') === false) {
          userProfile.getElementById('email1').focus();
          return false;
        }
      }
      return true;
    },

    updateAccount: function() {
      if (!userProfile.validateForm()) {
        return;
      }
      var data = userProfile.serializeUpdate();
      if (data.length == 0) {
        alertbox('Nothing updated.');
        return;
      }
      showSpinner();
      var method = 'POST';
      var uri = 'member';
      if (this.accountId > 0) {
        method = 'PUT';
        uri = 'member/' + this.accountId;
      }
      apiRequest(method, uri, data)
        .then(function() {
          if (method == 'POST') {
            alertbox('Account Created, email sent').then(function() {
              window.location = '/index.php?Function=public';
            });
          } else {
            alertbox('Member data updated');
            basicBackendRequest('POST', 'profile', 'reloadProfile=1',
              function() {
                hideSpinner();
                location.reload();
              },
              function() {
                hideSpinner();
                location.reload();
              });
          }
        })
        .catch(function(response) {
          if (response instanceof Error) { throw response; }
          console.log(response);
          hideSpinner();
          var email = userProfile.getElementById('email1').value;
          if (response.status == 409) {
            if (email) {
              alertbox('Account with the email \'' + email +
                         '\' already exists!');
            } else {
              alertbox('Email for account invalid, please retry!');
            }
            userProfile.getElementById('email1').value = '';
          }
          else if (response.status != 200) {
            if (email) {
              alertbox('Account Update Failed.');
            }
          }
        });
    },

    changePassword: function() {
      var current = userProfile.getElementById('currentPassword');
      var newPassword = userProfile.getElementById('newPassword');
      var again = userProfile.getElementById('againPassword');
      var accountId = this.accountId;

      if (!current.value) {
        alertbox('Current Password not supplied');
        return;
      }
      if (!newPassword.value) {
        alertbox('New Password not supplied');
        return;
      }
      if (!again.value || again.value != newPassword.value) {
        alertbox('New Password confirmation does not match');
        return;
      }

      confirmbox('Proceed in changing your password?').then(function() {
        showSpinner();
        apiRequest('PUT', 'member/' + accountId + '/password',
          'NewPassword=' + newPassword.value + '&OldPassword=' + current.value)
          .then(function() {
            hideSpinner();
            alertbox('Password Updated').then(
              function() {
                location.reload();
              }
            );
          })
          .catch(function(response) {
            hideSpinner();
            if (response.status == 403) {
              if (current.value) {
                alertbox('Current Password Incorrect');
              }
              current.value = '';
            }
          });
      },
      function() {
        current.value = '';
        newPassword.value = '';
        again.value = '';
      });
    },

    onChange: function(element) {
      if (settings.onChange) {
        settings.onChange(element);
      }
    },

    build: function() {
      accountId = -1;
      this.accountId = accountId;
      const content = document.getElementById(settings.urlTag);

      var prefix = '';
      if (settings.prefix) {
        prefix = settings.prefix + '_';
      }

      var updateButton = '';
      if (settings.updateButtonText && settings.inlineUpdateButton) {
        updateButton = `
<button class="UI-profile-update-button"
    onclick="userProfile.updateAccount();">${settings.updateButtonText}</button>
`;
      }

      const titleBlock = `
<h3>${settings.title}</h3>
<div name="Profile" id="${prefix}profile_update" class="UI-container">
`;

      const nameBlock = `
<div class="UI-profile-name-div">
  <div>
    Legal Name
    ${updateButton}
  </div>
  <div class="UI-container">
    <div class="UI-half">
      <div class="UI-twothird">
        <input class="UI-input" id="${prefix}firstName" name="firstName"
        placeholder="First (Required)" type="text" value=""
        onchange="userProfile.onChange(this);">
      </div>
      <div class="UI-third">
        <input class="UI-input" id="${prefix}middleName" name="middleName"
        placeholder="Middle" type="text" value=""
        onchange="userProfile.onChange(this);">
      </div>
    </div>
    <div class="UI-half">
      <div class="UI-threequarter">
        <input class="UI-input" id="${prefix}lastName" name="lastName"
        placeholder="Last (Required)" type="text" value=""
        onchange="userProfile.onChange(this);">
      </div>
      <div class="UI-quarter">
        <input class="UI-input" id="${prefix}suffix" name="suffix"
        placeholder="Suffix" type="text" value=""
        onchange="userProfile.onChange(this);">
      </div>
    </div>
  </div>
</div>
`;

      const prefNameBlock = `
<div class="UI-profile-pref-name-div">
  <div>
    Preferred Name
    ${updateButton}
  </div>
  <div class="UI-container">
    <div class="UI-half">
      <input class="UI-input" id="${prefix}preferredFirstName"
      name="preferredFirstName" placeholder="First - If Different" type="text"
      value="" onchange="userProfile.onChange(this);">
    </div>
    <div class="UI-half">
      <input class="UI-input" id="${prefix}preferredLastName"
      name="preferredLastName" placeholder="Last - If Different" type="text"
      value="" onchange="userProfile.onChange(this);">
    </div>
  </div>
</div>
`;

      const badgeBlock = `
<div class="UI-profile-badge-number-div">
  <div>
    Badge Number
  </div>
  <div class="UI-container">
    <input class="UI-input" disabled id="${prefix}badgeNumber"
    name="badgeNumber" type= "text" value="">
  </div>
</div>
`;

      const emailPrimaryBlock = `
<div class="UI-profile-email-div">
  <div>
    Email
    ${updateButton}
  </div>
  <div class="UI-container">
    <input class="UI-input" id="${prefix}email1" name="email1" placeholder=
    "Email Address (Required)" type="email" value=""
    onchange="userProfile.onChange(this);">

  </div>
</div>
`;

      const emailAllBlock = `
<div class="UI-profile-email-div">
  <div>
    Email
    ${updateButton}
  </div>
  <div class="UI-container">
    <input class="UI-input" id="${prefix}email1" name="email1" placeholder=
    "Primary Email and Login (Required)" type="text" value=""
     onchange="userProfile.onChange(this);"> <input class=
    "UI-input" id="${prefix}email2" name="email2" placeholder="Secondary Email"
    type= "text" value="" onchange="userProfile.onChange(this);">
    <input class="UI-input" id="${prefix}email3" name="email3"
    placeholder="Other Email" type="text" value=""
    onchange="userProfile.onChange(this);">
  </div>
</div>
`;

      const phoneBlock = `
<div class="UI-profile-phone-div">
  <div>
    Phone Numbers
    ${updateButton}
  </div>
  <div class="UI-container">
    <input class="UI-input UI-half" id="${prefix}phone1" name="phone1"
    placeholder="Primary" type="text" value=""
    onchange="userProfile.onChange(this);"> <input class="UI-input UI-half"
    id="${prefix}phone2" name="phone2" placeholder="Other" type="text" value=""
    onchange="userProfile.onChange(this);">
  </div>
</div>
`;

      const concomPhoneBlock = `
<div class="UI-profile-phone-div">
  <div>
    Phone Numbers
    ${updateButton}
  </div>
  <div class="UI-container">
    <input class="UI-input UI-half" id="${prefix}phone1" name="phone1"
    placeholder="Primary" type="text" value=""
    onchange="userProfile.onChange(this);"> <input class="UI-input UI-half"
    id="${prefix}phone2" name="phone2" placeholder="Other" type="text" value=""
    onchange="userProfile.onChange(this);">
    <div class="UI-profile-concom-div">
      <span class="UI-profile-concom-label">Display Phone number on the ConCom
      list?</span> <select class="UI-profile-concom-select" id=
      "${prefix}conComDisplayPhone" name="conComDisplayPhone" required="">
        <option disabled selected value=""
         onchange="userProfile.onChange(this);">
          Choose
        </option>
        <option value="1">
          Yes
        </option>
        <option value="0">
          No
        </option>
      </select>
    </div>
  </div>
</div>
`;

      const addrBlock = `
<div class="UI-profile-address-div">
  <div>
    Address
    ${updateButton}
  </div>
  <div class="UI-container">
    <input class="UI-input" id="${prefix}addressLine1" name="addressLine1"
    placeholder="Address" type="text" value=""
    onchange="userProfile.onChange(this);"> <input class="UI-input"
    id="${prefix}addressLine2" name="addressLine2" type="text" value=""
    onchange="userProfile.onChange(this);"> <input class="UI-input UI-half"
    id="${prefix}city" name="city" placeholder="City"
    type="text" value="" onchange="userProfile.onChange(this);">
    <select class="UI-select UI-quarter" id="${prefix}state" name="state"
    onchange="userProfile.expandOut(this.value, 'otherCountries')">
      <option value="" onchange="userProfile.onChange(this);">
        Foreign (Non-US/Canada)
      </option>
    </select>
    <div class="UI-profile-zip-div">
      <input class="UI-profile-zip-field" id="${prefix}zipCode" name="zipCode"
      placeholder="Zip Code" type="text" value=""
      onchange="userProfile.onChange(this);"> <input class=
      "UI-profile-zip-4-field" id="${prefix}zipPlus4" name="zipPlus4"
      placeholder="Zip+4" type="text" value=""
      onchange="userProfile.onChange(this);">
    </div><select class="UI-select" id="${prefix}countryName"
    name="countryName">
      <option value="" onchange="userProfile.onChange(this);">
        Select Country
      </option>
    </select>
    <div class="UI-hide" id="${prefix}otherCountries">
      <input class="UI-profile-province-field" id="${prefix}province"
      name="province" placeholder="Province (Foreign Only)" type="text"
      value="" onchange="userProfile.onChange(this);">
    </div>
  </div>
</div>
`;

      const passwordBlock = `
<h3>Password Security Information</h3>
<div id="password_form" class="UI-container UI-padding">
  <div class="UI-profile-password-div">
    <div>
     Change Password
    </div>
    <div class="UI-container">
      <div class="UI-third">
        <input type="password" id="${prefix}currentPassword" value=""
         class="UI-input" placeholder="Current Password (Required)">
      </div>
      <div class="UI-third">
        <input type="password" id="${prefix}newPassword" value=""
         class="UI-input" placeholder="New Password(Required)">
      </div>
      <div class="UI-third">
        <input type="password" id="${prefix}againPassword" value=""
         class="UI-input" placeholder="New Password Again(Required)">
      </div>
    </div>
  </div>
  <button class="UI-profile-update-password-button"
   onclick="userProfile.changePassword();">Update Password</button>
</div>
`;

      var tailBlock = '';
      if (settings.updateButtonText) {
        tailBlock += `
<button class="UI-profile-update-button-end"
    onclick="userProfile.updateAccount();">
${settings.updateButtonText}</button>
`;
      }
      tailBlock += `
</div>
`;

      var form = [];

      if (!settings.noTitleBlock) {
        form.push(titleBlock);
      }

      if (settings.panes.includes('emailPrimary')) {
        form.push(emailPrimaryBlock);
      }
      if (settings.panes.includes('emailAll')) {
        form.push(emailAllBlock);
      }
      if (settings.panes.includes('name')) {
        form.push(nameBlock);
      }
      if (settings.panes.includes('prefName')) {
        form.push(prefNameBlock);
      }
      if (settings.panes.includes('badge')) {
        form.push(badgeBlock);
      }
      if (settings.panes.includes('phone')) {
        form.push(phoneBlock);
      }
      if (settings.panes.includes('concomPhone')) {
        form.push(concomPhoneBlock);
      }
      if (settings.panes.includes('addr')) {
        form.push(addrBlock);
      }

      form.push(tailBlock);

      if (settings.panes.includes('password')) {
        form.push(passwordBlock);
      }

      content.insertAdjacentHTML('beforeend', form.join('<p></p>'));

      if (settings.panes.includes('addr')) {
        /* populate states */
        var states = userProfile.getElementById('state');
        STATES.forEach(function(s) {
          var option = document.createElement('OPTION');
          option.value = s.code;
          option.text = s.code + ' - ' + s.name;
          states.add(option);
        });
        states.value = 'MN';
        /* populate countries*/
        var countries = userProfile.getElementById('countryName');
        COUNTRIES.forEach(function(s) {
          var option = document.createElement('OPTION');
          option.value = s;
          option.text = s;
          countries.add(option);
        });
        countries.value = 'United States of America';
      }
    }
  };
}) ();

const STATES =
    [
      {'code' : 'AL', 'name' : 'ALABAMA'},
      {'code' : 'AK', 'name' : 'ALASKA'},
      {'code' : 'AS', 'name' : 'AMERICAN SAMOA'},
      {'code' : 'AZ', 'name' : 'ARIZONA'},
      {'code' : 'AR', 'name' : 'ARKANSAS'},
      {'code' : 'CA', 'name' : 'CALIFORNIA'},
      {'code' : 'CO', 'name' : 'COLORADO'},
      {'code' : 'CT', 'name' : 'CONNECTICUT'},
      {'code' : 'DE', 'name' : 'DELAWARE'},
      {'code' : 'DC', 'name' : 'DISTRICT OF COLUMBIA'},
      {'code' : 'FM', 'name' : 'FEDERATED STATES OF MICRONESIA'},
      {'code' : 'FL', 'name' : 'FLORIDA'},
      {'code' : 'GA', 'name' : 'GEORGIA'},
      {'code' : 'GU', 'name' : 'GUAM GU'},
      {'code' : 'HI', 'name' : 'HAWAII'},
      {'code' : 'ID', 'name' : 'IDAHO'},
      {'code' : 'IL', 'name' : 'ILLINOIS'},
      {'code' : 'IN', 'name' : 'INDIANA'},
      {'code' : 'IA', 'name' : 'IOWA'},
      {'code' : 'KS', 'name' : 'KANSAS'},
      {'code' : 'KY', 'name' : 'KENTUCKY'},
      {'code' : 'LA', 'name' : 'LOUISIANA'},
      {'code' : 'ME', 'name' : 'MAINE'},
      {'code' : 'MH', 'name' : 'MARSHALL ISLANDS'},
      {'code' : 'MD', 'name' : 'MARYLAND'},
      {'code' : 'MA', 'name' : 'MASSACHUSETTS'},
      {'code' : 'MI', 'name' : 'MICHIGAN'},
      {'code' : 'MN', 'name' : 'MINNESOTA'},
      {'code' : 'MS', 'name' : 'MISSISSIPPI'},
      {'code' : 'MO', 'name' : 'MISSOURI'},
      {'code' : 'MT', 'name' : 'MONTANA'},
      {'code' : 'NE', 'name' : 'NEBRASKA'},
      {'code' : 'NV', 'name' : 'NEVADA'},
      {'code' : 'NH', 'name' : 'NEW HAMPSHIRE'},
      {'code' : 'NJ', 'name' : 'NEW JERSEY'},
      {'code' : 'NM', 'name' : 'NEW MEXICO'},
      {'code' : 'NY', 'name' : 'NEW YORK'},
      {'code' : 'NC', 'name' : 'NORTH CAROLINA'},
      {'code' : 'ND', 'name' : 'NORTH DAKOTA'},
      {'code' : 'MP', 'name' : 'NORTHERN MARIANA ISLANDS'},
      {'code' : 'OH', 'name' : 'OHIO'},
      {'code' : 'OK', 'name' : 'OKLAHOMA'},
      {'code' : 'OR', 'name' : 'OREGON'},
      {'code' : 'PW', 'name' : 'PALAU'},
      {'code' : 'PA', 'name' : 'PENNSYLVANIA'},
      {'code' : 'PR', 'name' : 'PUERTO RICO'},
      {'code' : 'RI', 'name' : 'RHODE ISLAND'},
      {'code' : 'SC', 'name' : 'SOUTH CAROLINA'},
      {'code' : 'SD', 'name' : 'SOUTH DAKOTA'},
      {'code' : 'TN', 'name' : 'TENNESSEE'},
      {'code' : 'TX', 'name' : 'TEXAS'},
      {'code' : 'UT', 'name' : 'UTAH'},
      {'code' : 'VT', 'name' : 'VERMONT'},
      {'code' : 'VI', 'name' : 'VIRGIN ISLANDS'},
      {'code' : 'VA', 'name' : 'VIRGINIA'},
      {'code' : 'WA', 'name' : 'WASHINGTON'},
      {'code' : 'WV', 'name' : 'WEST VIRGINIA'},
      {'code' : 'WI', 'name' : 'WISCONSIN'},
      {'code' : 'WY', 'name' : 'WYOMING'},
      {'code' : 'AE',
        'name' : 'ARMED FORCES AFRICA \\ CANADA \\ EUROPE \\ MIDDLE EAST'},
      {'code' : 'AA', 'name' : 'ARMED FORCES AMERICA (EXCEPT CANADA)'},
      {'code' : 'AP', 'name' : 'ARMED FORCES PACIFIC'}
    ];

const COUNTRIES = [
  'United States of America', 'Canada', 'Afghanistan', 'Albania', 'Algeria',
  'American Samoa', 'Andorra', 'Angola', 'Antarctica', 'Antigua and Barbuda',
  'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan',
  'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium',
  'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia (Plurinational State of)',
  'Bosnia and Herzegovina', 'Botswana', 'Bouvet Island', 'Brazil',
  'British Indian Ocean Territory', 'Brunei Darussalam', 'Bulgaria',
  'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Cabo Verde',
  'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China',
  'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo',
  'Cook Islands', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czechia',
  'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Timor-Leste',
  'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia',
  'Ethiopia', 'Falkland Islands [Malvinas]', 'Faroe Islands', 'Fiji', 'Finland',
  'France', 'French Guiana', 'French Southern Territories', 'Gabon', 'Gambia',
  'Georgia', 'Germany', 'Ghana', 'Gibraltar',
  'United Kingdom of Great Britain and Northern Irela', 'Greece', 'Greenland',
  'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea', 'Guinea-Bissau',
  'Guyana', 'Haiti', 'Heard Island and McDonald Islands', 'Honduras',
  'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia',
  'Iran (Islamic Republic of)', 'Iraq', 'Ireland', 'Israel', 'Italy',
  'Côte d\'Ivoire', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya',
  'Kiribati', 'Kuwait', 'Kyrgyzstan', 'Lao People\'s Democratic Republic',
  'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein',
  'Lithuania', 'Luxembourg', 'Macao',
  'Macedonia (the former Yugoslav Republic of)', 'Madagascar', 'Malawi',
  'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique',
  'Mauritania', 'Mauritius', 'Mayotte', 'Mexico',
  'Micronesia (Federated States of)', 'Moldova (the Republic of)',
  'Monaco', 'Mongolia', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar',
  'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Caledonia', 'New Zealand',
  'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island',
  'Korea (the Democratic People\'s Republic of)', 'Northern Mariana Islands',
  'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea',
  'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'French Polynesia',
  'Portugal', 'Puerto Rico', 'Qatar', 'Réunion', 'Romania',
  'Russian Federation', 'Rwanda',
  'South Georgia and the South Sandwich Islands',
  'Saint Helena, Ascension and Tristan da Cunha', 'Saint Kitts and Nevis',
  'Saint Lucia', 'Saint Pierre and Miquelon', 'Sao Tome and Principe',
  'Saint Vincent and the Grenadines', 'Samoa', 'San Marino',
  'Saudi Arabia', 'Senegal', 'Seychelles', 'Sierra Leone', 'Singapore',
  'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa',
  'Korea (the Republic of)', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname',
  'Svalbard and Jan Mayen', 'Swaziland', 'Sweden', 'Switzerland',
  'Syrian Arab Republic', 'Tajikistan', 'Taiwan (Province of China)',
  'Tanzania, United Republic of', 'Thailand', 'Togo', 'Tokelau',
  'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan',
  'Turks and Caicos Islands', 'Tuvalu', 'Uganda', 'Ukraine',
  'United Arab Emirates', 'Uruguay', 'United States Minor Outlying Islands',
  'Uzbekistan', 'Vanuatu', 'Holy See', 'Venezuela (Bolivarian Republic of)',
  'Viet Nam', 'Virgin Islands (British)', 'Virgin Islands (U.S.)',
  'Wallis and Futuna', 'Western Sahara*', 'Yemen',
  'Congo (the Democratic Republic of the)', 'Zambia', 'Zimbabwe', 'Anguilla',
  'South Sudan', 'Sint Maarten (Dutch part)', 'Palestine, State of',
  'Åland Islands', 'Bonaire, Sint Eustatius and Saba', 'Curaçao', 'Guernsey',
  'Isle of Man', 'Jersey', 'Montenegro', 'Saint Barthélemy',
  'Saint Martin (French part)', 'Serbia'
];

if (window.addEventListener) {
  window.addEventListener('load', userProfile.build);
} else {
  window.attachEvent('onload', userProfile.build);
}
